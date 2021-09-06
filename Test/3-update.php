<?php

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

if ($error = \M\Core\Connection::instance()->query($sql))
  throw new Exception($error);

\M\UpdateUser::creates([
  ['name' => 'OA', 'bio' => 'Test', 'sex' => 'boy', 'birthday' => '1989-07-21', 'height' => '171.1', 'weight' => '79.8', 'info' => ['a' => 'a1', 'b' => [1, 2, 3]]],
  ['name' => 'OB', 'bio' => 'Test', 'sex' => 'girl', 'birthday' => '1989-03-19', 'height' => '151.1', 'weight' => '45', 'info' => null],
  ['name' => 'OC', 'bio' => 'Hi!', 'sex' => 'boy', 'birthday' => '2000-03-29', 'height' => '155.1', 'weight' => '47.1', 'info' => 'null'],
]);

if (\M\UpdateUser::one(1)->birthday->value != '1989-07-21') throw new Exception();
if (\M\UpdateUser::one(2)->birthday->value != '1989-03-19') throw new Exception();
if (\M\UpdateUser::one(3)->birthday->value != '2000-03-29') throw new Exception();

if (\M\UpdateUser::update(['birthday' => '1900-01-01']) != 3) throw new Exception();

$u1 = \M\UpdateUser::one(1);
$u2 = \M\UpdateUser::one(2);
$u3 = \M\UpdateUser::one(3);

if ($u1->birthday->value != '1900-01-01') throw new Exception();
if ($u2->birthday->value != '1900-01-01') throw new Exception();
if ($u3->birthday->value != '1900-01-01') throw new Exception();

$u1->birthday = '1989-07-21';
$u2->birthday = '1989-03-19';
$u3->birthday = '2000-03-29';

if (!($u1->save() && $u2->save() && $u3->save())) throw new Exception();

if (\M\UpdateUser::where('id', '!=', 1)->update(['birthday' => '1900-01-01']) != 2) throw new Exception();

if (\M\UpdateUser::one(1)->birthday->value != '1989-07-21') throw new Exception();
if (\M\UpdateUser::one(2)->birthday->value != '1900-01-01') throw new Exception();
if (\M\UpdateUser::one(3)->birthday->value != '1900-01-01') throw new Exception();

$u1 = \M\UpdateUser::one(1);
if ($u1->info !== ['a' => 'a1', 'b' => [1, 2, 3]]) throw new Exception();
$u1->info = null;
$u1->save();

$u1 = \M\UpdateUser::one(1);
if ($u1->info !== null) throw new Exception();
$u1->info = 'null';
$u1->save();

$u1 = \M\UpdateUser::one(1);
if ($u1->info !== 'null') throw new Exception();
$u1->info = 0;
$u1->save();

$u1 = \M\UpdateUser::one(1);
if ($u1->info !== 0) throw new Exception();

$u2 = \M\UpdateUser::one(2);
$u2->info = ['a' => 'a1', 'b' => [1, 2, 3]];
$u2->save();
$u2 = \M\UpdateUser::one(2);
if ($u2->info !== ['a' => 'a1', 'b' => [1, 2, 3]]) throw new Exception();
\M\UpdateUser::where(2)->update(['info' => null]);

$u2 = \M\UpdateUser::one(2);
if ($u2->info !== null) throw new Exception();
\M\UpdateUser::where(2)->update(['info' => 'null']);
$u2 = \M\UpdateUser::one(2);
if ($u2->info !== 'null') throw new Exception();

\M\UpdateUser::where(2)->update(['info' => 0]);
$u2 = \M\UpdateUser::one(2);
if ($u2->info !== 0) throw new Exception();

$u = \M\UpdateUser::one(1);
if ($u->birthday->value != '1989-07-21') throw new Exception();
$u->birthday = $date = date('Y-m-d');
if (!$u->save()) throw new Exception();

$u = \M\UpdateUser::one(1);
if ($u->birthday->value != $date) throw new Exception();

$u->birthday = \DateTime::createFromFormat('Y-m-d', $date);
if (!$u->save()) throw new Exception(\M\Model::$lastLog);

$u = \M\UpdateUser::one(1);
if ($u->birthday->value != $date) throw new Exception();

$u->birthday = '1989-07-21';
if (!$u->save()) throw new Exception(\M\Model::$lastLog);

$u = \M\UpdateUser::one(1);
if ($u->birthday->value != '1989-07-21') throw new Exception();

$u->birthday = null;
if (!$u->save()) throw new Exception(\M\Model::$lastLog);

$u = \M\UpdateUser::one(1);
if ($u->birthday->value != null) throw new Exception();

