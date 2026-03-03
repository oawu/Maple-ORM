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

// ========== whereGroup ==========

title('whereGroup');

$user = \Model\AdvancedQuery\User::whereGroup(function ($b) {
  $b->where('id', '>', 1)->where('name', 'OB');
})->one();
check($user->name == 'OB');

$users = \Model\AdvancedQuery\User::where('id', 1)->orWhereGroup(function ($b) {
  $b->where('id', '>', 2);
})->all();
check(count($users) == 2);
check($users[0]->name == 'OA');
check($users[1]->name == 'OC');

$user = \Model\AdvancedQuery\User::where('sex', 'boy')->whereGroup(function ($b) {
  $b->where('id', '>', 2);
})->one();
check($user->name == 'OC');

// ========== group / having ==========

title('group / having');

$rows = \Model\AdvancedQuery\User::select('sex', 'COUNT(*) as cnt')->group('sex')->having('cnt > 1')->all();
check(count($rows) == 1);
check($rows[0]->sex == 'boy');
check($rows[0]->cnt == 2);

// ========== orWhereIn / orWhereNotIn / orWhereBetween ==========

title('orWhereIn / orWhereNotIn / orWhereBetween');

$users = \Model\AdvancedQuery\User::where('id', 1)->orWhereIn('id', [2, 3])->all();
check(count($users) == 3);

$users = \Model\AdvancedQuery\User::where('id', 1)->orWhereNotIn('id', [1, 2])->all();
check(count($users) == 2);
check($users[0]->name == 'OA');
check($users[1]->name == 'OC');

$users = \Model\AdvancedQuery\User::where('id', 1)->orWhereBetween('id', 2, 3)->all();
check(count($users) == 3);

// ========== WHERE NULL ==========

title('WHERE NULL');

$users = \Model\AdvancedQuery\User::where('bio', null)->all();
check(count($users) == 1);
check($users[0]->name == 'OC');

$users = \Model\AdvancedQuery\User::where('bio', '!=', null)->all();
check(count($users) == 2);

// ========== 多欄排序 ==========

title('多欄排序');

$users = \Model\AdvancedQuery\User::order('sex ASC', 'id DESC')->all();
check($users[0]->name == 'OC');
check($users[1]->name == 'OA');
check($users[2]->name == 'OB');

// ========== LIKE ==========

title('LIKE');

$users = \Model\AdvancedQuery\User::where('name', 'LIKE', 'O%')->all();
check(count($users) == 3);

$users = \Model\AdvancedQuery\User::where('name', 'LIKE', 'OA')->all();
check(count($users) == 1);
check($users[0]->name == 'OA');

// ========== 關聯式陣列 WHERE ==========

title('關聯式陣列 WHERE');

$users = \Model\AdvancedQuery\User::where(['sex' => 'boy', 'name' => 'OA'])->all();
check(count($users) == 1);
check($users[0]->name == 'OA');

$users = \Model\AdvancedQuery\User::where(['sex' => 'boy', 'id' => [1, 3]])->all();
check(count($users) == 2);

// ========== byKey 分組 ==========

title('byKey 分組');

$groups = \Model\AdvancedQuery\User::byKey('sex')->all();
check(count($groups) == 2);
check(count($groups['boy']) == 2);
check(count($groups['girl']) == 1);

$groups = \Model\AdvancedQuery\User::byKey('sex', 'name')->all();
check(count($groups) == 3);

// ========== DateTime Plugin ==========

title('DateTime Plugin');

$user = \Model\AdvancedQuery\User::one(1);
$formatted = $user->birthday->format('Y/m/d');
check($formatted == '1989/07/21');

$dt = $user->birthday->getDatetime();
check($dt instanceof \DateTime);
check($dt->format('Y-m-d') == '1989-07-21');

$unix = $user->birthday->unix();
check(is_int($unix));

$user3 = \Model\AdvancedQuery\User::one(3);
check($user3->createAt->getValue() !== null);
check($user3->createAt->format('Y-m-d') !== null);

// ========== attrs() ==========

title('attrs()');

$user = \Model\AdvancedQuery\User::one(1);
$allAttrs = $user->attrs();
check(is_array($allAttrs));
check(array_key_exists('id', $allAttrs));
check(array_key_exists('name', $allAttrs));

check($user->attrs('name') == 'OA');
check($user->attrs('nonExistent', 'fallback') == 'fallback');

// ========== toArray ==========

title('toArray');

$raw = $user->toArray(true);
check(is_array($raw));
check(!array_key_exists('bio', $raw));
check($raw['birthday'] == '1989-07-21');

$normal = $user->toArray(false);
check($normal['birthday'] == '1989-07-21');

$user = \Model\AdvancedQuery\User::one(2);
$arr = \Orm\Helper::toArray($user);
check(!array_key_exists('bio', $arr));
check(array_key_exists('name', $arr));
check($arr['name'] == 'OB');

$arr2 = $user->toArray();
check(!array_key_exists('bio', $arr2));

// ========== __isset ==========

title('__isset');

$user = \Model\AdvancedQuery\User::one(1);
check(!isset($user->nonExistentField));
check(isset($user->name));
check(isset($user->id));

// ========== set() / save($count) ==========

title('set() / save($count)');

$user = \Model\AdvancedQuery\User::one(1);
$user->set(['name' => 'XX', 'bio' => 'YY']);
$user->save();

$user = \Model\AdvancedQuery\User::one(1);
check($user->name == 'XX');
check($user->bio == 'YY');

$user->set(['name' => 'ZZ', 'bio' => 'WW'], ['name']);
$user->save();

$user = \Model\AdvancedQuery\User::one(1);
check($user->name == 'ZZ');
check($user->bio == 'YY');

$user = \Model\AdvancedQuery\User::one(2);
$user->name = 'OB-updated';
$count = 0;
$user->save($count);
check($count == 1);

$count = 0;
$user->save($count);
check($count == 1);

$user = \Model\AdvancedQuery\User::one(2);
check($user->name == 'OB-updated');
$user->name = 'OB';
$user->save();

$user = \Model\AdvancedQuery\User::one(3);
$user->set(['name' => 'OC-auto'], [], true);

$user = \Model\AdvancedQuery\User::one(3);
check($user->name == 'OC-auto');
$user->name = 'OC';
$user->save();
