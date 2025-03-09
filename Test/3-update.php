<?php

use \Orm\Core\Connection;

include '0.php';

$sql = "CREATE TABLE `UpdateUser` (
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

\Model\Update\User::creates([
  ['name' => 'OA', 'bio' => 'Test', 'sex' => 'boy', 'birthday' => '1989-07-21', 'height' => '171.1', 'weight' => '79.8', 'info' => ['a' => 'a1', 'b' => [1, 2, 3]]],
  ['name' => 'OB', 'bio' => 'Test', 'sex' => 'girl', 'birthday' => '1989-03-19', 'height' => '151.1', 'weight' => '45', 'info' => null],
  ['name' => 'OC', 'bio' => 'Hi!', 'sex' => 'boy', 'birthday' => '2000-03-29', 'height' => '155.1', 'weight' => '47.1', 'info' => 'null'],
]);

if (\Model\Update\User::one(1)->birthday->getValue() != '1989-07-21') {
  throw new Exception();
}
if (\Model\Update\User::one(2)->birthday->getValue() != '1989-03-19') {
  throw new Exception();
}
if (\Model\Update\User::one(3)->birthday->getValue() != '2000-03-29') {
  throw new Exception();
}

if (\Model\Update\User::updates(['birthday' => '1900-01-01']) != 3) {
  throw new Exception();
}

$u1 = \Model\Update\User::one(1);
$u2 = \Model\Update\User::one(2);
$u3 = \Model\Update\User::one(3);

if ($u1->birthday->getValue() != '1900-01-01') {
  throw new Exception();
}
if ($u2->birthday->getValue() != '1900-01-01') {
  throw new Exception();
}
if ($u3->birthday->getValue() != '1900-01-01') {
  throw new Exception();
}

$u1->birthday = '1989-07-21';
$u2->birthday = '1989-03-19';
$u3->birthday = '2000-03-29';

if (!($u1->save() && $u2->save() && $u3->save())) {
  throw new Exception();
}

if (\Model\Update\User::where('id', '!=', 1)->update(['birthday' => '1900-01-01']) != 2) {
  throw new Exception();
}

if (\Model\Update\User::one(1)->birthday->getValue() != '1989-07-21') {
  throw new Exception();
}
if (\Model\Update\User::one(2)->birthday->getValue() != '1900-01-01') {
  throw new Exception();
}
if (\Model\Update\User::one(3)->birthday->getValue() != '1900-01-01') {
  throw new Exception();
}

$u1 = \Model\Update\User::one(1);
if ($u1->info !== ['a' => 'a1', 'b' => [1, 2, 3]]) {
  throw new Exception();
}
$u1->info = null;
$u1->save();

$u1 = \Model\Update\User::one(1);
if ($u1->info !== null) {
  throw new Exception();
}
$u1->info = 'null';
$u1->save();

$u1 = \Model\Update\User::one(1);
if ($u1->info !== 'null') {
  throw new Exception();
}
$u1->info = 0;
$u1->save();

$u1 = \Model\Update\User::one(1);
if ($u1->info !== 0) {
  throw new Exception();
}

$u2 = \Model\Update\User::one(2);
$u2->info = ['a' => 'a1', 'b' => [1, 2, 3]];
$u2->save();
$u2 = \Model\Update\User::one(2);
if ($u2->info !== ['a' => 'a1', 'b' => [1, 2, 3]]) {
  throw new Exception();
}
\Model\Update\User::where(2)->update(['info' => null]);

$u2 = \Model\Update\User::one(2);
if ($u2->info !== null) {
  throw new Exception();
}
\Model\Update\User::where(2)->update(['info' => 'null']);
$u2 = \Model\Update\User::one(2);
if ($u2->info !== 'null') {
  throw new Exception();
}

\Model\Update\User::where(2)->update(['info' => 0]);
$u2 = \Model\Update\User::one(2);
if ($u2->info !== 0) {
  throw new Exception();
}

$u = \Model\Update\User::one(1);
if ($u->birthday->getValue() != '1989-07-21') {
  throw new Exception();
}
$u->birthday = $date = date('Y-m-d');
if (!$u->save()) {
  throw new Exception();
}

$u = \Model\Update\User::one(1);
if ($u->birthday->getValue() != $date) {
  throw new Exception();
}

$u->birthday = \DateTime::createFromFormat('Y-m-d', $date);
if (!$u->save()) {
  throw new Exception(\Orm\Model::getLastLog());
}

$u = \Model\Update\User::one(1);
if ($u->birthday->getValue() != $date) {
  throw new Exception();
}

$u->birthday = '1989-07-21';
if (!$u->save()) {
  throw new Exception(\Orm\Model::getLastLog());
}

$u = \Model\Update\User::one(1);
if ($u->birthday->getValue() != '1989-07-21') {
  throw new Exception();
}

$u->birthday = null;
if (!$u->save()) {
  throw new Exception(\Orm\Model::getLastLog());
}

$u = \Model\Update\User::one(1);
if ($u->birthday->getValue() != null) {
  throw new Exception();
}
