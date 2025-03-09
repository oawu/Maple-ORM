<?php

\Orm\Model::setConfig('', \Orm\Core\Config::create()
  ->setHostname('db-mysql-8.3')
  ->setUsername('root')
  ->setPassword('1234')
  ->setDatabase('maple-orm'));

\Orm\Model::setConfig('R', \Orm\Core\Config::create()
  ->setHostname('db-mysql-8.3')
  ->setUsername('root')
  ->setPassword('1234')
  ->setDatabase('maple-orm-R'));

\Orm\Model::setConfig('W', \Orm\Core\Config::create()
  ->setHostname('db-mysql-8.3')
  ->setUsername('root')
  ->setPassword('1234')
  ->setDatabase('maple-orm-W'));

\Orm\Model::setNamespace('Model');

\Orm\Model::setCaseTable(\Orm\Model::CASE_CAMEL);
\Orm\Model::setCaseColumn(\Orm\Model::CASE_CAMEL);

\Orm\Model::setErrorFunc(static function (...$args) {
  var_dump($args);
  exit();
});

\Orm\Model::setQueryLogFunc(static function (string $db, string $sql, array $vals, bool $status, float $during) {
  var_dump($db, $sql, $vals, $status, $during);
});
\Orm\Model::setLogFunc(static function (string $message) {
  // var_dump($message);
});

\Orm\Model::setCacheFunc('MetaData', fn(string $key, callable $closure) => $closure());

\Orm\Model::setHashids(8, 'key', 'abcdefghijklmnopqrstuvwxyz1234567890');

\Orm\Model::setImageThumbnail(fn($file) => \Orm\Core\Thumbnail\Gd::create($file));

\Orm\Model::setUploader(static function (Orm\Core\Plugin\Uploader $uploader): void {
  $uploader->setDriver('Local', ['storage' => PATH]);
  // $uploader->setDriver('S3', [
  //   'bucket' => '',
  //   'access' => '',
  //   'secret' => '',
  //   'region' => 'ap-northeast-1',
  //   'acl' => 'private',
  //   'ttl' => 0,
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
