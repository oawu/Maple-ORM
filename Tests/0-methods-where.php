<?php

include '_.php';

\M\Model::case(\M\Model::CASE_CAMEL);

$sql = "CREATE TABLE `Article` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `memberId` int(10) unsigned NOT NULL DEFAULT '0', `title` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '', `fakeId` int(11) DEFAULT NULL, `score` int(11) DEFAULT NULL, `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `content` text COLLATE utf8mb4_unicode_ci, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
if (\M\Core\Connection::instance()->query($sql)) {
  var_dump('create Article table Error');
  exit(1);
}

$sql = "CREATE TABLE `Member` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `fakeId` int(11) DEFAULT NULL, `nick` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `name` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
if (\M\Core\Connection::instance()->query($sql)) {
  var_dump('create Member table Error');
  exit(1);
}

$sql = "CREATE TABLE `User` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `a` varchar(190) CHARACTER SET utf8mb4 DEFAULT NULL, `b` text COLLATE utf8mb4_unicode_ci, `c` enum('a') COLLATE utf8mb4_unicode_ci DEFAULT NULL, `d` datetime DEFAULT NULL, `e` timestamp NULL DEFAULT NULL, `f` time DEFAULT NULL, `g` date DEFAULT NULL, `h` float DEFAULT NULL, `i` double DEFAULT NULL, `j` decimal(10,2) DEFAULT NULL, `k` json DEFAULT NULL, `a1` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '', `b1` text COLLATE utf8mb4_unicode_ci NOT NULL, `c1` enum('a','b') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'a', `d1` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `e1` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `f1` time NOT NULL, `g1` date NOT NULL, `h1` float NOT NULL, `i1` double NOT NULL, `j1` decimal(10,2) NOT NULL, `k1` json NOT NULL, `avatar` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `zip` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `updateAt` datetime DEFAULT NULL, `createAt` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
if (\M\Core\Connection::instance()->query($sql)) {
  var_dump('create User table Error');
  exit(1);
}


function test1Feature() {
  $user = \M\User::truncate();
  $user || QQ('Err truncate');

  $user = \M\User::create(['a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => []]);
  $user || QQ('Err create');

  $user = \M\User::one(1);
  $user || QQ('Err one 1');

  $user->b = 'aaaaaaa';
  $user->save() || QQ('Err save');

  $user = \M\User::one(1);
  $user->b == 'aaaaaaa' || QQ('Err save');

  $user->delete() || QQ('Err delete');

  $user = \M\User::creates([[
    'a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => [], ],[
    'a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => [], ],[
    'a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => [],
  ]]);
  $user || QQ('Err creates');

  \M\User::deleteAll('id <= ?', 3) || QQ('Err delete < 3');

  $user = \M\User::creates([[
    'a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => [], ],[
    'a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => [], ],[
    'a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => [], ],[
    'a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => [],
  ]]);

  \M\User::where()->delete(8) || QQ('Err delete 8');

  $user = \M\User::last();
  $user || QQ('Err last');
  $user->id == 7 || QQ('Err 7');

  \M\User::where('id = ? or id = 5', 6)->delete() || QQ('Err delete 5,6');

  $users = \M\User::all();
  $users = array_map(function($user) { return $user->id; }, $users);
  count($users) == 2 || QQ('Err all count 2');

  array_shift($users) == 4 || QQ('Err all id 4');
  array_shift($users) == 7 || QQ('Err all id 7');

  \M\User::where(1)->first() && QQ('Err where first 1');
  \M\User::where(4)->first() || QQ('Err where first 4');
  \M\User::where(1)->one()   && QQ('Err where one 1');
  \M\User::where(4)->one()   || QQ('Err where one 4');
  \M\User::where(1)->last()  && QQ('Err where last 1');
  \M\User::where(7)->last()  || QQ('Err where last 7');

  $users = \M\User::where()->all();
  $users = array_map(function($user) { return $user->id; }, $users);
  count($users) == 2 || QQ('Err where all count 2');
  array_shift($users) == 4 || QQ('Err where all id 4');
  array_shift($users) == 7 || QQ('Err where all id 7');

  $users = \M\User::where()->all(4);
  $users = array_map(function($user) { return $user->id; }, $users);
  count($users) == 1 || QQ('Err where 4 all count 1');
  array_shift($users) == 4 || QQ('Err where 4 all id 4');

  \M\User::count() == 2 || QQ('Err Count 2');

  $user = \M\User::creates([[
    'a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => [], ],[
    'a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => [], ],[
    'a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => [], ],[
    'a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => [],
  ]]);

  \M\User::count('id >= 7 AND id < ?', 11) == 3 || QQ('Err Count 3');
  \M\User::count(7) == 1 || QQ('Err Count 1');
  \M\User::count(8) == 0 || QQ('Err Count 0');
  \M\User::count(\M\User::where(7)) == 1 || QQ('Err Count 2');

  \M\User::where('id >= 7 AND id < ?', 11)->count() == 3 || QQ('Err Where Count 3');
  \M\User::where()->count(7) == 1 || QQ('Err Where Count 1');
  \M\User::where()->count(8) == 0 || QQ('Err Where Count 0');
  \M\User::where()->count(\M\User::where(7)) == 1 || QQ('Err Where Count 1');

  \M\User::updateAll([
    'a' => '456',
    'k' => [1,2,3],
    'k1' => [1,2,3],
    'createAt' => new DateTime("2020-10-22 12:12:12")
  ], 'id IN (?)', [7, 8, 9]) || QQ('Err updateAll');

  $user = \M\User::one(7);
  $user || QQ('Err one 7');
  $user->a == '456' || QQ('Err 456');
  is_array($user->k) || QQ('Err array');
  count($user->k) == 3 || QQ('Err array');
  is_array($user->k1) || QQ('Err array');
  count($user->k1) == 3 || QQ('Err array');

  \M\User::one(8) && QQ('Err one 8');

  $user = \M\User::one(9);
  $user || QQ('Err one 9');
  $user->a == '456' || QQ('Err 9 456');
  is_array($user->k) || QQ('Err 9 array');
  count($user->k) == 3 || QQ('Err 9 array');
  is_array($user->k1) || QQ('Err 9 array');
  count($user->k1) == 3 || QQ('Err 9 array');

  \M\User::where([7, 8, 9])
    ->update([
      'a' => '789',
      'k' => [4, 5, 6, 7],
      'k1' => [4, 5, 6, 7],
      'createAt' => new DateTime("2010-12-22 12:12:12")
    ]);

  $user = \M\User::one(7);
  $user || QQ('Err one 7');
  $user->a == '789' || QQ('Err 789');
  is_array($user->k) || QQ('Err array');
  count($user->k) == 4 || QQ('Err array');
  is_array($user->k1) || QQ('Err array');
  count($user->k1) == 4 || QQ('Err array');

  \M\User::one(8) && QQ('Err one 8');

  $user = \M\User::one(9);
  $user || QQ('Err one 9');
  $user->a == '789' || QQ('Err 9 789');
  is_array($user->k) || QQ('Err 9 array');
  count($user->k) == 4 || QQ('Err 9 array');
  is_array($user->k1) || QQ('Err 9 array');
  count($user->k1) == 4 || QQ('Err 9 array');

  \M\User::closeDB();
}

echo "test1Feature";
test1Feature();
echo " - ok\n";
