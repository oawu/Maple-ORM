<?php

namespace M {
  class User9 extends Model {
    static $tableName = 'User';
  }
}

namespace {
  include '_.php';

  \M\Model::case(\M\Model::CASE_CAMEL);

  $sql = "CREATE TABLE `User` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `name` varchar(190) CHARACTER SET utf8mb4 DEFAULT NULL, `age` int(10) DEFAULT NULL, `updateAt` datetime DEFAULT NULL, `createAt` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
  if (\M\Core\Connection::instance()->query($sql)) {
    var_dump('create User table Error');
    exit(1);
  }
  
  echo "transaction Test";

  $result = \M\transaction(function() {
    $user = \M\User9::create([
      'name' => 'OA'
    ]);

    return false;
  });
  \M\User9::count() == 0 || exit('Err');

  $result = \M\transaction(function() {
    $user = \M\User9::create([
      'name' => 'OA'
    ]);

    return true;
  });
  \M\User9::count() == 1 || exit('Err');
  \M\User9::one()->id == 2 || exit('Err');

  echo " - ok\n";
}
