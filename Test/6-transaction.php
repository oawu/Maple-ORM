<?php

use \Orm\Core\Connection;

include '0.php';

$sql = "CREATE TABLE `TransactionUser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(190) CHARACTER SET utf8mb4 NOT NULL,
  `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($error = Connection::instance()->runQuery($sql)) {
  throw $error;
}

title('rollback（create 失敗）');
$errors = null;
$user = \Orm\Helper::transaction(null, static function () {
  return \Model\Transaction\User::create() ?: \Orm\Helper::rollback();
}, $errors);
check($user === null);
check($errors && $errors[0] == '實體 Model 時發生錯誤，錯誤原因：欄位「name」不可以為 NULL');

title('commit（create 成功）');
$errors = null;
$user = \Orm\Helper::transaction(null, static function () {
  return \Model\Transaction\User::create(['name' => 'OA']) ?: \Orm\Helper::rollback();
}, $errors);
check($user !== null);
check($errors === null);

title('rollback（Exception）');
$errors = null;
$user = \Orm\Helper::transaction(null, static function () {
  \Model\Transaction\User::create(['name' => 'OA']);
  throw new \Exception('Error');
}, $errors);
check($user === null);
check($errors && $errors[0] == 'Error');

title('rollback（回傳 false）');
$errors = null;
$user = \Orm\Helper::transaction(null, static function () {
  return !\Model\Transaction\User::create(['name' => 'OA']);
}, $errors);
check($errors && $errors[0] == 'transaction 回傳 false，故 rollback');

title('驗證最終資料');
$errors = null;
$user = \Orm\Helper::transaction(null, static function () {
  return \Model\Transaction\User::create(['name' => 'OB']) ?: \Orm\Helper::rollback();
}, $errors);
check($user !== null);
check($errors === null);
check(\Model\Transaction\User::count() == 2);
check(\Model\Transaction\User::one()->id == 1);
check(\Model\Transaction\User::last()->id == 4);
