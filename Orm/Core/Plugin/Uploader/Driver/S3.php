<?php

namespace Orm\Core\Plugin\Uploader\Driver;

use \Orm\Core\S3 as _S3;
use \Orm\Core\Plugin\Uploader\Driver;

final class S3 extends Driver {
  private _S3 $_s3;
  private string $_acl;
  private int $_cache; // seconds

  public function __construct(array $options = []) {
    $this->_acl = $options['acl'] ?? 'public-read';
    $this->_cache = $options['ttl'] ?? 0;

    $this->_s3 = new _S3([
      'bucket' => $options['bucket'] ?? '',
      'accessKey' => $options['access'] ?? '',
      'secretKey' => $options['secret'] ?? '',
      'region' => $options['region'] ?? '',
      'isUseSSL' => $options['isUseSSL'] ?? false,
    ]);
  }
  public function put(string $source, string $dest): bool {
    $headers = ['x-amz-acl' => $this->_acl];
    if ($this->_cache > 0) {
      $headers['cache-control'] = 'max-age=' . $this->_cache;
    }

    try {
      $this->_s3->putObjectStreaming($source, $dest, $headers);
    } catch (\Throwable $e) {
      return false;
    }
    return true;
  }
  public function delete(string $path): bool {
    try {
      $this->_s3->deleteObject($path);
    } catch (\Throwable $e) {
      return false;
    }
    return true;
  }
  public function saveAs(string $source, string $dest): bool {
    try {
      $this->_s3->copyObject($source, $dest);
    } catch (\Throwable $e) {
      return false;
    }
    return true;
  }
}
