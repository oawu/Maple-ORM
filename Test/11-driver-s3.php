<?php

use \Orm\Core\S3;

@exec('rm -rf ' . PATH_TMP . '*');
@exec('rm -rf ' . PATH_STORAGE . '*');

$src1 = PATH_STORAGE . 'avatar.jpg';
$src2 = PATH_STORAGE . 'avatar.png';

$dest = '/test/';

if (!(file_exists($src1) || @copy(PATH_SAMPLE . 'avatar.jpg', $src1))) {
  throw new Exception();
}
if (!(file_exists($src2) || @copy(PATH_SAMPLE . 'avatar.png', $src2))) {
  throw new Exception();
}

$uploader = new S3([
  'bucket' => S3_BUCKET,
  'region' => S3_REGION,
  'accessKey' => S3_ACCESSKEY,
  'secretKey' => S3_SECRETKEY,
]);

$headersList = ['default' => [], 'options' => ['x-amz-acl' => 'public-read', 'Cache-Control' => 'max-age=60']];

foreach ($headersList as $title => $headers) {
  echo '' . $title . "\n";

  echo '  putObjectStreaming - small file';
  $s = microtime(true);
  $result = $uploader->putObjectStreaming($src1, $dest . $title . '/putObjectStreaming-small.jpg', $headers);
  echo ' - ' . (microtime(true) - $s) . ' - ok' . "\n";

  echo '  putObjectMultipart - small file';
  $s = microtime(true);
  $result = $uploader->putObjectMultipart($src1, $dest . $title . '/putObjectMultipart-small.jpg', $headers);
  echo ' - ' . (microtime(true) - $s) . ' - ok' . "\n";

  echo '  putObject          - small file';
  $s = microtime(true);
  $result = $uploader->putObject($src1, $dest . $title . '/putObject-small.jpg', $headers);
  echo ' - ' . (microtime(true) - $s) . ' - ok' . "\n";



  echo '  putObjectStreaming - large file';
  $s = microtime(true);
  $result = $uploader->putObjectStreaming($src2, $dest . $title . '/putObjectStreaming-large.png', $headers);
  echo ' - ' . (microtime(true) - $s) . ' - ok' . "\n";

  echo '  putObjectMultipart - large file';
  $s = microtime(true);
  $result = $uploader->putObjectMultipart($src2, $dest . $title . '/putObjectMultipart-large.png', $headers);
  echo ' - ' . (microtime(true) - $s) . ' - ok' . "\n";

  echo '  putObject          - large file';
  $s = microtime(true);
  $result = $uploader->putObject($src2, $dest . $title . '/putObject-large.png', $headers);
  echo ' - ' . (microtime(true) - $s) . ' - ok' . "\n";



  echo '  copyObject';
  $s = microtime(true);
  $result = $uploader->copyObject($dest . $title . '/putObject-large.png', $dest . $title . '/putObject-large-copy.png');
  echo ' - ' . (microtime(true) - $s) . ' - ok' . "\n";

  echo '  deleteObject';
  $s = microtime(true);
  $result = $uploader->deleteObject($dest . $title . '/putObject-large-copy.png');
  echo ' - ' . (microtime(true) - $s) . ' - ok' . "\n";

  echo "\n";
}
