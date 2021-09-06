<?php

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

if ($error = \M\Core\Connection::instance()->query($sql))
  throw new Exception($error);

\M\SelectUser::creates([
  ['name' => 'OA', 'bio' => 'Test', 'sex' => 'boy', 'birthday' => '1989-07-21', 'height' => '171.1', 'weight' => '79.8', 'info' => ['a' => 'a1', 'b' => [1, 2, 3]]],
  ['name' => 'OB', 'bio' => 'Test', 'sex' => 'girl', 'birthday' => '1989-03-19', 'height' => '151.1', 'weight' => '45', 'info' => null],
  ['name' => 'OC', 'bio' => 'Hi!', 'sex' => 'boy', 'birthday' => '2000-03-29', 'height' => '155.1', 'weight' => '47.1', 'info' => 'null'],
]);

if (\M\SelectUser::one()->name != 'OA') throw new Exception();
if (\M\SelectUser::one(2)->name != 'OB') throw new Exception();
if (\M\SelectUser::one('2')->name != 'OB') throw new Exception();
if (\M\SelectUser::where(2)->one()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('2')->one()->name != 'OB') throw new Exception();
if (\M\SelectUser::where([2, 3])->one()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', 2)->one()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', '=', 2)->one()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', '>', 2)->one()->name != 'OC') throw new Exception();
if (\M\SelectUser::where('id', '<', 2)->one()->name != 'OA') throw new Exception();
if (\M\SelectUser::where('id', [2, 3])->one()->name != 'OB') throw new Exception();
if (\M\SelectUser::whereIn('id', [2, 3])->orWhere(1)->one()->name != 'OA') throw new Exception();
if (\M\SelectUser::in('id', [2, 3])->or(1)->one()->name != 'OA') throw new Exception();
if (\M\SelectUser::whereIn('id', [2, 3])->or(1)->one()->name != 'OA') throw new Exception();
if (\M\SelectUser::whereNotIn('id', [2, 3])->one()->name != 'OA') throw new Exception();
if (\M\SelectUser::where('id', '>', 1)->where('id', '<', 3)->one()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', '>', 2)->orWhere('id', '<', 2)->one()->name != 'OA') throw new Exception();
if (\M\SelectUser::where('id', '>', 2)->or('id', '<', 2)->one()->name != 'OA') throw new Exception();
if (\M\SelectUser::whereBetween('id', 2, 3)->one()->name != 'OB') throw new Exception();

if (\M\SelectUser::first()->name != 'OA') throw new Exception();
if (\M\SelectUser::first(2)->name != 'OB') throw new Exception();
if (\M\SelectUser::first('2')->name != 'OB') throw new Exception();
if (\M\SelectUser::where(2)->first()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('2')->first()->name != 'OB') throw new Exception();
if (\M\SelectUser::where([2, 3])->first()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', 2)->first()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', '=', 2)->first()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', '>', 2)->first()->name != 'OC') throw new Exception();
if (\M\SelectUser::where('id', '<', 2)->first()->name != 'OA') throw new Exception();
if (\M\SelectUser::where('id', [2, 3])->first()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', 'in', [2, 3])->first()->name != 'OB') throw new Exception();
if (\M\SelectUser::whereIn('id', [2, 3])->orWhere(1)->first()->name != 'OA') throw new Exception();
if (\M\SelectUser::in('id', [2, 3])->or(1)->first()->name != 'OA') throw new Exception();
if (\M\SelectUser::whereIn('id', [2, 3])->or(1)->first()->name != 'OA') throw new Exception();
if (\M\SelectUser::whereNotIn('id', [2, 3])->first()->name != 'OA') throw new Exception();
if (\M\SelectUser::where('id', '>', 1)->where('id', '<', 3)->first()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', '>', 2)->orWhere('id', '<', 2)->first()->name != 'OA') throw new Exception();
if (\M\SelectUser::where('id', '>', 2)->or('id', '<', 2)->first()->name != 'OA') throw new Exception();
if (\M\SelectUser::whereBetween('id', 2, 3)->first()->name != 'OB') throw new Exception();

if (\M\SelectUser::last()->name != 'OC') throw new Exception();
if (\M\SelectUser::last(2)->name != 'OB') throw new Exception();
if (\M\SelectUser::last('2')->name != 'OB') throw new Exception();
if (\M\SelectUser::where(2)->last()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('2')->last()->name != 'OB') throw new Exception();
if (\M\SelectUser::where([2, 3])->last()->name != 'OC') throw new Exception();
if (\M\SelectUser::where('id', 2)->last()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', '=', 2)->last()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', '>', 2)->last()->name != 'OC') throw new Exception();
if (\M\SelectUser::where('id', '<', 2)->last()->name != 'OA') throw new Exception();
if (\M\SelectUser::where('id', [2, 3])->last()->name != 'OC') throw new Exception();
if (\M\SelectUser::where('id', 'in', [2, 3])->last()->name != 'OC') throw new Exception();
if (\M\SelectUser::whereIn('id', [2, 3])->orWhere(1)->last()->name != 'OC') throw new Exception();
if (\M\SelectUser::in('id', [2, 3])->or(1)->last()->name != 'OC') throw new Exception();
if (\M\SelectUser::whereIn('id', [2, 3])->or(1)->last()->name != 'OC') throw new Exception();
if (\M\SelectUser::whereNotIn('id', [2, 3])->last()->name != 'OA') throw new Exception();
if (\M\SelectUser::where('id', '>', 1)->where('id', '<', 3)->last()->name != 'OB') throw new Exception();
if (\M\SelectUser::where('id', '>', 2)->orWhere('id', '<', 2)->last()->name != 'OC') throw new Exception();
if (\M\SelectUser::where('id', '>', 2)->or('id', '<', 2)->last()->name != 'OC') throw new Exception();
if (\M\SelectUser::whereBetween('id', 1, 2)->last()->name != 'OB') throw new Exception();

$user = \M\SelectUser::select('id', 'name')->one();
if ($user->id != 1) throw new Exception();
if ($user->name != 'OA') throw new Exception();
if ($user->bio !== null) throw new Exception();

$user = \M\SelectUser::select('id, name')->one();
if ($user->id != 1) throw new Exception();
if ($user->name != 'OA') throw new Exception();
if ($user->bio !== null) throw new Exception();

$user = \M\SelectUser::select(['id' => 'i', 'name' => 'n'])->one();
if ($user->i != 1) throw new Exception();
if ($user->n != 'OA') throw new Exception();
if ($user->bio !== null) throw new Exception();

$user = \M\SelectUser::select('id, name', 'bio', ['height' => 'h'])->one();
if ($user->id != 1) throw new Exception();
if ($user->name != 'OA') throw new Exception();
if ($user->bio === 'boy') throw new Exception();
if ($user->h === 171.1) throw new Exception();
if ($user->weight !== null) throw new Exception();

if (\M\SelectUser::limit(2)->last()->id != 3) throw new Exception();
if (\M\SelectUser::offset(1)->limit(2)->last()->id != 2) throw new Exception();
if (\M\SelectUser::offset(1)->last()->id != 2) throw new Exception();

if (count(\M\SelectUser::limit(2)->all()) != 2) throw new Exception();
if (count(\M\SelectUser::limit(2)->offset(2)->all()) != 1) throw new Exception();
if (count(\M\SelectUser::offset(1)->all()) != 0) throw new Exception();

$users = \M\SelectUser::keyBy('bio')->all();
if (count($users) != 2) throw new Exception();
if (count($users['Test']) != 2) throw new Exception();
if (count($users['Hi!']) != 1) throw new Exception();

if (\M\SelectUser::where('id', '>', 1)->count() != 2) throw new Exception();
if (\M\SelectUser::where('id', '>', 10)->count() != 0) throw new Exception();


$users = \M\SelectUser::where('id', '>', 2)->all();

if (count($users) != 1) throw new Exception();
$user = array_shift($users);
if (!$user) throw new Exception();
if ($user->id != 3) throw new Exception();

$users = \M\SelectUser::order('id DESC')->all();
if ($users[0]->id != 3) throw new Exception();
if ($users[1]->id != 2) throw new Exception();
if ($users[2]->id != 1) throw new Exception();
if ($users[0]->info !== 'null') throw new Exception();
if ($users[1]->info !== null) throw new Exception();
if ($users[2]->info !== ['a' => 'a1', 'b' => [1, 2, 3]]) throw new Exception();

