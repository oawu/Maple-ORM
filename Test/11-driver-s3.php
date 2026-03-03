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
  'bucket'    => S3_BUCKET,
  'region'    => S3_REGION,
  'accessKey' => S3_ACCESSKEY,
  'secretKey' => S3_SECRETKEY,
]);

$headersList = ['default' => [], 'options' => ['x-amz-acl' => 'public-read', 'Cache-Control' => 'max-age=60']];

foreach ($headersList as $title => $headers) {
  title("{$title}: putObjectStreaming（small）");
  $result = $uploader->putObjectStreaming($src1, $dest . $title . '/putObjectStreaming-small.jpg', $headers);
  check(is_array($result) && isset($result['headers']));

  title("{$title}: putObjectMultipart（small）");
  $result = $uploader->putObjectMultipart($src1, $dest . $title . '/putObjectMultipart-small.jpg', $headers);
  check(is_array($result) && isset($result['headers']));

  title("{$title}: putObject（small）");
  $result = $uploader->putObject($src1, $dest . $title . '/putObject-small.jpg', $headers);
  check(is_array($result) && isset($result['headers']));

  title("{$title}: putObjectStreaming（large）");
  $result = $uploader->putObjectStreaming($src2, $dest . $title . '/putObjectStreaming-large.png', $headers);
  check(is_array($result) && isset($result['headers']));

  title("{$title}: putObjectMultipart（large）");
  $result = $uploader->putObjectMultipart($src2, $dest . $title . '/putObjectMultipart-large.png', $headers);
  check(is_array($result) && isset($result['headers']));

  title("{$title}: putObject（large）");
  $result = $uploader->putObject($src2, $dest . $title . '/putObject-large.png', $headers);
  check(is_array($result) && isset($result['headers']));

  title("{$title}: copyObject");
  $result = $uploader->copyObject($dest . $title . '/putObject-large.png', $dest . $title . '/putObject-large-copy.png');
  check(is_array($result) && isset($result['headers']));

  title("{$title}: deleteObject");
  $result = $uploader->deleteObject($dest . $title . '/putObject-large-copy.png');
  check(is_array($result) && isset($result['headers']));
}
