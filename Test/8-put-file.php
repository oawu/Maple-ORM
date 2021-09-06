<?php

include '0.php';

$sql = "CREATE TABLE `FileUser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `info1` varchar(50) CHARACTER SET utf8mb4,
  `info2` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($error = \M\Core\Connection::instance()->query($sql)) throw new Exception($error);

$path1 = PATH_SAMPLE . 'avatar2.zip';
$path2 = PATH_SAMPLE . 'avatar3.zip';

function resetFile($path1, $path2) {
  @exec('rm -rf ' . PATH . 'Storage/*');

  if (!(file_exists($path1) || @copy(PATH_SAMPLE . 'avatar.zip', $path1)))
    throw new Exception();
  
  if (!(file_exists($path2) || @copy(PATH_SAMPLE . 'avatar.zip', $path2)))
    throw new Exception();

  \M\FileUser::truncate();
  return \M\FileUser::create();
}

$u = resetFile($path1, $path2);

if ($u->info1->value !== null) throw new Exception();
if (!$u->info1->isNull()) throw new Exception();
if (!$u->info1->isEmpty()) throw new Exception();
if ($u->info2->value !== '') throw new Exception();
if (!$u->info2->isEmpty()) throw new Exception();

if ($u->info1->put($path1) !== true) throw new Exception();
if (\M\FileUser::one()->info1->value !== null) throw new Exception();
if (!\M\FileUser::one()->info1->isNull()) throw new Exception();
if (!\M\FileUser::one()->info1->isEmpty()) throw new Exception();
if ($u->info2->put($path2) !== true) throw new Exception();
if (\M\FileUser::one()->info2->value !== '') throw new Exception();
if (!\M\FileUser::one()->info2->isEmpty()) throw new Exception();

if ($u->save() === null) throw new Exception();
if (\M\FileUser::one()->info1->value === null) throw new Exception();
if (\M\FileUser::one()->info1->isNull()) throw new Exception();
if (\M\FileUser::one()->info1->isEmpty()) throw new Exception();
if (\M\FileUser::one()->info2->value === '') throw new Exception();
if (\M\FileUser::one()->info2->isEmpty()) throw new Exception();

$u = resetFile($path1, $path2);

if ($u->info1->value !== null) throw new Exception();
if (!$u->info1->isNull()) throw new Exception();
if (!$u->info1->isEmpty()) throw new Exception();
if ($u->info2->value !== '') throw new Exception();
if (!$u->info2->isEmpty()) throw new Exception();

if ($u->info1->put($path1, true) !== $u) throw new Exception();
if (\M\FileUser::one()->info1->value === null) throw new Exception();
if (\M\FileUser::one()->info1->isNull()) throw new Exception();
if (\M\FileUser::one()->info1->isEmpty()) throw new Exception();

if ($u->info2->put($path2, true) !== $u) throw new Exception();
if (\M\FileUser::one()->info2->value === '') throw new Exception();
if (\M\FileUser::one()->info2->isEmpty()) throw new Exception();

$u = resetFile($path1, $path2);
if ($u->info1->url() !== 'http://dev.orm.ioa.tw/404.png') throw new Exception();
if ($u->info2->url() !== 'http://dev.orm.ioa.tw/404.png') throw new Exception();

if ($u->info1->put($path1, true) !== $u) throw new Exception();
if ($u->info1->clean() !== true) throw new Exception();
if (\M\FileUser::one()->info1->value === null) throw new Exception();
if (\M\FileUser::one()->info1->isNull()) throw new Exception();
if (\M\FileUser::one()->info1->isEmpty()) throw new Exception();

if ($u->info2->put($path2, true) !== $u) throw new Exception();
if ($u->info2->clean() !== true) throw new Exception();
if (\M\FileUser::one()->info2->value === '') throw new Exception();
if (\M\FileUser::one()->info2->isEmpty()) throw new Exception();

if ($u->info1->clean(true) !== $u) throw new Exception();
if (\M\FileUser::one()->info1->value !== null) throw new Exception();
if (!\M\FileUser::one()->info1->isNull()) throw new Exception();
if (!\M\FileUser::one()->info1->isEmpty()) throw new Exception();

if ($u->info2->clean(true) !== $u) throw new Exception();
if (\M\FileUser::one()->info2->value !== '') throw new Exception();
if (!\M\FileUser::one()->info2->isEmpty()) throw new Exception();
