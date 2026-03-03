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

// === 初始值 ===
title('初始值');

$u = resetFile($path1, $path2);

check($u->info1->getValue() === null);
check($u->info2->getValue() === '');

// === 指定後未儲存 ===
title('指定後未儲存');

$u->info1 = $path1;
check(User::one()->info1->getValue() === null);

$u->info2 = $path2;
check(User::one()->info2->getValue() === '');

// === save() 寫入 ===
title('save() 寫入');

check($u->save() !== null);
check(User::one()->info1->getValue() !== null);
check(User::one()->info2->getValue() !== '');

// === 分次 save() ===
title('分次 save()');

$u = resetFile($path1, $path2);

check($u->info1->getValue() === null);
check($u->info2->getValue() === '');

$u->info1 = $path1;
$u->save();
check(User::one()->info1->getValue() !== null);

$u->info2 = $path2;
$u->save();
check(User::one()->info2->getValue() !== '');

// === URL ===
title('URL');

$u = resetFile($path1, $path2);
check($u->info1->getUrl() === 'http://dev.orm.ioa.tw/404.png');
check($u->info2->getUrl() === 'http://dev.orm.ioa.tw/404.png');

// === 清除檔案 ===
title('清除檔案');

$u->info1 = $path1;
$u->save();
$u->info1 = '';
check(User::one()->info1->getValue() !== null);

$u->info2 = $path2;
$u->save();
$u->info2 = '';
check(User::one()->info2->getValue() !== '');

$u->info1 = '';
$u->save();
check(User::one()->info1->getValue() === null);

$u->info2 = '';
$u->save();
check(User::one()->info2->getValue() === '');
