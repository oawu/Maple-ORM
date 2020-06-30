<?php

// 資料庫連線設定
\M\Model::config()
  ->hostname('127.0.0.1')
  ->username('root')
  ->password('password')
  ->database('database');

// Model 目錄
\M\Model::dir(PATH_MODEL);

// Model 命名規則
\M\Model::case(\M\Model::CASE_CAMEL);

// 紀錄 Query Log
\M\Model::queryLogger(function($sql, $vals, $status, $during) {
  // var_dump($sql, $vals);
});

// Model 錯誤紀錄
\M\Model::logger(function($log) {
  var_dump($log);
});

// 設定 GG
\M\Model::errorFunc(function($message) {
  var_dump($message);
});

// 針對 MetaData 做 Cache
\M\Model::cacheFunc('MetaData', function($key, $closure) {
  return $closure();
});

// 所有上傳器設定
\M\Model::setUploader(function($uploader) {
  // $uploader->driver('S3', [
    // 'bucket' => '',
    // 'access' => '',
    // 'secret' => '',
  // ]);

  $uploader->driver('Local', ['dir' => PATH]);
  $uploader->tmpDir(sys_get_temp_dir() . DIRECTORY_SEPARATOR);
  $uploader->rootDirs('Storage');
  $uploader->baseURL('http://dev.orm.ioa.tw/');
  $uploader->default('http://dev.orm.ioa.tw/404.png');

  if (!($uploader instanceof \M\Core\Plugin\Uploader\Image))
    return;

  $uploader->version('w100', 'resize', [100, 100, 'width'])
           ->version('pad', 'pad', [120, 120, '#001122'])
           ->version('c120x120', 'adaptiveResizeQuadrant', [120, 120, 'c']);
});

// 縮圖程式
\M\Model::thumbnail(function($file) {
  return new Thumbnail\Gd($file);
});


// 施加鹽巴
\M\Model::salt('123');

// 設定 extensions
\M\Model::extensions(['jpg' => ['image/jpeg', 'image/pjpeg'], 'gif' => ['image/gif'], 'png' => ['image/png', 'image/x-png'], 'pdf' => ['application/pdf', 'application/x-download'], 'gz' => ['application/x-gzip'], 'zip' => ['application/x-zip', 'application/zip', 'application/x-zip-compressed'], 'swf' => ['application/x-shockwave-flash'], 'tar' => ['application/x-tar'], 'bz' => ['application/x-bzip'], 'bz2' => ['application/x-bzip2'], 'txt' => ['text/plain'], 'html' => ['text/html'], 'htm' => ['text/html'], 'ico' => ['image/x-icon'], 'css' => ['text/css'], 'js' => ['application/x-javascript'], 'xml' => ['text/xml'], 'ogg' => ['application/ogg'], 'wav' => ['audio/x-wav', 'audio/wave', 'audio/wav'], 'avi' => ['video/x-msvideo'], 'mpg' => ['video/mpeg'], 'mov' => ['video/quicktime'], 'mp3' => ['audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'], 'mpeg' => ['video/mpeg'], 'flv' => ['video/x-flv'], 'php' => ['application/x-httpd-php'], 'bin' => ['application/macbinary'], 'psd' => ['application/x-photoshop'], 'ai' => ['application/postscript'], 'ppt' => ['application/powerpoint', 'application/vnd.ms-powerpoint'], 'wbxml' => ['application/wbxml'], 'tgz' => ['application/x-tar', 'application/x-gzip-compressed'], 'jpeg' => ['image/jpeg', 'image/pjpeg'], 'jpe' => ['image/jpeg', 'image/pjpeg'], 'bmp' => ['image/bmp', 'image/x-windows-bmp'], 'shtml' => ['text/html'], 'text' => ['text/plain'], 'doc' => ['application/msword'], 'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'], 'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'], 'word' => ['application/msword', 'application/octet-stream'], 'json' => ['application/json', 'text/json'], 'svg' => ['image/svg+xml'], 'mp2' => ['audio/mpeg'], 'exe' => ['application/octet-stream', 'application/x-msdownload'], 'tif' => ['image/tiff'], 'tiff' => ['image/tiff'], 'asc' => ['text/plain'], 'xsl' => ['text/xml'], 'hqx' => ['application/mac-binhex40'], 'cpt' => ['application/mac-compactpro'], 'csv' => ['text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'], 'dms' => ['application/octet-stream'], 'lha' => ['application/octet-stream'], 'lzh' => ['application/octet-stream'], 'class' => ['application/octet-stream'], 'so' => ['application/octet-stream'], 'sea' => ['application/octet-stream'], 'dll' => ['application/octet-stream'], 'oda' => ['application/oda'], 'eps' => ['application/postscript'], 'ps' => ['application/postscript'], 'smi' => ['application/smil'], 'smil' => ['application/smil'], 'mif' => ['application/vnd.mif'], 'xls' => ['application/excel', 'application/vnd.ms-excel', 'application/msexcel'], 'wmlc' => ['application/wmlc'], 'dcr' => ['application/x-director'], 'dir' => ['application/x-director'], 'dxr' => ['application/x-director'], 'dvi' => ['application/x-dvi'], 'gtar' => ['application/x-gtar'], 'php4' => ['application/x-httpd-php'], 'php3' => ['application/x-httpd-php'], 'phtml' => ['application/x-httpd-php'], 'phps' => ['application/x-httpd-php-source'], 'sit' => ['application/x-stuffit'], 'xhtml' => ['application/xhtml+xml'], 'xht' => ['application/xhtml+xml'], 'mid' => ['audio/midi'], 'midi' => ['audio/midi'], 'mpga' => ['audio/mpeg'], 'aif' => ['audio/x-aiff'], 'aiff' => ['audio/x-aiff'], 'aifc' => ['audio/x-aiff'], 'ram' => ['audio/x-pn-realaudio'], 'rm' => ['audio/x-pn-realaudio'], 'rpm' => ['audio/x-pn-realaudio-plugin'], 'ra' => ['audio/x-realaudio'], 'rv' => ['video/vnd.rn-realvideo'], 'log' => ['text/plain', 'text/x-log'], 'rtx' => ['text/richtext'], 'rtf' => ['text/rtf'], 'mpe' => ['video/mpeg'], 'qt' => ['video/quicktime'], 'movie' => ['video/x-sgi-movie'], 'xl' => ['application/excel'], 'eml' => ['message/rfc822']]);
