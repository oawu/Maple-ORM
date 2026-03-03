<?php

$local = include PATH . 'Config.local.php';

\Orm\Model::setConfig('', \Orm\Core\Config::create()
  ->setHostname($local['db']['hostname'])
  ->setUsername($local['db']['username'])
  ->setPassword($local['db']['password'])
  ->setDatabase($local['db']['database']));

\Orm\Model::setConfig('R', \Orm\Core\Config::create()
  ->setHostname($local['db_r']['hostname'])
  ->setUsername($local['db_r']['username'])
  ->setPassword($local['db_r']['password'])
  ->setDatabase($local['db_r']['database']));

\Orm\Model::setConfig('W', \Orm\Core\Config::create()
  ->setHostname($local['db_w']['hostname'])
  ->setUsername($local['db_w']['username'])
  ->setPassword($local['db_w']['password'])
  ->setDatabase($local['db_w']['database']));

\Orm\Model::setNamespace('Model');

\Orm\Model::setCaseTable(\Orm\Model::CASE_CAMEL);
\Orm\Model::setCaseColumn(\Orm\Model::CASE_CAMEL);

\Orm\Model::setErrorFunc(static function (...$args) {
  var_dump($args);
  exit();
});

\Orm\Model::setQueryLogFunc(static function (string $db, string $sql, array $vals, bool $status, float $during) {
  // var_dump($db, $sql, $vals, $status, $during);
});
\Orm\Model::setLogFunc(static function (string $message) {
  // var_dump($message);
});

\Orm\Model::setCacheFunc('MetaData', fn(string $key, callable $closure) => $closure());

\Orm\Model::setHashids(8, 'key', 'abcdefghijklmnopqrstuvwxyz1234567890');

\Orm\Model::setImageThumbnail(fn($file) => \Orm\Core\Thumbnail\Gd::create($file));

\Orm\Model::setUploader(static function (Orm\Core\Plugin\Uploader $uploader) use ($local): void {
  $uploader->setDriver('Local', ['storage' => PATH]);
  // 若要使用 S3 驅動，取消下方註解並註解上方 Local 驅動
  // $uploader->setDriver('S3', [
  //   'bucket' => $local['s3']['bucket'],
  //   'access' => $local['s3']['accessKey'],
  //   'secret' => $local['s3']['secretKey'],
  //   'region' => $local['s3']['region'],
  //   'acl'    => 'private',
  //   'ttl'    => 0,
  //   'isUseSSL' => false,
  // ]);

  $uploader->setNamingSort('origin', 'md5', 'random');
  $uploader->setTmpDir(PATH_TMP);
  $uploader->setBaseDir('Storage');
  $uploader->setBaseUrl(BASE_URL);
  $uploader->setDefaultUrl(BASE_URL . '404.png');

  if (!($uploader instanceof \Orm\Core\Plugin\Uploader\Image)) {
    return;
  }

  $uploader->addVersion('w100')->setMethod('resize')->setArgs(100, 100, 'width');
});

define('S3_BUCKET',    $local['s3']['bucket']);
define('S3_REGION',    $local['s3']['region']);
define('S3_ACCESSKEY', $local['s3']['accessKey']);
define('S3_SECRETKEY', $local['s3']['secretKey']);
