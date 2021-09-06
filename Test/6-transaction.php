<?php

include '0.php';

$sql = "CREATE TABLE `TransactionUser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(190) CHARACTER SET utf8mb4 NOT NULL,
  `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($error = \M\Core\Connection::instance()->query($sql)) throw new Exception($error);

$error = null;
$user = \M\transaction(function() { return \M\TransactionUser::create() ?: \M\rollback(); }, $error);
if (!($error && $error[0] == '「name」欄位不可以為 NULL')) throw new Exception();

$error = null;
$user = \M\transaction(function() { return \M\TransactionUser::create(['name' => 'OA']) ?: \M\rollback(); }, $error);
if (!($error === null && $user)) throw new Exception();

$error = null;
$user = \M\transaction(function() {
  \M\TransactionUser::create(['name' => 'OA']);
  throw new \Exception('Error');
}, $error);
if (!($error && $error[0] == 'Error')) throw new Exception();

$error = null;
$user = \M\transaction(function() { return !\M\TransactionUser::create(['name' => 'OA']); }, $error);
if (!($error && $error[0] == 'transaction 回傳 false，故 rollback')) throw new Exception();

$error = null;
$user = \M\transaction(function() { return \M\TransactionUser::create(['name' => 'OB']) ?: \M\rollback(); }, $error);

if (!($error === null && $user)) throw new Exception();
if (\M\TransactionUser::count() != 2) throw new Exception();
if (\M\TransactionUser::one()->id != 1) throw new Exception();
if (\M\TransactionUser::last()->id != 4) throw new Exception();
