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

// === 初始值 ===
title('初始值');
$u = resetImage($path1, $path2);
check($u->avatar1->getValue() === null);
check($u->avatar2->getValue() === '');

// === 指定後未儲存 ===
title('指定後未儲存');
$u->avatar1 = $path1;
check(User::one()->avatar1->getValue() === null);

$u->avatar2 = $path2;
check(User::one()->avatar2->getValue() === '');

// === save() 寫入 ===
title('save() 寫入');
check($u->save() !== null);
check(User::one()->avatar1->getValue() !== null);
check(User::one()->avatar2->getValue() !== '');

// === 分次 save() ===
title('分次 save()');
$u = resetImage($path1, $path2);
check($u->avatar1->getValue() === null);
check($u->avatar2->getValue() === '');

$u->avatar1 = $path1;
$u->save();
check(User::one()->avatar1->getValue() !== null);

$u->avatar2 = $path2;
$u->save();
check(User::one()->avatar2->getValue() !== '');

// === URL 與版本 ===
title('URL 與版本');
$u = resetImage($path1, $path2);
check($u->avatar1->getUrl() === 'http://dev.orm.ioa.tw/404.png');
check($u->avatar2->getUrl() === 'http://dev.orm.ioa.tw/404.png');
check(array_keys($u->avatar1->getVersions()) === ['w100', 'rotate']);
check(array_keys($u->avatar2->getVersions()) === ['w100']);

// === 清除圖片 ===
title('清除圖片');
$u->avatar1 = $path1;
$u->save();

$u->avatar1 = '';
check(User::one()->avatar1->getValue() !== null);

$u->avatar2 = $path2;
$u->save();

$u->avatar2 = '';
check(User::one()->avatar2->getValue() !== '');

$u->avatar1 = '';
$u->save();
check(User::one()->avatar1->getValue() === null);

$u->avatar2 = '';
$u->save();
check(User::one()->avatar2->getValue() === '');
