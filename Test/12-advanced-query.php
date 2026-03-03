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

// === LIKE 運算子 ===
$users = \Model\AdvancedQuery\User::where('name', 'LIKE', 'O%')->all();
if (count($users) != 3) {
  throw new Exception('LIKE failed: expected 3, got ' . count($users));
}

$users = \Model\AdvancedQuery\User::where('name', 'LIKE', 'OA')->all();
if (count($users) != 1 || $users[0]->name != 'OA') {
  throw new Exception('LIKE exact match failed');
}

// === 關聯式陣列 WHERE ===
$users = \Model\AdvancedQuery\User::where(['sex' => 'boy', 'name' => 'OA'])->all();
if (count($users) != 1 || $users[0]->name != 'OA') {
  throw new Exception('associative array where failed');
}

$users = \Model\AdvancedQuery\User::where(['sex' => 'boy', 'id' => [1, 3]])->all();
if (count($users) != 2) {
  throw new Exception('associative array where with IN failed: expected 2, got ' . count($users));
}

// === byKey 單鍵分組 ===
$groups = \Model\AdvancedQuery\User::byKey('sex')->all();
if (count($groups) != 2) {
  throw new Exception('byKey sex failed: expected 2 groups, got ' . count($groups));
}
if (count($groups['boy']) != 2) {
  throw new Exception('byKey boy count failed');
}
if (count($groups['girl']) != 1) {
  throw new Exception('byKey girl count failed');
}

// === byKey 多鍵分組 ===
$groups = \Model\AdvancedQuery\User::byKey('sex', 'name')->all();
if (count($groups) != 3) {
  throw new Exception('byKey multi-key failed: expected 3 groups, got ' . count($groups));
}

// === DateTime Plugin: format() 自訂格式 ===
$user = \Model\AdvancedQuery\User::one(1);
$formatted = $user->birthday->format('Y/m/d');
if ($formatted != '1989/07/21') {
  throw new Exception('DateTime format failed: expected 1989/07/21, got ' . $formatted);
}

// === DateTime Plugin: getDatetime() ===
$dt = $user->birthday->getDatetime();
if (!($dt instanceof \DateTime)) {
  throw new Exception('DateTime getDatetime() should return DateTime instance');
}
if ($dt->format('Y-m-d') != '1989-07-21') {
  throw new Exception('DateTime getDatetime format failed');
}

// === DateTime Plugin: unix() ===
$unix = $user->birthday->unix();
if (!is_int($unix)) {
  throw new Exception('DateTime unix() should return int');
}

// === DateTime Plugin: null 值回傳 default ===
$user3 = \Model\AdvancedQuery\User::one(3);
if ($user3->createAt->getValue() === null) {
  throw new Exception('DateTime createAt should not be null');
}
if ($user3->createAt->format('Y-m-d') === null) {
  throw new Exception('DateTime createAt format should not return null');
}

// === attrs() 取得全部屬性 ===
$user = \Model\AdvancedQuery\User::one(1);
$allAttrs = $user->attrs();
if (!is_array($allAttrs)) {
  throw new Exception('attrs() should return array');
}
if (!array_key_exists('id', $allAttrs)) {
  throw new Exception('attrs() should contain id');
}
if (!array_key_exists('name', $allAttrs)) {
  throw new Exception('attrs() should contain name');
}

// === attrs() 取得指定 key ===
if ($user->attrs('name') != 'OA') {
  throw new Exception('attrs(key) failed');
}

// === attrs() 不存在的 key 回傳 default ===
if ($user->attrs('nonExistent', 'fallback') != 'fallback') {
  throw new Exception('attrs(key, default) failed');
}

// === toArray(true) raw 模式 ===
$raw = $user->toArray(true);
if (!is_array($raw)) {
  throw new Exception('toArray(true) should return array');
}
if (array_key_exists('bio', $raw)) {
  throw new Exception('toArray(true) should still respect $hides');
}
if ($raw['birthday'] != '1989-07-21') {
  throw new Exception('toArray(true) birthday should be raw string: ' . $raw['birthday']);
}

// === toArray(false) 非 raw 模式 DateTime 格式化 ===
$normal = $user->toArray(false);
if ($normal['birthday'] != '1989-07-21') {
  throw new Exception('toArray(false) birthday should be formatted string');
}

// === __isset() 不存在的欄位 ===
if (isset($user->nonExistentField)) {
  throw new Exception('__isset non-existent field should return false');
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

// === save() 帶 $count 參數 ===
$user = \Model\AdvancedQuery\User::one(2);
$user->name = 'OB-updated';
$count = 0;
$user->save($count);
if ($count != 1) {
  throw new Exception('save() count failed: expected 1, got ' . $count);
}

// === save() 無變更時 $count = 1（短路回傳） ===
$count = 0;
$user->save($count);
if ($count != 1) {
  throw new Exception('save() no-change count should be 1, got ' . $count);
}

$user = \Model\AdvancedQuery\User::one(2);
if ($user->name != 'OB-updated') {
  throw new Exception('save() with count persist failed');
}
$user->name = 'OB';
$user->save();

// === set() 搭配 $save=true 自動儲存 ===
$user = \Model\AdvancedQuery\User::one(3);
$user->set(['name' => 'OC-auto'], [], true);

$user = \Model\AdvancedQuery\User::one(3);
if ($user->name != 'OC-auto') {
  throw new Exception('set() with save=true failed');
}
$user->name = 'OC';
$user->save();
