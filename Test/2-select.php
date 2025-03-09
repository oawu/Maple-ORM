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

if (\Model\Select\User::one()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::one(2)->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::one('2')->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where(2)->one()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('2')->one()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where([2, 3])->one()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', 2)->one()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '=', 2)->one()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 2)->one()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '<', 2)->one()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::where('id', [2, 3])->one()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::whereIn('id', [2, 3])->orWhere(1)->one()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::in('id', [2, 3])->or(1)->one()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::whereIn('id', [2, 3])->or(1)->one()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::whereNotIn('id', [2, 3])->one()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 1)->where('id', '<', 3)->one()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 2)->orWhere('id', '<', 2)->one()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 2)->or('id', '<', 2)->one()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::whereBetween('id', 2, 3)->one()->name != 'OB') {
  throw new Exception();
}

if (\Model\Select\User::first()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::first(2)->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::first('2')->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where(2)->first()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('2')->first()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where([2, 3])->first()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', 2)->first()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '=', 2)->first()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 2)->first()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '<', 2)->first()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::where('id', [2, 3])->first()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', 'in', [2, 3])->first()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::whereIn('id', [2, 3])->orWhere(1)->first()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::in('id', [2, 3])->or(1)->first()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::whereIn('id', [2, 3])->or(1)->first()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::whereNotIn('id', [2, 3])->first()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 1)->where('id', '<', 3)->first()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 2)->orWhere('id', '<', 2)->first()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 2)->or('id', '<', 2)->first()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::whereBetween('id', 2, 3)->first()->name != 'OB') {
  throw new Exception();
}

if (\Model\Select\User::last()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::last(2)->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::last('2')->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where(2)->last()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('2')->last()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where([2, 3])->last()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::where('id', 2)->last()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '=', 2)->last()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 2)->last()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '<', 2)->last()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::where('id', [2, 3])->last()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::where('id', 'in', [2, 3])->last()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::whereIn('id', [2, 3])->orWhere(1)->last()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::in('id', [2, 3])->or(1)->last()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::whereIn('id', [2, 3])->or(1)->last()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::whereNotIn('id', [2, 3])->last()->name != 'OA') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 1)->where('id', '<', 3)->last()->name != 'OB') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 2)->orWhere('id', '<', 2)->last()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 2)->or('id', '<', 2)->last()->name != 'OC') {
  throw new Exception();
}
if (\Model\Select\User::whereBetween('id', 1, 2)->last()->name != 'OB') {
  throw new Exception();
}

$user = \Model\Select\User::select('id', 'name')->one();
if ($user->id != 1) {
  throw new Exception();
}
if ($user->name != 'OA') {
  throw new Exception();
}
if ($user->bio !== null) {
  throw new Exception();
}

$user = \Model\Select\User::select('id, name')->one();
if ($user->id != 1) {
  throw new Exception();
}
if ($user->name != 'OA') {
  throw new Exception();
}
if ($user->bio !== null) {
  throw new Exception();
}

$user = \Model\Select\User::select('id as i', 'name as n')->one();
if ($user->i != 1) {
  throw new Exception();
}
if ($user->n != 'OA') {
  throw new Exception();
}
if ($user->bio !== null) {
  throw new Exception();
}

$user = \Model\Select\User::select('id, name', 'bio', 'height as h')->one();
if ($user->id != 1) {
  throw new Exception();
}
if ($user->name != 'OA') {
  throw new Exception();
}
if ($user->bio === 'boy') {
  throw new Exception();
}
if ($user->h === 171.1) {
  throw new Exception();
}
if ($user->weight !== null) {
  throw new Exception();
}

if (\Model\Select\User::limit(2)->last()->id != 3) {
  throw new Exception();
}
if (\Model\Select\User::offset(1)->limit(2)->last()->id != 2) {
  throw new Exception();
}
if (\Model\Select\User::offset(1)->last()->id != 2) {
  throw new Exception();
}

if (count(\Model\Select\User::limit(2)->all()) != 2) {
  throw new Exception();
}
if (count(\Model\Select\User::limit(2)->offset(2)->all()) != 1) {
  throw new Exception();
}
if (count(\Model\Select\User::offset(1)->all()) != 3) {
  throw new Exception();
}

$users = \Model\Select\User::byKey('bio')->all();
if (count($users) != 2) {
  throw new Exception();
}
if (count($users['Test']) != 2) {
  throw new Exception();
}
if (count($users['Hi!']) != 1) {
  throw new Exception();
}

if (\Model\Select\User::where('id', '>', 1)->count() != 2) {
  throw new Exception();
}
if (\Model\Select\User::where('id', '>', 10)->count() != 0) {
  throw new Exception();
}


$users = \Model\Select\User::where('id', '>', 2)->all();

if (count($users) != 1) {
  throw new Exception();
}
$user = array_shift($users);
if (!$user) {
  throw new Exception();
}
if ($user->id != 3) {
  throw new Exception();
}

$users = \Model\Select\User::order('id DESC')->all();
if ($users[0]->id != 3) {
  throw new Exception();
}
if ($users[1]->id != 2) {
  throw new Exception();
}
if ($users[2]->id != 1) {
  throw new Exception();
}
if ($users[0]->info !== 'null') {
  throw new Exception();
}
if ($users[1]->info !== null) {
  throw new Exception();
}
if ($users[2]->info !== ['a' => 'a1', 'b' => [1, 2, 3]]) {
  throw new Exception();
}
