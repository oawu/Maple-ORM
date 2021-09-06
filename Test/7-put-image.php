<?php

include '0.php';

$sql = "CREATE TABLE `ImageUser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `avatar1` varchar(50) CHARACTER SET utf8mb4,
  `avatar2` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($error = \M\Core\Connection::instance()->query($sql)) throw new Exception($error);

$path1 = PATH_SAMPLE . 'avatar2.jpg';
$path2 = PATH_SAMPLE . 'avatar3.jpg';

function resetImage($path1, $path2) {
  @exec('rm -rf ' . PATH . 'Storage/*');

  if (!(file_exists($path1) || @copy(PATH_SAMPLE . 'avatar.jpg', $path1)))
    throw new Exception();
  
  if (!(file_exists($path2) || @copy(PATH_SAMPLE . 'avatar.jpg', $path2)))
    throw new Exception();

  \M\ImageUser::truncate();
  return \M\ImageUser::create();
}

$u = resetImage($path1, $path2);

if ($u->avatar1->value !== null) throw new Exception();
if (!$u->avatar1->isNull()) throw new Exception();
if (!$u->avatar1->isEmpty()) throw new Exception();
if ($u->avatar2->value !== '') throw new Exception();
if (!$u->avatar2->isEmpty()) throw new Exception();

if ($u->avatar1->put($path1) !== true) throw new Exception();
if (\M\ImageUser::one()->avatar1->value !== null) throw new Exception();
if (!\M\ImageUser::one()->avatar1->isNull()) throw new Exception();
if (!\M\ImageUser::one()->avatar1->isEmpty()) throw new Exception();
if ($u->avatar2->put($path2) !== true) throw new Exception();
if (\M\ImageUser::one()->avatar2->value !== '') throw new Exception();
if (!\M\ImageUser::one()->avatar2->isEmpty()) throw new Exception();

if ($u->save() === null) throw new Exception();
if (\M\ImageUser::one()->avatar1->value === null) throw new Exception();
if (\M\ImageUser::one()->avatar1->isNull()) throw new Exception();
if (\M\ImageUser::one()->avatar1->isEmpty()) throw new Exception();
if (\M\ImageUser::one()->avatar2->value === '') throw new Exception();
if (\M\ImageUser::one()->avatar2->isEmpty()) throw new Exception();

$u = resetImage($path1, $path2);

if ($u->avatar1->value !== null) throw new Exception();
if (!$u->avatar1->isNull()) throw new Exception();
if (!$u->avatar1->isEmpty()) throw new Exception();
if ($u->avatar2->value !== '') throw new Exception();
if (!$u->avatar2->isEmpty()) throw new Exception();

if ($u->avatar1->put($path1, true) !== $u) throw new Exception();
if (\M\ImageUser::one()->avatar1->value === null) throw new Exception();
if (\M\ImageUser::one()->avatar1->isNull()) throw new Exception();
if (\M\ImageUser::one()->avatar1->isEmpty()) throw new Exception();

if ($u->avatar2->put($path2, true) !== $u) throw new Exception();
if (\M\ImageUser::one()->avatar2->value === '') throw new Exception();
if (\M\ImageUser::one()->avatar2->isEmpty()) throw new Exception();

$u = resetImage($path1, $path2);
if ($u->avatar1->url() !== 'http://dev.orm.ioa.tw/404.png') throw new Exception();
if ($u->avatar2->url() !== 'http://dev.orm.ioa.tw/404.png') throw new Exception();
if (array_keys($u->avatar1->versions()) !== ['w100', 'pad', 'c120x120', 'rotate']) throw new Exception();
if (array_keys($u->avatar2->versions()) !== ['w100', 'pad', 'c120x120']) throw new Exception();
if ($u->avatar1->put($path1, true) !== $u) throw new Exception();
if ($u->avatar1->clean() !== true) throw new Exception();
if (\M\ImageUser::one()->avatar1->value === null) throw new Exception();
if (\M\ImageUser::one()->avatar1->isNull()) throw new Exception();
if (\M\ImageUser::one()->avatar1->isEmpty()) throw new Exception();

if ($u->avatar2->put($path2, true) !== $u) throw new Exception();
if ($u->avatar2->clean() !== true) throw new Exception();
if (\M\ImageUser::one()->avatar2->value === '') throw new Exception();
if (\M\ImageUser::one()->avatar2->isEmpty()) throw new Exception();

if ($u->avatar1->clean(true) !== $u) throw new Exception();
if (\M\ImageUser::one()->avatar1->value !== null) throw new Exception();
if (!\M\ImageUser::one()->avatar1->isNull()) throw new Exception();
if (!\M\ImageUser::one()->avatar1->isEmpty()) throw new Exception();

if ($u->avatar2->clean(true) !== $u) throw new Exception();
if (\M\ImageUser::one()->avatar2->value !== '') throw new Exception();
if (!\M\ImageUser::one()->avatar2->isEmpty()) throw new Exception();
