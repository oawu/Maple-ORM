<?php

use \Model\Image\User;
use \Orm\Core\Connection;

include '0.php';

$sql = "CREATE TABLE `ImageUser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `avatar1` varchar(50) CHARACTER SET utf8mb4,
  `avatar2` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($error = Connection::instance()->runQuery($sql)) {
  throw $error;
}

$path1 = PATH_STORAGE . 'avatar2.jpg';
$path2 = PATH_STORAGE . 'avatar3.jpg';

function resetImage($path1, $path2) {
  @exec('rm -rf ' . PATH_TMP . '*');
  @exec('rm -rf ' . PATH_STORAGE . '*');

  if (!(file_exists($path1) || @copy(PATH_SAMPLE . 'avatar.jpg', $path1))) {
    throw new Exception();
  }

  if (!(file_exists($path2) || @copy(PATH_SAMPLE . 'avatar.jpg', $path2))) {
    throw new Exception();
  }

  User::truncate();
  return User::create();
}

$u = resetImage($path1, $path2);

if ($u->avatar1->getValue() !== null) {
  throw new Exception();
}
if ($u->avatar2->getValue() !== '') {
  throw new Exception();
}

$u->avatar1 = $path1;
if (User::one()->avatar1->getValue() !== null) {
  throw new Exception();
}

$u->avatar2 = $path2;
if (User::one()->avatar2->getValue() !== '') {
  throw new Exception();
}

if ($u->save() === null) {
  throw new Exception();
}

if (User::one()->avatar1->getValue() === null) {
  throw new Exception();
}
if (User::one()->avatar2->getValue() === '') {
  throw new Exception();
}

$u = resetImage($path1, $path2);

if ($u->avatar1->getValue() !== null) {
  throw new Exception();
}
if ($u->avatar2->getValue() !== '') {
  throw new Exception();
}

$u->avatar1 = $path1;
$u->save();
if (User::one()->avatar1->getValue() === null) {
  throw new Exception();
}

$u->avatar2 = $path2;
$u->save();
if (User::one()->avatar2->getValue() === '') {
  throw new Exception();
}

$u = resetImage($path1, $path2);

if ($u->avatar1->getUrl() !== 'http://dev.orm.ioa.tw/404.png') {
  throw new Exception();
}
if ($u->avatar2->getUrl() !== 'http://dev.orm.ioa.tw/404.png') {
  throw new Exception();
}
if (array_keys($u->avatar1->getVersions()) !== ['w100', 'rotate']) {
  throw new Exception();
}
if (array_keys($u->avatar2->getVersions()) !== ['w100']) {
  throw new Exception();
}


$u->avatar1 = $path1;
$u->save();

$u->avatar1 = '';

if (User::one()->avatar1->getValue() === null) {
  throw new Exception();
}

$u->avatar2 = $path2;
$u->save();

$u->avatar2 = '';

if (User::one()->avatar2->getValue() === '') {
  throw new Exception();
}

$u->avatar1 = '';
$u->save();

if (User::one()->avatar1->getValue() !== null) {
  throw new Exception();
}

$u->avatar2 = '';
$u->save();
if (User::one()->avatar2->getValue() !== '') {
  throw new Exception();
}
