<?php

\M\Model::setConfig('', \M\Core\Config::create()
  ->setHostname('db-mysql-8.3')
  ->setUsername('root')
  ->setPassword('1234')
  ->setDatabase('maple-orm'));

\M\Model::setConfig('R', \M\Core\Config::create()
  ->setHostname('db-mysql-8.3')
  ->setUsername('root')
  ->setPassword('1234')
  ->setDatabase('maple-orm-R'));

\M\Model::setConfig('W', \M\Core\Config::create()
  ->setHostname('db-mysql-8.3')
  ->setUsername('root')
  ->setPassword('1234')
  ->setDatabase('maple-orm-W'));

\M\Model::setDir('Model', PATH_MODEL);

\M\Model::setCaseTable(\M\Model::CASE_CAMEL);
\M\Model::setCaseColumn(\M\Model::CASE_CAMEL);

\M\Model::setErrorFunc(static function(...$args) {
  var_dump($args);
  exit();
});

\M\Model::setQueryLogFunc(static function(string $db, string $sql, array $vals, bool $status, float $during) {
  // var_dump($db, $sql, $vals, $status, $during);
});
\M\Model::setLogFunc(static function(string $message) {
  // var_dump($message);
});

\M\Model::setCacheFunc('MetaData', fn (string $key, callable $closure) => $closure());

\M\Model::setHashids(8, 'key', 'abcdefghijklmnopqrstuvwxyz1234567890');

\M\Model::setImageThumbnail(fn ($file) => \M\Core\Thumbnail\Gd::create($file));

\M\Model::setUploader(static function(M\Core\Plugin\Uploader $uploader): void {
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

  if (!($uploader instanceof \M\Core\Plugin\Uploader\Image)) {
    return;
  }

  $uploader->addVersion('w100')->setMethod('resize')->setArgs(100, 100, 'width');
});