<?php

use \Model\File\User;
use \Orm\Core\Connection;

include '0.php';

$sql = "CREATE TABLE `FileUser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `info1` varchar(50) CHARACTER SET utf8mb4,
  `info2` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($error = Connection::instance()->runQuery($sql)) {
  throw $error;
}

$path1 = PATH_STORAGE . 'avatar2.zip';
$path2 = PATH_STORAGE . 'avatar3.zip';

function resetFile($path1, $path2) {
  @exec('rm -rf ' . PATH_TMP . '*');
  @exec('rm -rf ' . PATH_STORAGE . '*');

  if (!(file_exists($path1) || @copy(PATH_SAMPLE . 'avatar.zip', $path1))) {
    throw new Exception();
  }

  if (!(file_exists($path2) || @copy(PATH_SAMPLE . 'avatar.zip', $path2))) {
    throw new Exception();
  }

  User::truncate();
  return User::create();
}

$u = resetFile($path1, $path2);

if ($u->info1->getValue() !== null) {
  throw new Exception();
}
if ($u->info2->getValue() !== '') {
  throw new Exception();
}

$u->info1 = $path1;
if (User::one()->info1->getValue() !== null) {
  throw new Exception();
}
$u->info2 = $path2;
if (User::one()->info2->getValue() !== '') {
  throw new Exception();
}

if ($u->save() === null) {
  throw new Exception();
}
if (User::one()->info1->getValue() === null) {
  throw new Exception();
}
if (User::one()->info2->getValue() === '') {
  throw new Exception();
}

$u = resetFile($path1, $path2);

if ($u->info1->getValue() !== null) {
  throw new Exception();
}
if ($u->info2->getValue() !== '') {
  throw new Exception();
}

$u->info1 = $path1;
$u->save();
if (User::one()->info1->getValue() === null) {
  throw new Exception();
}

$u->info2 = $path2;
$u->save();
if (User::one()->info2->getValue() === '') {
  throw new Exception();
}

$u = resetFile($path1, $path2);
if ($u->info1->getUrl() !== 'http://dev.orm.ioa.tw/404.png') {
  throw new Exception();
}
if ($u->info2->getUrl() !== 'http://dev.orm.ioa.tw/404.png') {
  throw new Exception();
}

$u->info1 = $path1;
$u->save();
$u->info1 = '';
if (User::one()->info1->getValue() === null) {
  throw new Exception();
}

$u->info2 = $path2;
$u->save();
$u->info2 = '';
if (User::one()->info2->getValue() === '') {
  throw new Exception();
}

$u->info1 = '';
$u->save();
if (User::one()->info1->getValue() !== null) {
  throw new Exception();
}

$u->info2 = '';
$u->save();
if (User::one()->info2->getValue() !== '') {
  throw new Exception();
}
