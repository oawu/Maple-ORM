<?php

if (\M\Core\Connection::instance()->query('show tables;', [], $sth)) {
  var_dump('show tables Error');
  exit(1);
}

$tables = [];
foreach ($sth->fetchAll() as $row)
  array_push($tables, array_shift($row));

foreach ($tables as $table) {
  if (\M\Core\Connection::instance()->query('DROP TABLE IF EXISTS `' . $table . '`;')) {
    var_dump('drop table `' . $table . '` Error');
    exit(1);
  }
}

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

$sql = "CREATE TABLE `User` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `a` varchar(190) CHARACTER SET utf8mb4 DEFAULT NULL, `b` text COLLATE utf8mb4_unicode_ci, `c` enum('a') COLLATE utf8mb4_unicode_ci DEFAULT NULL, `d` datetime DEFAULT NULL, `e` timestamp NULL DEFAULT NULL, `f` time DEFAULT NULL, `g` date DEFAULT NULL, `h` float DEFAULT NULL, `i` double DEFAULT NULL, `j` decimal(10,2) DEFAULT NULL, `k` json DEFAULT NULL, `a1` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '', `b1` text COLLATE utf8mb4_unicode_ci NOT NULL, `c1` enum('a','b') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'a', `d1` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `e1` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `f1` time NOT NULL, `g1` date NOT NULL, `h1` float NOT NULL, `i1` double NOT NULL, `j1` decimal(10,2) NOT NULL, `k1` json NOT NULL, `avatar` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `zip` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `updateAt` datetime DEFAULT NULL, `createAt` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
if (\M\Core\Connection::instance()->query($sql)) {
  var_dump('create User table Error');
  exit(1);
}

\M\Model::case(\M\Model::CASE_CAMEL);

function test1BelongToMany1(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToMany1 1');
    count($article->$key) == 1 || GG('test1BelongToMany1 2');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToMany1 3');
    $article = \M\Article::one(2);
    array_shift($article->$key)->name == 'A' || GG('test1BelongToMany1 4');
    
    $article = \M\Article::one(3);
    is_array($article->$key) || GG('test1BelongToMany1 5');
    count($article->$key) == 0 || GG('test1BelongToMany1 6');
  }
}
function test1BelongToOne1(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key !== null || GG('test1BelongToOne1 1');
    $article->$key instanceof \M\Member || GG('test1BelongToOne1 2');
    $article->$key->name == 'A' || GG('test1BelongToOne1 3');
    $article = \M\Article::one(3);
    $article->$key === null || GG('test1BelongToOne1 4');
  }
}
function test1BelongToMany2(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'CCbCC']);
  \M\Member::create(['name' => 'BBaBB']);
  \M\Member::create(['name' => 'AAcAA']);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToMany2 1');
    count($article->$key) == 0 || GG('test1BelongToMany2 2');

    $article = \M\Article::one(1);
    is_array($article->$key) || GG('test1BelongToMany2 3');
    count($article->$key) == 1 || GG('test1BelongToMany2 4');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToMany2 5');
    $article = \M\Article::one(1);
    array_shift($article->$key)->name == 'BBaBB' || GG('test1BelongToMany2 6');
  }
}
function test1BelongToOne2(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'CCbCC']);
  \M\Member::create(['name' => 'BBaBB']);
  \M\Member::create(['name' => 'AAcAA']);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key === null || GG('test1BelongToOne2 1');

    $article = \M\Article::one(1);
    $article->$key !== null || GG('test1BelongToOne2 2');
    $article->$key instanceof \M\Member || GG('test1BelongToOne2 3');
    $article->$key->name == 'BBaBB' || GG('test1BelongToOne2 4');
  }
}
function test1BelongToMany3(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A', 'nick' => '~A']);
  \M\Member::create(['name' => 'B', 'nick' => '~B']);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToMany3 1');
    count($article->$key) == 1 || GG('test1BelongToMany3 2');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToMany3 3');
    $article = \M\Article::one(2);
    !isset(array_shift($article->$key)->name) || GG('test1BelongToMany3 4');
    $article = \M\Article::one(2);
    array_shift($article->$key)->nick == '~A' || GG('test1BelongToMany3 5');
    
    $article = \M\Article::one(3);
    is_array($article->$key) || GG('test1BelongToMany3 6');
    count($article->$key) == 0 || GG('test1BelongToMany3 7');
  }
}
function test1BelongToOne3(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A', 'nick' => '~A']);
  \M\Member::create(['name' => 'B', 'nick' => '~B']);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key !== null || GG('test1BelongToOne3 1');
    $article->$key instanceof \M\Member || GG('test1BelongToOne3 2');
    !isset($article->$key->name) || GG('test1BelongToOne3 3');
    $article->$key->nick == '~A' || GG('test1BelongToOne3 4');

    $article = \M\Article::one(3);
    $article->$key === null || GG('test1BelongToOne3 5');
  }
}
function test1BelongToMany4(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A', 'fakeId' => 2]);
  \M\Member::create(['name' => 'B', 'fakeId' => 1]);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToMany4 1');
    count($article->$key) == 1 || GG('test1BelongToMany4 2');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToMany4 3');
    $article = \M\Article::one(2);
    array_shift($article->$key)->name == 'B' || GG('test1BelongToMany4 4');
    
    $article = \M\Article::one(3);
    is_array($article->$key) || GG('test1BelongToMany4 5');
    count($article->$key) == 0 || GG('test1BelongToMany4 6');
  }
}
function test1BelongToOne4(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A', 'fakeId' => 2]);
  \M\Member::create(['name' => 'B', 'fakeId' => 1]);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key !== null || GG('test1BelongToOne4 1');
    $article->$key instanceof \M\Member || GG('test1BelongToOne4 2');
    $article->$key->name == 'B' || GG('test1BelongToOne4 3');
    $article = \M\Article::one(3);
    $article->$key === null || GG('test1BelongToOne4 4');
  }
}
function test1BelongToMany5(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'CCbCC', 'fakeId' => 2]);
  \M\Member::create(['name' => 'BBaBB', 'fakeId' => 3]);
  \M\Member::create(['name' => 'aacaa', 'fakeId' => 2]);

  foreach ($keys as $key) {
    $article = \M\Article::one(1);
    is_array($article->$key) || GG('test1BelongToMany5 1');
    count($article->$key) == 1 || GG('test1BelongToMany5 2');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToMany5 3');
    $article = \M\Article::one(1);
    array_shift($article->$key)->name == 'aacaa' || GG('test1BelongToMany5 4');

    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToMany5 5');
    count($article->$key) == 0 || GG('test1BelongToMany5 6');
  }
}
function test1BelongToOne5(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'CCbCC', 'fakeId' => 2]);
  \M\Member::create(['name' => 'BBaBB', 'fakeId' => 3]);
  \M\Member::create(['name' => 'aacaa', 'fakeId' => 2]);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key === null || GG('test1BelongToOne5 1');

    $article = \M\Article::one(1);
    $article->$key !== null || GG('test1BelongToOne5 2');
    $article->$key instanceof \M\Member || GG('test1BelongToOne5 3');
    $article->$key->name == 'aacaa' || GG('test1BelongToOne5 4');
  }
}
function test1BelongToMany6(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A', 'nick' => '~A', 'fakeId' => 2]);
  \M\Member::create(['name' => 'B', 'nick' => '~B', 'fakeId' => 1]);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToMany6 1');
    count($article->$key) == 1 || GG('test1BelongToMany6 2');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToMany6 3');
    $article = \M\Article::one(2);
    !isset(array_shift($article->$key)->name) || GG('test1BelongToMany6 4');
    $article = \M\Article::one(2);
    array_shift($article->$key)->nick == '~B' || GG('test1BelongToMany6 5');
    
    $article = \M\Article::one(3);
    is_array($article->$key) || GG('test1BelongToMany6 6');
    count($article->$key) == 0 || GG('test1BelongToMany6 7');
  }
}
function test1BelongToOne6(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A', 'nick' => '~A', 'fakeId' => 2]);
  \M\Member::create(['name' => 'B', 'nick' => '~B', 'fakeId' => 1]);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key !== null || GG('test1BelongToOne6 1');
    $article->$key instanceof \M\Member || GG('test1BelongToOne6 2');
    !isset($article->$key->name) || GG('test1BelongToOne6 3');
    $article->$key->nick == '~B' || GG('test1BelongToOne6 4');

    $article = \M\Article::one(3);
    $article->$key === null || GG('test1BelongToOne6 5');
  }
}
function test1BelongToMany7(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'fakeId' => 1, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'fakeId' => 3, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'fakeId' => 4, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'C']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToMany7 1');
    count($article->$key) == 1 || GG('test1BelongToMany7 2');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToMany7 3');
    $article = \M\Article::one(2);
    array_shift($article->$key)->name == 'B' || GG('test1BelongToMany7 4');
    
    $article = \M\Article::one(3);
    is_array($article->$key) || GG('test1BelongToMany7 5');
    count($article->$key) == 0 || GG('test1BelongToMany7 6');
  }
}
function test1BelongToOne7(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'fakeId' => 1, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'fakeId' => 3, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'fakeId' => 4, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'C']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key !== null || GG('test1BelongToOne7 1');
    $article->$key instanceof \M\Member || GG('test1BelongToOne7 2');
    $article->$key->name == 'B' || GG('test1BelongToOne7 3');
    $article = \M\Article::one(3);
    $article->$key === null || GG('test1BelongToOne7 4');
  }
}
function test1BelongToMany8(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'fakeId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'fakeId' => 5, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'fakeId' => 1, 'title' => 'CCC']);
  \M\Member::create(['name' => 'CCbCC']);
  \M\Member::create(['name' => 'BBaBB']);
  \M\Member::create(['name' => 'aacaa']);

  foreach ($keys as $key) {
    $article = \M\Article::one(1);
    is_array($article->$key) || GG('test1BelongToMany8 1');
    count($article->$key) == 1 || GG('test1BelongToMany8 2');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToMany8 3');
    $article = \M\Article::one(1);
    array_shift($article->$key)->name == 'BBaBB' || GG('test1BelongToMany8 4');

    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToMany8 5');
    count($article->$key) == 0 || GG('test1BelongToMany8 6');
  }
}
function test1BelongToOne8(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'fakeId' => 2, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'fakeId' => 5, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'fakeId' => 1, 'title' => 'CCC']);
  \M\Member::create(['name' => 'CCbCC']);
  \M\Member::create(['name' => 'BBaBB']);
  \M\Member::create(['name' => 'aacaa']);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key === null || GG('test1BelongToOne8 1');

    $article = \M\Article::one(1);
    $article->$key !== null || GG('test1BelongToOne8 2');
    $article->$key instanceof \M\Member || GG('test1BelongToOne8 3');
    $article->$key->name == 'BBaBB' || GG('test1BelongToOne8 4');
  }
}
function test1BelongToMany9(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA', 'fakeId' => 1]);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'fakeId' => 3]);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'fakeId' => 4]);
  \M\Member::create(['name' => 'A', 'nick' => '~A']);
  \M\Member::create(['name' => 'C', 'nick' => '~C']);
  \M\Member::create(['name' => 'B', 'nick' => '~B']);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToMany9 1');
    count($article->$key) == 1 || GG('test1BelongToMany9 2');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToMany9 3');
    $article = \M\Article::one(2);
    !isset(array_shift($article->$key)->name) || GG('test1BelongToMany9 4');
    $article = \M\Article::one(2);
    array_shift($article->$key)->nick == '~B' || GG('test1BelongToMany9 5');
    
    $article = \M\Article::one(3);
    is_array($article->$key) || GG('test1BelongToMany9 6');
    count($article->$key) == 0 || GG('test1BelongToMany9 7');
  }
}
function test1BelongToOne9(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA', 'fakeId' => 1]);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'fakeId' => 3]);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'fakeId' => 4]);
  \M\Member::create(['name' => 'A', 'nick' => '~A']);
  \M\Member::create(['name' => 'C', 'nick' => '~C']);
  \M\Member::create(['name' => 'B', 'nick' => '~B']);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key !== null || GG('test1BelongToOne9 1');
    $article->$key instanceof \M\Member || GG('test1BelongToOne9 2');
    !isset($article->$key->name) || GG('test1BelongToOne9 3');
    $article->$key->nick == '~B' || GG('test1BelongToOne9 4');

    $article = \M\Article::one(3);
    $article->$key === null || GG('test1BelongToOne9 5');
  }
}
function test1BelongToManyA(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'fakeId' => 1, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'fakeId' => 3, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'fakeId' => 4, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A', 'fakeId' => 2]);
  \M\Member::create(['name' => 'C', 'fakeId' => 3]);
  \M\Member::create(['name' => 'B', 'fakeId' => 1]);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToManyA 1');
    count($article->$key) == 1 || GG('test1BelongToManyA 2');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToManyA 3');
    $article = \M\Article::one(2);
    array_shift($article->$key)->name == 'C' || GG('test1BelongToManyA 4');
    
    $article = \M\Article::one(3);
    is_array($article->$key) || GG('test1BelongToManyA 5');
    count($article->$key) == 0 || GG('test1BelongToManyA 6');
  }
}
function test1BelongToOneA(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'fakeId' => 1, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'fakeId' => 3, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'fakeId' => 4, 'title' => 'CCC']);
  \M\Member::create(['name' => 'A', 'fakeId' => 2]);
  \M\Member::create(['name' => 'C', 'fakeId' => 3]);
  \M\Member::create(['name' => 'B', 'fakeId' => 1]);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key !== null || GG('test1BelongToOneA 1');
    $article->$key instanceof \M\Member || GG('test1BelongToOneA 2');
    $article->$key->name == 'C' || GG('test1BelongToOneA 3');
    $article = \M\Article::one(3);
    $article->$key === null || GG('test1BelongToOneA 4');
  }
}
function test1BelongToManyB(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'fakeId' => 1, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'fakeId' => 5, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'fakeId' => 4, 'title' => 'CCC']);
  \M\Member::create(['name' => 'CCbCC', 'fakeId' => 1]);
  \M\Member::create(['name' => 'BBaBB', 'fakeId' => 3]);
  \M\Member::create(['name' => 'aacaa', 'fakeId' => 1]);

  foreach ($keys as $key) {
    $article = \M\Article::one(1);
    is_array($article->$key) || GG('test1BelongToManyB 1');
    count($article->$key) == 1 || GG('test1BelongToManyB 2');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToManyB 3');
    $article = \M\Article::one(1);
    array_shift($article->$key)->name == 'aacaa' || GG('test1BelongToManyB 4');

    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToManyB 5');
    count($article->$key) == 0 || GG('test1BelongToManyB 6');
  }
}
function test1BelongToOneB(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'fakeId' => 1, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'fakeId' => 5, 'title' => 'BBB']);
  \M\Article::create(['memberId' => 3, 'fakeId' => 4, 'title' => 'CCC']);
  \M\Member::create(['name' => 'CCbCC', 'fakeId' => 1]);
  \M\Member::create(['name' => 'BBaBB', 'fakeId' => 3]);
  \M\Member::create(['name' => 'aacaa', 'fakeId' => 1]);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key === null || GG('test1BelongToOneB 1');

    $article = \M\Article::one(1);
    $article->$key !== null || GG('test1BelongToOneB 2');
    $article->$key instanceof \M\Member || GG('test1BelongToOneB 3');
    $article->$key->name == 'aacaa' || GG('test1BelongToOneB 4');
  }
}
function test1BelongToManyC(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA', 'fakeId' => 1]);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'fakeId' => 3]);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'fakeId' => 4]);
  \M\Member::create(['name' => 'A', 'nick' => '~A', 'fakeId' => 2]);
  \M\Member::create(['name' => 'C', 'nick' => '~C', 'fakeId' => 3]);
  \M\Member::create(['name' => 'B', 'nick' => '~B', 'fakeId' => 1]);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    is_array($article->$key) || GG('test1BelongToManyC 1');
    count($article->$key) == 1 || GG('test1BelongToManyC 2');
    array_shift($article->$key) instanceof \M\Member || GG('test1BelongToManyC 3');
    $article = \M\Article::one(2);
    !isset(array_shift($article->$key)->name) || GG('test1BelongToManyC 4');
    $article = \M\Article::one(2);
    array_shift($article->$key)->nick == '~C' || GG('test1BelongToManyC 5');
    
    $article = \M\Article::one(3);
    is_array($article->$key) || GG('test1BelongToManyC 6');
    count($article->$key) == 0 || GG('test1BelongToManyC 7');
  }
}
function test1BelongToOneC(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();

  \M\Article::create(['memberId' => 2, 'title' => 'AAA', 'fakeId' => 1]);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'fakeId' => 3]);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'fakeId' => 4]);
  \M\Member::create(['name' => 'A', 'nick' => '~A', 'fakeId' => 2]);
  \M\Member::create(['name' => 'C', 'nick' => '~C', 'fakeId' => 3]);
  \M\Member::create(['name' => 'B', 'nick' => '~B', 'fakeId' => 1]);

  foreach ($keys as $key) {
    $article = \M\Article::one(2);
    $article->$key !== null || GG('test1BelongToOneC 1');
    $article->$key instanceof \M\Member || GG('test1BelongToOneC 2');
    !isset($article->$key->name) || GG('test1BelongToOneC 3');
    $article->$key->nick == '~C' || GG('test1BelongToOneC 4');

    $article = \M\Article::one(3);
    $article->$key === null || GG('test1BelongToOneC 5');
  }
}

function test1HasMany1(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Article::create(['memberId' => 1, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasMany1 1');
    count($member->$key) == 2 || GG('test1HasMany1 2');
    array_shift($member->$key)->title == 'AAA' || GG('test1HasMany1 3');
    array_shift($member->$key)->title == 'BBB' || GG('test1HasMany1 4');
    
    $member = \M\Member::one(2);
    is_array($member->$key) || GG('test1HasMany1 5');
    count($member->$key) == 0 || GG('test1HasMany1 6');
  }
}
function test1HasOne1(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 3, 'title' => 'CCC']);
  \M\Article::create(['memberId' => 1, 'title' => 'AAA']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB']);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) && GG('test1HasOne1 1');
    $member->$key instanceof \M\Article || GG('test1HasOne1 2');
    $member->$key->title = 'AAA' || GG('test1HasOne1 3');
   
    $member = \M\Member::one(2);
    $member->$key === null || GG('test1HasOne1 4');
  }
}
function test1HasMany2(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 1, 'title' => 'AAA', 'score' => 6]);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'score' => 3]);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'score' => 12]);
  \M\Article::create(['memberId' => 1, 'title' => 'DDD', 'score' => 10]);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasMany2 1');
    count($member->$key) == 2 || GG('test1HasMany2 2');
    array_shift($member->$key)->title == 'AAA' || GG('test1HasMany2 3');
    array_shift($member->$key)->title == 'DDD' || GG('test1HasMany2 4');
    $member = \M\Member::one(2);
    is_array($member->$key) || GG('test1HasMany2 5');
    count($member->$key) == 0 || GG('test1HasMany2 6');
  }
}
function test1HasOne2(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 3, 'title' => 'AAA', 'score' => 10]);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'score' => 2]);
  \M\Article::create(['memberId' => 1, 'title' => 'CCC', 'score' => 10]);
  \M\Article::create(['memberId' => 1, 'title' => 'DDD', 'score' => 12]);

  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) && GG('test1HasOne2 1');
    $member->$key instanceof \M\Article || GG('test1HasOne2 2');
    $member->$key->title = 'CCC' || GG('test1HasOne2 3');
   
    $member = \M\Member::one(2);
    $member->$key === null || GG('test1HasOne2 4');
  }
}
function test1HasMany3(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 1, 'title' => 'AAA', 'content' => 'aa - 6']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'content' => 'aa - 3']);
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'content' => 'aa - 12']);
  \M\Article::create(['memberId' => 1, 'title' => 'DDD', 'content' => 'aa - 10']);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasMany3 1');
    count($member->$key) == 3 || GG('test1HasMany3 2');
    isset(array_shift($member->$key)->title) && GG('test1HasMany3 3');
    isset(array_shift($member->$key)->title) && GG('test1HasMany3 4');
    isset(array_shift($member->$key)->title) && GG('test1HasMany3 5');
    
    $member = \M\Member::one(1);
    array_shift($member->$key)->content == 'aa - 6'  || GG('test1HasMany3 6');
    array_shift($member->$key)->content == 'aa - 3'  || GG('test1HasMany3 7');
    array_shift($member->$key)->content == 'aa - 10' || GG('test1HasMany3 8');
  }
}
function test1HasOne3(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 3, 'title' => 'AAA', 'content' => 'AAA - 1']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'content' => 'BBB - 2']);

  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) && GG('test1HasOne3 1');
    $member->$key instanceof \M\Article || GG('test1HasOne3 2');
    isset($member->$key->title) && GG('test1HasOne3 3');
    $member->$key->content == 'BBB - 2' || GG('test1HasOne3 4');
   
    $member = \M\Member::one(2);
    $member->$key === null || GG('test1HasOne3 5');
  }
}
function test1HasMany4(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'fakeId' => 1]);
  \M\Article::create(['memberId' => 1, 'title' => 'AAA', 'fakeId' => 3]);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'fakeId' => 1]);
  \M\Article::create(['memberId' => 1, 'title' => 'DDD', 'fakeId' => 1]);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasMany4 1');
    count($member->$key) == 3 || GG('test1HasMany4 2');
    array_shift($member->$key)->title == 'CCC' || GG('test1HasMany4 3');
    array_shift($member->$key)->title == 'BBB' || GG('test1HasMany4 4');
    array_shift($member->$key)->title == 'DDD' || GG('test1HasMany4 5');
    
    $member = \M\Member::one(2);
    is_array($member->$key) || GG('test1HasMany4 6');
    count($member->$key) == 0 || GG('test1HasMany4 7');
  }
}
function test1HasOne4(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'fakeId' => 3]);
  \M\Article::create(['memberId' => 1, 'title' => 'AAA', 'fakeId' => 1]);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'fakeId' => 1]);
  \M\Article::create(['memberId' => 1, 'title' => 'DDD', 'fakeId' => 1]);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) && GG('test1HasOne4 1');
    $member->$key instanceof \M\Article || GG('test1HasOne4 2');
    $member->$key->title == 'AAA' || GG('test1HasOne4 3');
    $member = \M\Member::one(2);
    $member->$key === null || GG('test1HasOne3 4');
  }
}
function test1HasMany5(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'fakeId' => 1, 'score' => 2]);
  \M\Article::create(['memberId' => 1, 'title' => 'AAA', 'fakeId' => 3, 'score' => 10]);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'fakeId' => 1, 'score' => 2]);
  \M\Article::create(['memberId' => 1, 'title' => 'DDD', 'fakeId' => 1, 'score' => 13]);
  \M\Article::create(['memberId' => 1, 'title' => 'EEE', 'fakeId' => 1, 'score' => 4]);
  \M\Article::create(['memberId' => 1, 'title' => 'FFF', 'fakeId' => 1, 'score' => 20]);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasMany5 1');
    count($member->$key) == 2 || GG('test1HasMany5 2');

    array_shift($member->$key)->title == 'DDD' || GG('test1HasMany5 3');
    array_shift($member->$key)->title == 'FFF' || GG('test1HasMany5 4');
    
    $member = \M\Member::one(2);
    is_array($member->$key) || GG('test1HasMany5 5');
    count($member->$key) == 0 || GG('test1HasMany5 6');
  }
}
function test1HasOne5(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'fakeId' => 1, 'score' => 2]);
  \M\Article::create(['memberId' => 1, 'title' => 'AAA', 'fakeId' => 3, 'score' => 10]);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'fakeId' => 1, 'score' => 2]);
  \M\Article::create(['memberId' => 1, 'title' => 'DDD', 'fakeId' => 1, 'score' => 13]);
  \M\Article::create(['memberId' => 1, 'title' => 'EEE', 'fakeId' => 1, 'score' => 4]);
  \M\Article::create(['memberId' => 1, 'title' => 'FFF', 'fakeId' => 1, 'score' => 20]);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) && GG('test1HasOne5 1');
    $member->$key instanceof \M\Article || GG('test1HasOne5 2');
    $member->$key->title == 'DDD' || GG('test1HasOne5 3');

    $member = \M\Member::one(2);
    $member->$key === null || GG('test1HasOne5 4');
  }
}
function test1HasMany6(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'fakeId' => 3, 'content' => 'CCC - 2']);
  \M\Article::create(['memberId' => 1, 'title' => 'AAA', 'fakeId' => 1, 'content' => 'AAA - 10']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'fakeId' => 1, 'content' => 'BBB - 2']);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasMany6 1');
    count($member->$key) == 2 || GG('test1HasMany6 2');
    array_shift($member->$key) instanceof \M\Article || GG('test1HasMany6 3');
    array_shift($member->$key) instanceof \M\Article || GG('test1HasMany6 4');

    $member = \M\Member::one(1);
    isset(array_shift($member->$key)->title) && GG('test1HasMany6 5');
    isset(array_shift($member->$key)->title) && GG('test1HasMany6 6');

    $member = \M\Member::one(1);
    array_shift($member->$key)->content == 'AAA - 10' || GG('test1HasMany6 7');
    array_shift($member->$key)->content == 'BBB - 2' || GG('test1HasMany6 8');

    $member = \M\Member::one(2);
    is_array($member->$key) || GG('test1HasMany6 9');
    count($member->$key) == 0 || GG('test1HasMany6 10');
  }
}
function test1HasOne6(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['memberId' => 3, 'title' => 'CCC', 'fakeId' => 3, 'content' => 'CCC - 2']);
  \M\Article::create(['memberId' => 1, 'title' => 'AAA', 'fakeId' => 1, 'content' => 'AAA - 10']);
  \M\Article::create(['memberId' => 1, 'title' => 'BBB', 'fakeId' => 1, 'content' => 'BBB - 2']);
  \M\Member::create(['name' => 'A']);
  \M\Member::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    $member->$key !== null || GG('test1HasOne6 1');
    $member->$key instanceof \M\Article || GG('test1HasOne6 2');
    isset($member->$key->title) && GG('test1HasOne6 3');
    $member->$key->content == 'AAA - 10' || GG('test1HasOne6 4');

    $member = \M\Member::one(2);
    $member->$key === null || GG('test1HasOne6 5');
  }
}
function test1HasMany7(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'CCC', 'memberId' => 4]);
  \M\Article::create(['title' => 'AAA', 'memberId' => 10]);
  \M\Article::create(['title' => 'BBB', 'memberId' => 10]);
  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 10]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasMany7 1');
    count($member->$key) == 0 || GG('test1HasMany7 2');

    $member = \M\Member::one(2);
    is_array($member->$key) || GG('test1HasMany7 3');
    count($member->$key) == 2 || GG('test1HasMany7 4');
    array_shift($member->$key) instanceof \M\Article || GG('test1HasMany7 5');
    array_shift($member->$key) instanceof \M\Article || GG('test1HasMany7 6');

    $member = \M\Member::one(2);
    array_shift($member->$key)->title == 'AAA' || GG('test1HasMany7 7');
    array_shift($member->$key)->title == 'BBB' || GG('test1HasMany7 8');

    $member = \M\Member::one(3);
    is_array($member->$key) || GG('test1HasMany7 9');
    count($member->$key) == 0 || GG('test1HasMany7 10');
  }
}
function test1HasOne7(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'CCC', 'memberId' => 4]);
  \M\Article::create(['title' => 'AAA', 'memberId' => 10]);
  \M\Article::create(['title' => 'BBB', 'memberId' => 10]);
  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 10]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    $member->$key === null || GG('test1HasOne7 2');

    $member = \M\Member::one(2);
    $member->$key !== null || GG('test1HasOne7 3');
    $member->$key instanceof \M\Article || GG('test1HasOne7 4');
    $member->$key->title == 'AAA' || GG('test1HasOne7 5');

    $member = \M\Member::one(3);
    $member->$key === null || GG('test1HasOne7 6');
  }
}
function test1HasMany8(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'CCC', 'memberId' => 4, 'score' => 10]);
  \M\Article::create(['title' => 'AAA', 'memberId' => 20, 'score' => 3]);
  \M\Article::create(['title' => 'BBB', 'memberId' => 20, 'score' => 12]);
  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 20]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasMany8 1');
    count($member->$key) == 0 || GG('test1HasMany8 2');

    $member = \M\Member::one(2);
    is_array($member->$key) || GG('test1HasMany8 3');
    count($member->$key) == 1 || GG('test1HasMany8 4');
    array_shift($member->$key) instanceof \M\Article || GG('test1HasMany8 5');

    $member = \M\Member::one(2);
    array_shift($member->$key)->title == 'BBB' || GG('test1HasMany8 6');

    $member = \M\Member::one(3);
    is_array($member->$key) || GG('test1HasMany8 7');
    count($member->$key) == 0 || GG('test1HasMany8 8');
  }
}
function test1HasOne8(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'CCC', 'memberId' => 4, 'score' => 10]);
  \M\Article::create(['title' => 'AAA', 'memberId' => 20, 'score' => 3]);
  \M\Article::create(['title' => 'BBB', 'memberId' => 20, 'score' => 12]);
  \M\Article::create(['title' => 'DDD', 'memberId' => 20, 'score' => 17]);

  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 20]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    $member->$key === null || GG('test1HasOne8 2');

    $member = \M\Member::one(2);
    $member->$key instanceof \M\Article || GG('test1HasOne8 3');
    $member->$key->title == 'BBB' || GG('test1HasOne8 4');

    $member = \M\Member::one(3);
    $member->$key === null || GG('test1HasOne8 4');
  }
}
function test1HasMany9(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'CCC', 'memberId' => 4, 'content' => 'CCC 10']);
  \M\Article::create(['title' => 'AAA', 'memberId' => 20, 'content' => 'AAA 3']);
  \M\Article::create(['title' => 'BBB', 'memberId' => 20, 'content' => 'BBB 12']);
  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 20]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasMany9 1');
    count($member->$key) == 0 || GG('test1HasMany9 2');

    $member = \M\Member::one(2);
    is_array($member->$key) || GG('test1HasMany9 3');
    count($member->$key) == 2 || GG('test1HasMany9 4');
    array_shift($member->$key) instanceof \M\Article || GG('test1HasMany9 5');
    array_shift($member->$key) instanceof \M\Article || GG('test1HasMany9 6');

    $member = \M\Member::one(2);
    isset(array_shift($member->$key)->title) && GG('test1HasMany9 7');
    isset(array_shift($member->$key)->title) && GG('test1HasMany9 8');

    $member = \M\Member::one(2);
    array_shift($member->$key)->content = 'AAA 3' || GG('test1HasMany9 9');
    array_shift($member->$key)->content = 'BBB 12' || GG('test1HasMany9 10');

    $member = \M\Member::one(3);
    is_array($member->$key) || GG('test1HasMany9 11');
    count($member->$key) == 0 || GG('test1HasMany9 12');
  }
}
function test1HasOne9(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'CCC', 'memberId' => 4, 'content' => 'CCC - 10']);
  \M\Article::create(['title' => 'AAA', 'memberId' => 20, 'content' => 'AAA - 3']);
  \M\Article::create(['title' => 'BBB', 'memberId' => 20, 'content' => 'BBB - 12']);
  \M\Article::create(['title' => 'DDD', 'memberId' => 20, 'content' => 'DDD - 17']);

  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 20]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    $member->$key === null || GG('test1HasOne9 2');

    $member = \M\Member::one(2);
    $member->$key instanceof \M\Article || GG('test1HasOne9 3');
    isset($member->$key->title) && GG('test1HasOne9 4');
    $member->$key->content == 'AAA - 3' || GG('test1HasOne9 5');

    $member = \M\Member::one(3);
    $member->$key === null || GG('test1HasOne9 4');
  }
}
function test1HasManyA(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'CCC', 'memberId' => 4, 'fakeId' => 10]);
  \M\Article::create(['title' => 'AAA', 'memberId' => 10, 'fakeId' => 10]);
  \M\Article::create(['title' => 'BBB', 'memberId' => 10, 'fakeId' => 3]);
  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 10]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasManyA 1');
    count($member->$key) == 0 || GG('test1HasManyA 2');

    $member = \M\Member::one(2);
    is_array($member->$key) || GG('test1HasManyA 3');
    count($member->$key) == 2 || GG('test1HasManyA 4');
    array_shift($member->$key) instanceof \M\Article || GG('test1HasManyA 5');
    array_shift($member->$key) instanceof \M\Article || GG('test1HasManyA 6');

    $member = \M\Member::one(2);
    array_shift($member->$key)->title == 'CCC' || GG('test1HasManyA 7');
    array_shift($member->$key)->title == 'AAA' || GG('test1HasManyA 8');

    $member = \M\Member::one(3);
    is_array($member->$key) || GG('test1HasManyA 9');
    count($member->$key) == 0 || GG('test1HasManyA 10');
  }
}
function test1HasOneA(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'DDD', 'memberId' => 4, 'fakeId' => 2]);
  \M\Article::create(['title' => 'CCC', 'memberId' => 4, 'fakeId' => 10]);
  \M\Article::create(['title' => 'AAA', 'memberId' => 10, 'fakeId' => 10]);
  \M\Article::create(['title' => 'BBB', 'memberId' => 10, 'fakeId' => 3]);
  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 10]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    $member->$key === null || GG('test1HasOneA 2');

    $member = \M\Member::one(2);
    $member->$key !== null || GG('test1HasOneA 3');
    $member->$key instanceof \M\Article || GG('test1HasOneA 4');
    $member->$key->title == 'CCC' || GG('test1HasOneA 5');

    $member = \M\Member::one(3);
    $member->$key === null || GG('test1HasOneA 6');
  }
}
function test1HasManyB(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'DDD', 'memberId' => 4, 'score' => 10, 'fakeId' => 52]);
  \M\Article::create(['title' => 'CCC', 'memberId' => 4, 'score' => 2, 'fakeId' => 20]);
  \M\Article::create(['title' => 'AAA', 'memberId' => 20, 'score' => 10, 'fakeId' => 20]);
  \M\Article::create(['title' => 'BBB', 'memberId' => 20, 'score' => 12, 'fakeId' => 52]);
  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 20]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasManyB 1');
    count($member->$key) == 0 || GG('test1HasManyB 2');

    $member = \M\Member::one(2);
    is_array($member->$key) || GG('test1HasManyB 3');
    count($member->$key) == 1 || GG('test1HasManyB 4');
    array_shift($member->$key) instanceof \M\Article || GG('test1HasManyB 5');

    $member = \M\Member::one(2);
    array_shift($member->$key)->title == 'AAA' || GG('test1HasManyB 6');

    $member = \M\Member::one(3);
    is_array($member->$key) || GG('test1HasManyB 7');
    count($member->$key) == 0 || GG('test1HasManyB 8');
  }
}
function test1HasOneB(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'DDD', 'memberId' => 4, 'score' => 10, 'fakeId' => 52]);
  \M\Article::create(['title' => 'CCC', 'memberId' => 4, 'score' => 2, 'fakeId' => 20]);
  \M\Article::create(['title' => 'AAA', 'memberId' => 20, 'score' => 10, 'fakeId' => 20]);
  \M\Article::create(['title' => 'BBB', 'memberId' => 20, 'score' => 12, 'fakeId' => 52]);
  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 20]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    $member->$key === null || GG('test1HasOneB 2');

    $member = \M\Member::one(2);
    $member->$key instanceof \M\Article || GG('test1HasOneB 3');
    $member->$key->title == 'AAA' || GG('test1HasOneB 4');

    $member = \M\Member::one(3);
    $member->$key === null || GG('test1HasOneB 5');
  }
}
function test1HasManyC(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'CCC', 'memberId' => 4, 'content' => 'CCC 10', 'fakeId' => 33]);
  \M\Article::create(['title' => 'AAA', 'memberId' => 20, 'content' => 'AAA 3', 'fakeId' => 20]);
  \M\Article::create(['title' => 'BBB', 'memberId' => 20, 'content' => 'BBB 12', 'fakeId' => 21]);
  \M\Article::create(['title' => 'DDD', 'memberId' => 20, 'content' => 'DDD 12', 'fakeId' => 21]);
  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 20]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    is_array($member->$key) || GG('test1HasManyC 1');
    count($member->$key) == 0 || GG('test1HasManyC 2');

    $member = \M\Member::one(2);
    is_array($member->$key) || GG('test1HasManyC 3');
    count($member->$key) == 1 || GG('test1HasManyC 4');
    array_shift($member->$key) instanceof \M\Article || GG('test1HasManyC 5');

    $member = \M\Member::one(2);
    isset(array_shift($member->$key)->title) && GG('test1HasManyC 6');

    $member = \M\Member::one(2);
    array_shift($member->$key)->content = 'AAA 3' || GG('test1HasManyC 7');

    $member = \M\Member::one(3);
    is_array($member->$key) || GG('test1HasManyC 8');
    count($member->$key) == 0 || GG('test1HasManyC 9');
  }
}
function test1HasOneC(...$keys) {
  \M\Article::truncate();
  \M\Member::truncate();
  \M\Article::create(['title' => 'CCC', 'memberId' => 4, 'content' => 'CCC - 10', 'fakeId' => 201]);
  \M\Article::create(['title' => 'AAA', 'memberId' => 20, 'content' => 'AAA - 3', 'fakeId' => 20]);
  \M\Article::create(['title' => 'BBB', 'memberId' => 20, 'content' => 'BBB - 12', 'fakeId' => 20]);
  \M\Article::create(['title' => 'DDD', 'memberId' => 20, 'content' => 'DDD - 17', 'fakeId' => 203]);

  \M\Member::create(['name' => 'A', 'fakeId' => 5]);
  \M\Member::create(['name' => 'A', 'fakeId' => 20]);
  \M\Member::create(['name' => 'B', 'fakeId' => 6]);

  foreach ($keys as $key) {
    $member = \M\Member::one(1);
    $member->$key === null || GG('test1HasOneC 2');

    $member = \M\Member::one(2);
    $member->$key instanceof \M\Article || GG('test1HasOneC 3');
    isset($member->$key->title) && GG('test1HasOneC 4');
    $member->$key->content == 'AAA - 3' || GG('test1HasOneC 5');

    $member = \M\Member::one(3);
    $member->$key === null || GG('test1HasOneC 6');
  }
}

function test1Relation() {
  test1BelongToMany1('b1_01', 'b1_02', 'b1_03', 'b1_04', 'b1_05', 'b1_06', 'b1_07', 'b1_08');
  test1BelongToOne1('b1_09', 'b1_11', 'b1_12', 'b1_13', 'b1_14', 'b1_15', 'b1_16', 'b1_17');
  test1BelongToMany2('b1_18', 'b1_19', 'b1_21', 'b1_22', 'b1_23', 'b1_24', 'b1_25', 'b1_26');
  test1BelongToOne2('b1_27', 'b1_28', 'b1_29', 'b1_30', 'b1_31', 'b1_32', 'b1_33', 'b1_34');
  test1BelongToMany3('b1_35', 'b1_36', 'b1_37', 'b1_38');
  test1BelongToOne3('b1_39', 'b1_40', 'b1_41', 'b1_42');
  
  test1BelongToMany4('b2_01', 'b2_02', 'b2_03', 'b2_04');
  test1BelongToOne4('b2_05', 'b2_06', 'b2_07', 'b2_08');
  test1BelongToMany5('b2_09', 'b2_10', 'b2_11', 'b2_12');
  test1BelongToOne5('b2_13', 'b2_14', 'b2_15', 'b2_16');
  test1BelongToMany6('b2_17', 'b2_18');
  test1BelongToOne6('b2_19', 'b2_20');

  test1BelongToMany7('b3_01', 'b3_02', 'b3_03', 'b3_04');
  test1BelongToOne7('b3_05', 'b3_06', 'b3_07', 'b3_08');
  test1BelongToMany8('b3_09', 'b3_10', 'b3_11', 'b3_12');
  test1BelongToOne8('b3_13', 'b3_14', 'b3_15', 'b3_16');
  test1BelongToMany9('b3_17', 'b3_18');
  test1BelongToOne9('b3_19', 'b3_20');

  test1BelongToManyA('b4_01', 'b4_02');
  test1BelongToOneA('b4_03', 'b4_04');
  test1BelongToManyB('b4_05', 'b4_06');
  test1BelongToOneB('b4_07', 'b4_08');
  test1BelongToManyC('b4_09');
  test1BelongToOneC('b4_10');

  test1HasMany1('a1_01', 'a1_02', 'a1_03', 'a1_04', 'a1_05', 'a1_06', 'a1_07', 'a1_08', 'a1_09', 'a1_10');
  test1HasOne1('a1_11', 'a1_12', 'a1_13', 'a1_14', 'a1_15', 'a1_16', 'a1_17', 'a1_18');
  test1HasMany2('a1_19', 'a1_20', 'a1_21', 'a1_22', 'a1_23', 'a1_24', 'a1_25', 'a1_26');
  test1HasOne2('a1_27', 'a1_28', 'a1_29', 'a1_30', 'a1_31', 'a1_32', 'a1_33', 'a1_34');
  test1HasMany3('a1_35', 'a1_36', 'a1_37', 'a1_38');
  test1HasOne3('a1_39', 'a1_40', 'a1_41', 'a1_42');
  
  test1HasMany4('a2_01', 'a2_02', 'a2_03', 'a2_04', 'a2_05');
  test1HasOne4('a2_06', 'a2_07', 'a2_08', 'a2_09');
  test1HasMany5('a2_10', 'a2_11', 'a2_12', 'a2_13');
  test1HasOne5('a2_14', 'a2_15', 'a2_16', 'a2_17');
  test1HasMany6('a2_18', 'a2_19');
  test1HasOne6('a2_20', 'a2_21');

  test1HasMany7('a3_01', 'a3_02', 'a3_03', 'a3_04');
  test1HasOne7('a3_05', 'a3_06', 'a3_07', 'a3_08');
  test1HasMany8('a3_09', 'a3_10', 'a3_11', 'a3_12');
  test1HasOne8('a3_13', 'a3_14', 'a3_15', 'a3_16');
  test1HasMany9('a3_17', 'a3_18');
  test1HasOne9('a3_19', 'a3_20');

  test1HasManyA('a4_01', 'a4_02');
  test1HasOneA('a4_03', 'a4_04');

  test1HasManyB('a4_05', 'a4_06');
  test1HasOneB('a4_07', 'a4_08');
  test1HasManyC('a4_09');
  test1HasOneC('a4_10');
}

echo "test1Relation";
test1Relation();
echo " - ok\n";
