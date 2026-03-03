<?php

use \Orm\Core\Connection;

include '0.php';

$sql = "CREATE TABLE `SelectUser` (
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

\Model\Select\User::creates([
  ['name' => 'OA', 'bio' => 'Test', 'sex' => 'boy', 'birthday' => '1989-07-21', 'height' => '171.1', 'weight' => '79.8', 'info' => ['a' => 'a1', 'b' => [1, 2, 3]]],
  ['name' => 'OB', 'bio' => 'Test', 'sex' => 'girl', 'birthday' => '1989-03-19', 'height' => '151.1', 'weight' => '45', 'info' => null],
  ['name' => 'OC', 'bio' => 'Hi!', 'sex' => 'boy', 'birthday' => '2000-03-29', 'height' => '155.1', 'weight' => '47.1', 'info' => 'null'],
]);

// ========== one() 查詢 ==========

title('one() 查詢');

check(\Model\Select\User::one()->name == 'OA');
check(\Model\Select\User::one(2)->name == 'OB');
check(\Model\Select\User::one('2')->name == 'OB');
check(\Model\Select\User::where(2)->one()->name == 'OB');
check(\Model\Select\User::where('2')->one()->name == 'OB');
check(\Model\Select\User::where([2, 3])->one()->name == 'OB');
check(\Model\Select\User::where('id', 2)->one()->name == 'OB');
check(\Model\Select\User::where('id', '=', 2)->one()->name == 'OB');
check(\Model\Select\User::where('id', '>', 2)->one()->name == 'OC');
check(\Model\Select\User::where('id', '<', 2)->one()->name == 'OA');
check(\Model\Select\User::where('id', [2, 3])->one()->name == 'OB');
check(\Model\Select\User::whereIn('id', [2, 3])->orWhere(1)->one()->name == 'OA');
check(\Model\Select\User::in('id', [2, 3])->or(1)->one()->name == 'OA');
check(\Model\Select\User::whereIn('id', [2, 3])->or(1)->one()->name == 'OA');
check(\Model\Select\User::whereNotIn('id', [2, 3])->one()->name == 'OA');
check(\Model\Select\User::where('id', '>', 1)->where('id', '<', 3)->one()->name == 'OB');
check(\Model\Select\User::where('id', '>', 2)->orWhere('id', '<', 2)->one()->name == 'OA');
check(\Model\Select\User::where('id', '>', 2)->or('id', '<', 2)->one()->name == 'OA');
check(\Model\Select\User::whereBetween('id', 2, 3)->one()->name == 'OB');

// ========== first() 查詢 ==========

title('first() 查詢');

check(\Model\Select\User::first()->name == 'OA');
check(\Model\Select\User::first(2)->name == 'OB');
check(\Model\Select\User::first('2')->name == 'OB');
check(\Model\Select\User::where(2)->first()->name == 'OB');
check(\Model\Select\User::where('2')->first()->name == 'OB');
check(\Model\Select\User::where([2, 3])->first()->name == 'OB');
check(\Model\Select\User::where('id', 2)->first()->name == 'OB');
check(\Model\Select\User::where('id', '=', 2)->first()->name == 'OB');
check(\Model\Select\User::where('id', '>', 2)->first()->name == 'OC');
check(\Model\Select\User::where('id', '<', 2)->first()->name == 'OA');
check(\Model\Select\User::where('id', [2, 3])->first()->name == 'OB');
check(\Model\Select\User::where('id', 'in', [2, 3])->first()->name == 'OB');
check(\Model\Select\User::whereIn('id', [2, 3])->orWhere(1)->first()->name == 'OA');
check(\Model\Select\User::in('id', [2, 3])->or(1)->first()->name == 'OA');
check(\Model\Select\User::whereIn('id', [2, 3])->or(1)->first()->name == 'OA');
check(\Model\Select\User::whereNotIn('id', [2, 3])->first()->name == 'OA');
check(\Model\Select\User::where('id', '>', 1)->where('id', '<', 3)->first()->name == 'OB');
check(\Model\Select\User::where('id', '>', 2)->orWhere('id', '<', 2)->first()->name == 'OA');
check(\Model\Select\User::where('id', '>', 2)->or('id', '<', 2)->first()->name == 'OA');
check(\Model\Select\User::whereBetween('id', 2, 3)->first()->name == 'OB');

// ========== last() 查詢 ==========

title('last() 查詢');

check(\Model\Select\User::last()->name == 'OC');
check(\Model\Select\User::last(2)->name == 'OB');
check(\Model\Select\User::last('2')->name == 'OB');
check(\Model\Select\User::where(2)->last()->name == 'OB');
check(\Model\Select\User::where('2')->last()->name == 'OB');
check(\Model\Select\User::where([2, 3])->last()->name == 'OC');
check(\Model\Select\User::where('id', 2)->last()->name == 'OB');
check(\Model\Select\User::where('id', '=', 2)->last()->name == 'OB');
check(\Model\Select\User::where('id', '>', 2)->last()->name == 'OC');
check(\Model\Select\User::where('id', '<', 2)->last()->name == 'OA');
check(\Model\Select\User::where('id', [2, 3])->last()->name == 'OC');
check(\Model\Select\User::where('id', 'in', [2, 3])->last()->name == 'OC');
check(\Model\Select\User::whereIn('id', [2, 3])->orWhere(1)->last()->name == 'OC');
check(\Model\Select\User::in('id', [2, 3])->or(1)->last()->name == 'OC');
check(\Model\Select\User::whereIn('id', [2, 3])->or(1)->last()->name == 'OC');
check(\Model\Select\User::whereNotIn('id', [2, 3])->last()->name == 'OA');
check(\Model\Select\User::where('id', '>', 1)->where('id', '<', 3)->last()->name == 'OB');
check(\Model\Select\User::where('id', '>', 2)->orWhere('id', '<', 2)->last()->name == 'OC');
check(\Model\Select\User::where('id', '>', 2)->or('id', '<', 2)->last()->name == 'OC');
check(\Model\Select\User::whereBetween('id', 1, 2)->last()->name == 'OB');

// ========== select() 指定欄位 ==========

title('select() 指定欄位');

$user = \Model\Select\User::select('id', 'name')->one();
check($user->id == 1);
check($user->name == 'OA');
check($user->bio === null);

$user = \Model\Select\User::select('id, name')->one();
check($user->id == 1);
check($user->name == 'OA');
check($user->bio === null);

$user = \Model\Select\User::select('id as i', 'name as n')->one();
check($user->i == 1);
check($user->n == 'OA');
check($user->bio === null);

$user = \Model\Select\User::select('id, name', 'bio', 'height as h')->one();
check($user->id == 1);
check($user->name == 'OA');
check($user->bio !== 'boy');
check($user->h !== 171.1);
check($user->weight === null);

// ========== limit / offset ==========

title('limit / offset');

check(\Model\Select\User::limit(2)->last()->id == 3);
check(\Model\Select\User::offset(1)->limit(2)->last()->id == 2);
check(\Model\Select\User::offset(1)->last()->id == 2);

check(count(\Model\Select\User::limit(2)->all()) == 2);
check(count(\Model\Select\User::limit(2)->offset(2)->all()) == 1);
check(count(\Model\Select\User::offset(1)->all()) == 3);

// ========== byKey 分組 ==========

title('byKey 分組');

$users = \Model\Select\User::byKey('bio')->all();
check(count($users) == 2);
check(count($users['Test']) == 2);
check(count($users['Hi!']) == 1);

// ========== count() ==========

title('count()');

check(\Model\Select\User::where('id', '>', 1)->count() == 2);
check(\Model\Select\User::where('id', '>', 10)->count() == 0);

// ========== order() 排序 ==========

title('order() 排序');

$users = \Model\Select\User::where('id', '>', 2)->all();
check(count($users) == 1);
$user = array_shift($users);
check($user != false);
check($user->id == 3);

$users = \Model\Select\User::order('id DESC')->all();
check($users[0]->id == 3);
check($users[1]->id == 2);
check($users[2]->id == 1);
check($users[0]->info === 'null');
check($users[1]->info === null);
check($users[2]->info === ['a' => 'a1', 'b' => [1, 2, 3]]);
