<?php

namespace Orm\Core;

final class S3 {
  // 產出簽名金鑰
  private static function _signingKey(string ...$data): string {
    $_data = null;
    foreach ($data as $item) {
      if ($_data === null) {
        $_data = $item;
      } else {
        $_data = hash_hmac('sha256', $item, $_data, true);
      }
    }
    return $_data;
  }
  // 產出授權標頭
  private static function _authorizationHeader(string $accessKey, string $scope, string $signedHeaders, string $signature): string {
    return implode(' ', [
      'AWS4-HMAC-SHA256',
      implode(',', [
        'Credential=' . $accessKey . '/' . $scope,
        'SignedHeaders=' . $signedHeaders,
        'Signature=' . $signature
      ])
    ]);
  }
  // 處理 CURL 標頭
  private static function _curlHeader(string $authorizationHeader, array $_headers): array {
    $headers = [
      'Authorization: ' . $authorizationHeader,
    ];
    foreach ($_headers as $header) {
      $headers[] = ucwords($header['key'], '-') . ': ' . $header['value'];
    }
    return $headers;
  }
  // 處理標頭
  private static function _header(array ...$headersList): array {
    $_headers = [];

    foreach ($headersList as $headers) {
      foreach ($headers as $key => $value) {
        $_headers[] = ['key' => strtolower(trim($key)), 'value' => trim($value)];
      }
    }

    usort($_headers, fn($a, $b) => strcasecmp($a['key'], $b['key']));
    return $_headers;
  }
  // 處理回應
  private static function _response($code, $response, $size, $errno, $error): array {
    $return = [
      'status' => false,
      'code' => $code,
      'response' => $response,
      'data' => null,
      'error' => null,
    ];

    if ($errno) {
      $return['error'] = $error;
      return $return;
    }

    if ($code >= 400) {
      $return['error'] = 'Status Code Error';
      return $return;
    }

    $headers = array_change_key_case(self::_parseHeaders(substr($response, 0, $size)));
    $body = substr($response, $size);
    $json = null;

    if (strpos($body, '<?xml') !== false) {
      $object = @simplexml_load_string($body);

      if ($object !== false) {
        $rootName = $object->getName();
        $json = json_decode(json_encode([$rootName => $object], JSON_UNESCAPED_UNICODE), true);
      }
    }

    $return['status'] = true;
    $return['data']['json'] = $json;
    $return['data']['headers'] = $headers;
    $return['data']['body'] = $body;
    return $return;
  }
  // 編碼 URI
  private static function _uriEncode(string $value, bool $path): string {
    if ($value === null) {
      return '';
    }

    $encoded = rawurlencode($value);
    $encoded = preg_replace_callback('/%[a-f0-9]{2}/', fn($matches) => strtoupper($matches[0]), $encoded);
    $replacements = ['*' => '%2A', '~' => '~'];

    if ($path) { // 路徑模式下，path 中的 "/" 不 encode
      $replacements['%2F'] = '/';
    }

    return strtr($encoded, $replacements);
  }
  // 解析標頭
  private static function _parseHeaders(string $headerContent): array {
    $headers = [];
    $lines = explode("\r\n", trim($headerContent));

    foreach ($lines as $line) {
      if (strpos($line, ':') !== false) {
        [$key, $value] = explode(':', $line, 2);
        $headers[trim($key)] = trim($value);
      } else { // 這是 status line (HTTP/1.1 200 OK)
        $headers['Status'] = $line;
      }
    }
    return $headers;
  }
  // 將陣列轉換為 XML
  private static function _arrayToXml(array $data, string $rootElement = 'root'): string {
    $xml = new \SimpleXMLElement("<$rootElement/>");
    self::_arrayToXmlRecursive($data, $xml);
    return $xml->asXML();
  }
  // 判斷陣列是否為索引陣列
  private static function _arrayIsList(array $arr): bool {
    if ($arr === []) {
      return true;
    }
    return array_keys($arr) === range(0, count($arr) - 1);
  }
  // 遞迴將陣列轉換為 XML
  private static function _arrayToXmlRecursive(array $data, \SimpleXMLElement $xml): void {
    if (self::_arrayIsList($data)) {
      foreach ($data as $value) {
        if (is_array($value)) {
          self::_arrayToXmlRecursive($value, $xml);
        } else {
          $xml->addChild(htmlspecialchars((string) $value));
        }
      }
      return;
    }

    foreach ($data as $key => $value) {
      if (is_array($value)) {
        $subNode = $xml->addChild($key);
        self::_arrayToXmlRecursive($value, $subNode);
      } else {
        $xml->addChild($key, htmlspecialchars((string) $value));
      }
    }
  }

  private string $_bucket = '';
  private string $_region = '';
  private string $_accessKey = '';
  private string $_secretKey = '';
  private bool $_isUseSSL = false;

  public function __construct(array $options) {
    $this->_bucket = $options['bucket'] ?? '';
    $this->_region = $options['region'] ?? '';
    $this->_accessKey = $options['accessKey'] ?? '';
    $this->_secretKey = $options['secretKey'] ?? '';

    $this->_isUseSSL = extension_loaded('openssl') && ($options['isUseSSL'] ?? false);
  }
  // 上傳檔案
  // https://docs.aws.amazon.com/zh_tw/AmazonS3/latest/API/API_PutObject.html
  public function putObject(string $filePath, string $s3Path, array $headers = []): array {
    $fileContent = file_get_contents($filePath);
    $contentType = mime_content_type($filePath) ?: 'application/octet-stream';
    $contentMd5 = base64_encode(md5_file($filePath, true));
    $contentLength = strlen($fileContent);

    $_headers = [
      'content-type' => $contentType,
      'content-md5' => $contentMd5,
      'content-length' => $contentLength,
    ];

    foreach ($headers as $key => $value) {
      $key = strtolower($key);
      if (in_array($key, ['x-amz-acl', 'cache-control'])) {
        $_headers[$key] = $value;
      }
      if (strpos($key, 'x-amz-meta-') === 0) {
        $_headers[$key] = $value;
      }
    }

    $result = $this->_sendRequest('PUT', $s3Path, [], $_headers, $fileContent);
    if ($result['status'] !== true) {
      throw new \Exception('回應錯誤');
    }

    return $result['data'];
  }
  // 上傳檔案 (Chunked)
  // https://docs.aws.amazon.com/zh_tw/AmazonS3/latest/API/sigv4-streaming.html
  public function putObjectStreaming(string $filePath, string $s3Path, array $headers = []) {
    $result = $this->_putObjectStreaming($filePath, $s3Path, $headers);
    if ($result['status'] !== true) {
      throw new \Exception('回應錯誤');
    }

    return $result['data'];
  }
  // copy object
  // https://docs.aws.amazon.com/zh_tw/AmazonS3/latest/API/API_CopyObject.html
  public function copyObject(string $sourcePath, string $destinationPath, array $destinationHeaders = []): array {
    return $this->_copyObject($this->_bucket, $sourcePath, $destinationPath, $destinationHeaders);
  }
  // 刪除檔案
  // https://docs.aws.amazon.com/zh_tw/AmazonS3/latest/API/API_DeleteObject.html
  public function deleteObject(string $s3Path): array {
    $result = $this->_sendRequest('DELETE', $s3Path, [], [], '');
    if ($result['status'] !== true) {
      throw new \Exception('回應錯誤');
    }

    return $result['data'];
  }
  // 上傳檔案 (Multipart)
  // https://docs.aws.amazon.com/zh_tw/AmazonS3/latest/API/API_AbortMultipartUpload.html
  // https://docs.aws.amazon.com/zh_tw/AmazonS3/latest/API/API_CreateMultipartUpload.html
  // https://docs.aws.amazon.com/zh_tw/AmazonS3/latest/API/API_CompleteMultipartUpload.html
  public function putObjectMultipart(string $filePath, string $s3Path, array $headers = [], int $chunkSize = 5 * 1024 * 1024, int $maxRetries = 3): array {
    $contentType = mime_content_type($filePath) ?: 'application/octet-stream';
    $_headers = [
      'content-type' => $contentType,
    ];

    foreach ($headers as $key => $value) {
      $key = strtolower($key);
      if (in_array($key, ['content-md5', 'content-length'])) {
        continue;
      }
      if (in_array($key, ['x-amz-acl', 'cache-control'])) {
        $_headers[$key] = $value;
      }
      if (strpos($key, 'x-amz-meta-') === 0) {
        $_headers[$key] = $value;
      }
    }

    $uploadId = $this->_initiateMultipartUpload($s3Path, $_headers);
    $parts = $this->_uploadParts($filePath, $s3Path, $uploadId, $chunkSize, $maxRetries);
    return $this->_completeMultipartUpload($s3Path, $uploadId, $parts);
  }

  // 上傳檔案 CURL (Chunked)
  private function _putObjectStreaming(string $filePath, string $s3Path, array $headers = []) {
    $contentType = mime_content_type($filePath) ?: 'application/octet-stream';

    $method = 'PUT';
    $region = $this->_region;
    $bucket = $this->_bucket;
    $secretKey = $this->_secretKey;
    $accessKey = $this->_accessKey;

    $date = gmdate('Ymd');
    $dateTime = gmdate('Ymd\THis\Z');

    $service = 's3';
    $host = $service . '.' . $region . '.amazonaws.com';
    $path = '/' . $bucket . '/' . implode('/', array_map('rawurlencode', explode('/', trim($s3Path, '/'))));
    $scope = implode('/', [$date, $region, $service, 'aws4_request']);

    $xAmzDecodedContentLength = filesize($filePath);
    $body = 'STREAMING-AWS4-HMAC-SHA256-PAYLOAD';
    $_headers = self::_header([
      'host' => $host,
      'x-amz-content-sha256' => $body,
      'x-amz-date' => $dateTime,
      'x-amz-decoded-content-length' => $xAmzDecodedContentLength,
      'content-type' => $contentType,
    ], $headers);

    $signedHeaders = implode(';', array_column($_headers, 'key'));

    $canonicalRequest = implode("\n", [
      $method,
      $path,
      '',
      ...array_map(fn($item) => $item['key'] . ':' . $item['value'], $_headers),
      '',
      $signedHeaders,
      $body
    ]);
    $canonicalRequestHash = hash('sha256', $canonicalRequest);

    $stringToSign = implode("\n", [
      'AWS4-HMAC-SHA256',
      $dateTime,
      $scope,
      $canonicalRequestHash
    ]);

    $signingKey = self::_signingKey('AWS4' . $secretKey, $date, $region, $service, 'aws4_request');
    $signature = hash_hmac('sha256', $stringToSign, $signingKey);
    $authorizationHeader = self::_authorizationHeader($accessKey, $scope, $signedHeaders, $signature);
    $headers = self::_curlHeader($authorizationHeader, $_headers);

    $url = ($this->_isUseSSL ? 'https' : 'http') . '://' . $host . $path;

    $func = static function ($ch, $fp, $length) use (&$signature, $signingKey, $dateTime, $scope): string {
      static $sentFinalChunk = false;

      if ($sentFinalChunk) {
        return '';
      }

      $buffer = strlen(dechex($length)) + strlen(';chunk-signature=') + strlen("\r\n") * 2 + strlen($signature);
      $chunk = fread($fp, $length - $buffer);

      $empty = hash('sha256', '');

      if ($chunk === false) {
        throw new \Exception("fread() failed");
      }

      if (strlen($chunk) === 0) {
        $stringToSign = implode("\n", ['AWS4-HMAC-SHA256-PAYLOAD', $dateTime, $scope, $signature, $empty, $empty]);
        $signature = hash_hmac('sha256', $stringToSign, $signingKey);

        $sentFinalChunk = true;
        return "0;chunk-signature={$signature}\r\n\r\n";
      }

      $stringToSign = implode("\n", ['AWS4-HMAC-SHA256-PAYLOAD', $dateTime, $scope, $signature, $empty, hash('sha256', $chunk)]);
      $signature = hash_hmac('sha256', $stringToSign, $signingKey);
      return sprintf("%x;chunk-signature=%s\r\n%s\r\n", strlen($chunk), $signature, $chunk);
    };

    $fileHandle = fopen($filePath, 'rb');

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_HEADER => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => false,

      CURLOPT_INFILE => $fileHandle,
      CURLOPT_PUT => true,
      CURLOPT_UPLOAD => true,
      CURLOPT_READFUNCTION => $func,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);

    fclose($fileHandle);

    return self::_response($code, $response, $size, $errno, $error);
  }
  // 初始化 Multipart Upload
  private function _initiateMultipartUpload(string $s3Path, array $headers = []): string {

    $result = $this->_sendRequest('POST', $s3Path, ['uploads' => ''], $headers, '');
    if ($result['status'] !== true) {
      throw new \Exception('回應錯誤');
    }

    $json = $result['data']['json'];

    if ($json === null) {
      throw new \Exception('初始化 Multipart Upload 失敗(1)');
    }

    if (!(isset($json['InitiateMultipartUploadResult']) && isset($json['InitiateMultipartUploadResult']['UploadId']) && is_string($json['InitiateMultipartUploadResult']['UploadId']) && $json['InitiateMultipartUploadResult']['UploadId'] !== '')) {
      throw new \Exception('初始化 Multipart Upload 失敗(2)');
    }

    return $json['InitiateMultipartUploadResult']['UploadId'];
  }
  // 上傳 Part
  private function _uploadParts(string $filePath, string $s3Path, string $uploadId, int $chunkSize, int $maxRetries): array {
    $handle = fopen($filePath, 'rb');
    $partNumber = 1;
    $parts = [];

    while (!feof($handle)) {
      $chunk = fread($handle, $chunkSize);

      $etag = '';
      try {
        $etag = $this->_uploadSinglePart($s3Path, $uploadId, $partNumber, $chunk, $maxRetries);
      } catch (\Throwable $_) {
        $etag = '';
      }

      if ($etag === '') {
        fclose($handle);
        throw new \Exception('上傳 Part - ' . $partNumber . '失敗');
      }

      $parts[] = ['PartNumber' => $partNumber, 'ETag' => $etag];
      $partNumber += 1;
    }

    fclose($handle);
    return $parts;
  }
  // 上傳單一 Part
  private function _uploadSinglePart(string $s3Path, string $uploadId, int $partNumber, string $chunk, int $maxRetries): string {
    for ($retry = 0; $retry < $maxRetries; $retry++) {
      $result = $this->_sendRequest('PUT', $s3Path, ['partNumber' => $partNumber, 'uploadId' => $uploadId], [], $chunk);

      if ($result['status'] !== true) {
        throw new \Exception('回應錯誤');
      }

      if (isset($result['data']['headers']['etag'])) {
        return trim($result['data']['headers']['etag'], '"');
      }

      sleep(2 ** $retry); // 指數等待
    }
    return '';
  }
  // 完成 Multipart Upload
  private function _completeMultipartUpload(string $s3Path, string $uploadId, array $parts): array {
    $xml = preg_replace('/^<\?xml.*?\?>\n?/i', '', self::_arrayToXml(array_map(fn($part) => [
      'Part' => [
        'PartNumber' => $part['PartNumber'],
        'ETag' => '"' . $part['ETag'] . '"'
      ]
    ], $parts), 'CompleteMultipartUpload'));

    $result = $this->_sendRequest('POST', $s3Path, ['uploadId' => $uploadId], ['Content-Type' => 'application/xml'], $xml);
    if ($result['status'] !== true) {
      throw new \Exception('回應錯誤');
    }

    $json = $result['data']['json'];

    if ($json === null || !isset($json['CompleteMultipartUploadResult'])) {
      throw new \Exception('完成 Multipart Upload 失敗');
    }

    return $result['data'];
  }
  // Access Key/Secret 必須：
  // 對來源 Bucket 有「讀取權限 (s3:GetObject)」。
  // 對目標 Bucket 有「寫入權限 (s3:PutObject)」。
  private function _copyObject(string $sourceBucket, string $sourcePath, string $destinationPath, array $destinationHeaders = []): array {
    $_headers = [
      'x-amz-copy-source' => '/' . $sourceBucket . '/' . implode('/', array_map('rawurlencode', explode('/', trim($sourcePath, '/')))),
    ];

    foreach ($destinationHeaders as $key => $value) {
      $key = strtolower($key);
      if (in_array($key, ['x-amz-acl', 'cache-control'])) {
        $_headers[$key] = $value;
      }
      if (strpos($key, 'x-amz-meta-') === 0) {
        $_headers[$key] = $value;
      }
    }

    $result = $this->_sendRequest('PUT', $destinationPath, [], $_headers, '');

    if ($result['status'] !== true) {
      throw new \Exception('回應錯誤');
    }

    return $result['data'];
  }
  // 發送請求
  private function _sendRequest(string $method, string $s3Path, array $queries, array $headers, string $body): array {
    $region = $this->_region;
    $bucket = $this->_bucket;
    $secretKey = $this->_secretKey;
    $accessKey = $this->_accessKey;

    $date = gmdate('Ymd');
    $dateTime = gmdate('Ymd\THis\Z');

    $service = 's3';
    $host = $service . '.' . $region . '.amazonaws.com';
    $path = '/' . $bucket . '/' . implode('/', array_map('rawurlencode', explode('/', trim($s3Path, '/'))));
    $scope = implode('/', [$date, $region, $service, 'aws4_request']);

    $payloadHash = hash('sha256', $body);

    $_headers = self::_header([
      'host' => $host,
      'x-amz-content-sha256' => $payloadHash,
      'x-amz-date' => $dateTime,
    ], $headers);
    $signedHeaders = implode(';', array_column($_headers, 'key'));

    $_queries = [];
    foreach ($queries as $key => $value) {
      $_queries[] = ['key' => self::_uriEncode($key, false), 'value' => self::_uriEncode($value, false)];
    }
    $canonicalRequest = implode("\n", [
      $method,
      $path,
      implode('&', array_map(fn($item) => $item['key'] . '=' . $item['value'], $_queries)),
      ...array_map(fn($item) => $item['key'] . ':' . $item['value'], $_headers),
      '',
      $signedHeaders,
      $payloadHash
    ]);

    $canonicalRequestHash = hash('sha256', $canonicalRequest);

    $stringToSign = implode("\n", [
      'AWS4-HMAC-SHA256',
      $dateTime,
      $scope,
      $canonicalRequestHash
    ]);

    $signingKey = self::_signingKey('AWS4' . $secretKey, $date, $region, $service, 'aws4_request');
    $signature = hash_hmac('sha256', $stringToSign, $signingKey);
    $authorizationHeader = self::_authorizationHeader($accessKey, $scope, $signedHeaders, $signature);
    $headers = self::_curlHeader($authorizationHeader, $_headers);

    $queries = http_build_query($queries);
    $url = ($this->_isUseSSL ? 'https' : 'http') . '://' . $host . $path . ($queries ? '?' . $queries : '');

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_HEADER => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => false,
    ];

    if ($this->_isUseSSL) {
      $options[CURLOPT_SSL_VERIFYHOST] = 2;
      $options[CURLOPT_SSL_VERIFYPEER] = true;
    }

    if ($method === 'PUT' || $method === 'POST') {
      $options[CURLOPT_POSTFIELDS] = $body;
    }

    $ch = curl_init();
    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);

    return self::_response($code, $response, $size, $errno, $error);
  }
}
