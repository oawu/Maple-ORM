<?php

use \Orm\Core\Connection;

include '0.php';

$sql = "CREATE TABLE `DeleteUser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,

  `name` varchar(190) CHARACTER SET utf8mb4 NULL,
  `bio` text COLLATE utf8mb4_unicode_ci NULL,

  `sex` enum('boy','girl') COLLATE utf8mb4_unicode_ci NULL,

  `birthday` date NULL,

  `height` decimal(10,2),
  `weight` decimal(10,2),

  `info` json NULL,

  `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($error = Connection::instance()->runQuery($sql)) {
  throw $error;
}

function resetDeleteDate() {
  \Model\Delete\User::truncate();
  \Model\Delete\User::creates([
    ['name' => 'OA', 'bio' => 'Test', 'sex' => 'boy', 'birthday' => '1989-07-21', 'height' => '171.1', 'weight' => '79.8', 'info' => ['a' => 'a1', 'b' => [1, 2, 3]]],
    ['name' => 'OB', 'bio' => 'Test', 'sex' => 'girl', 'birthday' => '1989-03-19', 'height' => '151.1', 'weight' => '45', 'info' => null],
    ['name' => 'OC', 'bio' => 'Hi!', 'sex' => 'boy', 'birthday' => '2000-03-29', 'height' => '155.1', 'weight' => '47.1', 'info' => 'null'],
  ]);
}

title('deletes() 全部刪除');
resetDeleteDate();
$count = \Model\Delete\User::deletes();
check($count == 3);
check(\Model\Delete\User::count() == 0);

title('delete() 條件刪除');
resetDeleteDate();
$count = \Model\Delete\User::where('id', '>', 1)->delete();
check($count == 2);
check(\Model\Delete\User::count() == 1);
check(\Model\Delete\User::last()->id == 1);

resetDeleteDate();
$count = \Model\Delete\User::where('id', '>', 10)->delete();
check($count == 0);
check(\Model\Delete\User::last()->id == 3);

title('delete() 單筆刪除');
resetDeleteDate();
$u = \Model\Delete\User::one();
check($u->id == 1);
check($u->delete($count) !== null);
check($count == 1);

$u = \Model\Delete\User::one();
check($u->id == 2);
check($u->delete($count) !== null);
check($count == 1);
