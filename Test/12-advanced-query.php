<?php

use \Orm\Core\Connection;

include '0.php';

$sql = "CREATE TABLE `AdvancedQueryUser` (
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

\Model\AdvancedQuery\User::creates([
  ['name' => 'OA', 'bio' => 'Test', 'sex' => 'boy',  'birthday' => '1989-07-21', 'height' => '171.1', 'weight' => '79.8', 'info' => ['a' => 'a1']],
  ['name' => 'OB', 'bio' => 'Test', 'sex' => 'girl', 'birthday' => '1989-03-19', 'height' => '151.1', 'weight' => '45',   'info' => null],
  ['name' => 'OC', 'bio' => null,   'sex' => 'boy',  'birthday' => '2000-03-29', 'height' => '155.1', 'weight' => '47.1', 'info' => 'null'],
]);

// === whereGroup ===
$user = \Model\AdvancedQuery\User::whereGroup(function ($b) {
  $b->where('id', '>', 1)->where('name', 'OB');
})->one();
if ($user->name != 'OB') {
  throw new Exception('whereGroup failed');
}

// === orWhereGroup ===
$users = \Model\AdvancedQuery\User::where('id', 1)->orWhereGroup(function ($b) {
  $b->where('id', '>', 2);
})->all();
if (count($users) != 2) {
  throw new Exception('orWhereGroup failed: expected 2, got ' . count($users));
}
if ($users[0]->name != 'OA' || $users[1]->name != 'OC') {
  throw new Exception('orWhereGroup order failed');
}

// === whereGroup + 外部條件 ===
$user = \Model\AdvancedQuery\User::where('sex', 'boy')->whereGroup(function ($b) {
  $b->where('id', '>', 2);
})->one();
if ($user->name != 'OC') {
  throw new Exception('whereGroup + outer where failed');
}

// === group() + having() ===
$rows = \Model\AdvancedQuery\User::select('sex', 'COUNT(*) as cnt')->group('sex')->having('cnt > 1')->all();
if (count($rows) != 1) {
  throw new Exception('group/having failed: expected 1, got ' . count($rows));
}
if ($rows[0]->sex != 'boy') {
  throw new Exception('group/having sex failed');
}
if ($rows[0]->cnt != 2) {
  throw new Exception('group/having cnt failed');
}

// === orWhereIn ===
$users = \Model\AdvancedQuery\User::where('id', 1)->orWhereIn('id', [2, 3])->all();
if (count($users) != 3) {
  throw new Exception('orWhereIn failed: expected 3, got ' . count($users));
}

// === orWhereNotIn ===
$users = \Model\AdvancedQuery\User::where('id', 1)->orWhereNotIn('id', [1, 2])->all();
if (count($users) != 2) {
  throw new Exception('orWhereNotIn failed: expected 2, got ' . count($users));
}
if ($users[0]->name != 'OA' || $users[1]->name != 'OC') {
  throw new Exception('orWhereNotIn order failed');
}

// === orWhereBetween ===
$users = \Model\AdvancedQuery\User::where('id', 1)->orWhereBetween('id', 2, 3)->all();
if (count($users) != 3) {
  throw new Exception('orWhereBetween failed: expected 3, got ' . count($users));
}

// === WHERE NULL (IS NULL) ===
$users = \Model\AdvancedQuery\User::where('bio', null)->all();
if (count($users) != 1) {
  throw new Exception('WHERE NULL failed: expected 1, got ' . count($users));
}
if ($users[0]->name != 'OC') {
  throw new Exception('WHERE NULL name failed');
}

// === WHERE NOT NULL (IS NOT NULL) ===
$users = \Model\AdvancedQuery\User::where('bio', '!=', null)->all();
if (count($users) != 2) {
  throw new Exception('WHERE NOT NULL failed: expected 2, got ' . count($users));
}

// === 多欄排序 ===
$users = \Model\AdvancedQuery\User::order('sex ASC', 'id DESC')->all();
if ($users[0]->name != 'OC') {
  throw new Exception('multi order failed: first should be OC (boy, id=3)');
}
if ($users[1]->name != 'OA') {
  throw new Exception('multi order failed: second should be OA (boy, id=1)');
}
if ($users[2]->name != 'OB') {
  throw new Exception('multi order failed: third should be OB (girl, id=2)');
}

// === set() 方法 ===
$user = \Model\AdvancedQuery\User::one(1);
$user->set(['name' => 'XX', 'bio' => 'YY']);
$user->save();

$user = \Model\AdvancedQuery\User::one(1);
if ($user->name != 'XX') {
  throw new Exception('set() name failed');
}
if ($user->bio != 'YY') {
  throw new Exception('set() bio failed');
}

// === set() 搭配 allow ===
$user->set(['name' => 'ZZ', 'bio' => 'WW'], ['name']);
$user->save();

$user = \Model\AdvancedQuery\User::one(1);
if ($user->name != 'ZZ') {
  throw new Exception('set() with allow name failed');
}
if ($user->bio != 'YY') {
  throw new Exception('set() with allow should not change bio');
}

// === toArray() + $hides ===
$user = \Model\AdvancedQuery\User::one(2);
$arr = \Orm\Helper::toArray($user);
if (array_key_exists('bio', $arr)) {
  throw new Exception('toArray hides failed: bio should be hidden');
}
if (!array_key_exists('name', $arr)) {
  throw new Exception('toArray failed: name should exist');
}
if ($arr['name'] != 'OB') {
  throw new Exception('toArray name value failed');
}

// === toArray() 透過實例方法 ===
$arr2 = $user->toArray();
if (array_key_exists('bio', $arr2)) {
  throw new Exception('instance toArray hides failed');
}

// === __isset() ===
$user = \Model\AdvancedQuery\User::one(1);
if (!isset($user->name)) {
  throw new Exception('__isset existing field failed');
}
if (!isset($user->id)) {
  throw new Exception('__isset id failed');
}
