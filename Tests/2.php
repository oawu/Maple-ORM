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

$sql = "CREATE TABLE `books` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `owner_id` int(10) unsigned NOT NULL DEFAULT '0', `title` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '', `fake_id` int(11) DEFAULT NULL, `score` int(11) DEFAULT NULL, `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `content` text COLLATE utf8mb4_unicode_ci, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
if (\M\Core\Connection::instance()->query($sql)) {
  var_dump('create books table Error');
  exit(1);
}

$sql = "CREATE TABLE `owners` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `fake_id` int(11) DEFAULT NULL, `nick` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `name` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
if (\M\Core\Connection::instance()->query($sql)) {
  var_dump('create owners table Error');
  exit(1);
}

\M\Model::case(\M\Model::CASE_SNAKE);

function test2BelongToMany1(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToMany1 1');
    count($article->$key) == 1 || GG('test2BelongToMany1 2');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToMany1 3');
    $article = \M\Book::one(2);
    array_shift($article->$key)->name == 'A' || GG('test2BelongToMany1 4');
    
    $article = \M\Book::one(3);
    is_array($article->$key) || GG('test2BelongToMany1 5');
    count($article->$key) == 0 || GG('test2BelongToMany1 6');
  }
}
function test2BelongToOne1(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key !== null || GG('test2BelongToOne1 1');
    $article->$key instanceof \M\Owner || GG('test2BelongToOne1 2');
    $article->$key->name == 'A' || GG('test2BelongToOne1 3');
    $article = \M\Book::one(3);
    $article->$key === null || GG('test2BelongToOne1 4');
  }
}
function test2BelongToMany2(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'CCbCC']);
  \M\Owner::create(['name' => 'BBaBB']);
  \M\Owner::create(['name' => 'AAcAA']);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToMany2 1');
    count($article->$key) == 0 || GG('test2BelongToMany2 2');

    $article = \M\Book::one(1);
    is_array($article->$key) || GG('test2BelongToMany2 3');
    count($article->$key) == 1 || GG('test2BelongToMany2 4');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToMany2 5');
    $article = \M\Book::one(1);
    array_shift($article->$key)->name == 'BBaBB' || GG('test2BelongToMany2 6');
  }
}
function test2BelongToOne2(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'CCbCC']);
  \M\Owner::create(['name' => 'BBaBB']);
  \M\Owner::create(['name' => 'AAcAA']);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key === null || GG('test2BelongToOne2 1');

    $article = \M\Book::one(1);
    $article->$key !== null || GG('test2BelongToOne2 2');
    $article->$key instanceof \M\Owner || GG('test2BelongToOne2 3');
    $article->$key->name == 'BBaBB' || GG('test2BelongToOne2 4');
  }
}
function test2BelongToMany3(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A', 'nick' => '~A']);
  \M\Owner::create(['name' => 'B', 'nick' => '~B']);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToMany3 1');
    count($article->$key) == 1 || GG('test2BelongToMany3 2');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToMany3 3');
    $article = \M\Book::one(2);
    !isset(array_shift($article->$key)->name) || GG('test2BelongToMany3 4');
    $article = \M\Book::one(2);
    array_shift($article->$key)->nick == '~A' || GG('test2BelongToMany3 5');
    
    $article = \M\Book::one(3);
    is_array($article->$key) || GG('test2BelongToMany3 6');
    count($article->$key) == 0 || GG('test2BelongToMany3 7');
  }
}
function test2BelongToOne3(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A', 'nick' => '~A']);
  \M\Owner::create(['name' => 'B', 'nick' => '~B']);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key !== null || GG('test2BelongToOne3 1');
    $article->$key instanceof \M\Owner || GG('test2BelongToOne3 2');
    !isset($article->$key->name) || GG('test2BelongToOne3 3');
    $article->$key->nick == '~A' || GG('test2BelongToOne3 4');

    $article = \M\Book::one(3);
    $article->$key === null || GG('test2BelongToOne3 5');
  }
}
function test2BelongToMany4(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A', 'fake_id' => 2]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 1]);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToMany4 1');
    count($article->$key) == 1 || GG('test2BelongToMany4 2');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToMany4 3');
    $article = \M\Book::one(2);
    array_shift($article->$key)->name == 'B' || GG('test2BelongToMany4 4');
    
    $article = \M\Book::one(3);
    is_array($article->$key) || GG('test2BelongToMany4 5');
    count($article->$key) == 0 || GG('test2BelongToMany4 6');
  }
}
function test2BelongToOne4(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A', 'fake_id' => 2]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 1]);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key !== null || GG('test2BelongToOne4 1');
    $article->$key instanceof \M\Owner || GG('test2BelongToOne4 2');
    $article->$key->name == 'B' || GG('test2BelongToOne4 3');
    $article = \M\Book::one(3);
    $article->$key === null || GG('test2BelongToOne4 4');
  }
}
function test2BelongToMany5(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'CCbCC', 'fake_id' => 2]);
  \M\Owner::create(['name' => 'BBaBB', 'fake_id' => 3]);
  \M\Owner::create(['name' => 'aacaa', 'fake_id' => 2]);

  foreach ($keys as $key) {
    $article = \M\Book::one(1);
    is_array($article->$key) || GG('test2BelongToMany5 1');
    count($article->$key) == 1 || GG('test2BelongToMany5 2');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToMany5 3');
    $article = \M\Book::one(1);
    array_shift($article->$key)->name == 'aacaa' || GG('test2BelongToMany5 4');

    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToMany5 5');
    count($article->$key) == 0 || GG('test2BelongToMany5 6');
  }
}
function test2BelongToOne5(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'CCbCC', 'fake_id' => 2]);
  \M\Owner::create(['name' => 'BBaBB', 'fake_id' => 3]);
  \M\Owner::create(['name' => 'aacaa', 'fake_id' => 2]);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key === null || GG('test2BelongToOne5 1');

    $article = \M\Book::one(1);
    $article->$key !== null || GG('test2BelongToOne5 2');
    $article->$key instanceof \M\Owner || GG('test2BelongToOne5 3');
    $article->$key->name == 'aacaa' || GG('test2BelongToOne5 4');
  }
}
function test2BelongToMany6(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A', 'nick' => '~A', 'fake_id' => 2]);
  \M\Owner::create(['name' => 'B', 'nick' => '~B', 'fake_id' => 1]);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToMany6 1');
    count($article->$key) == 1 || GG('test2BelongToMany6 2');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToMany6 3');
    $article = \M\Book::one(2);
    !isset(array_shift($article->$key)->name) || GG('test2BelongToMany6 4');
    $article = \M\Book::one(2);
    array_shift($article->$key)->nick == '~B' || GG('test2BelongToMany6 5');
    
    $article = \M\Book::one(3);
    is_array($article->$key) || GG('test2BelongToMany6 6');
    count($article->$key) == 0 || GG('test2BelongToMany6 7');
  }
}
function test2BelongToOne6(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A', 'nick' => '~A', 'fake_id' => 2]);
  \M\Owner::create(['name' => 'B', 'nick' => '~B', 'fake_id' => 1]);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key !== null || GG('test2BelongToOne6 1');
    $article->$key instanceof \M\Owner || GG('test2BelongToOne6 2');
    !isset($article->$key->name) || GG('test2BelongToOne6 3');
    $article->$key->nick == '~B' || GG('test2BelongToOne6 4');

    $article = \M\Book::one(3);
    $article->$key === null || GG('test2BelongToOne6 5');
  }
}
function test2BelongToMany7(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'fake_id' => 1, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'fake_id' => 3, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'fake_id' => 4, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'C']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToMany7 1');
    count($article->$key) == 1 || GG('test2BelongToMany7 2');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToMany7 3');
    $article = \M\Book::one(2);
    array_shift($article->$key)->name == 'B' || GG('test2BelongToMany7 4');
    
    $article = \M\Book::one(3);
    is_array($article->$key) || GG('test2BelongToMany7 5');
    count($article->$key) == 0 || GG('test2BelongToMany7 6');
  }
}
function test2BelongToOne7(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'fake_id' => 1, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'fake_id' => 3, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'fake_id' => 4, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'C']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key !== null || GG('test2BelongToOne7 1');
    $article->$key instanceof \M\Owner || GG('test2BelongToOne7 2');
    $article->$key->name == 'B' || GG('test2BelongToOne7 3');
    $article = \M\Book::one(3);
    $article->$key === null || GG('test2BelongToOne7 4');
  }
}
function test2BelongToMany8(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'fake_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'fake_id' => 5, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'fake_id' => 1, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'CCbCC']);
  \M\Owner::create(['name' => 'BBaBB']);
  \M\Owner::create(['name' => 'aacaa']);

  foreach ($keys as $key) {
    $article = \M\Book::one(1);
    is_array($article->$key) || GG('test2BelongToMany8 1');
    count($article->$key) == 1 || GG('test2BelongToMany8 2');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToMany8 3');
    $article = \M\Book::one(1);
    array_shift($article->$key)->name == 'BBaBB' || GG('test2BelongToMany8 4');

    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToMany8 5');
    count($article->$key) == 0 || GG('test2BelongToMany8 6');
  }
}
function test2BelongToOne8(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'fake_id' => 2, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'fake_id' => 5, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'fake_id' => 1, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'CCbCC']);
  \M\Owner::create(['name' => 'BBaBB']);
  \M\Owner::create(['name' => 'aacaa']);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key === null || GG('test2BelongToOne8 1');

    $article = \M\Book::one(1);
    $article->$key !== null || GG('test2BelongToOne8 2');
    $article->$key instanceof \M\Owner || GG('test2BelongToOne8 3');
    $article->$key->name == 'BBaBB' || GG('test2BelongToOne8 4');
  }
}
function test2BelongToMany9(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA', 'fake_id' => 1]);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'fake_id' => 3]);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'fake_id' => 4]);
  \M\Owner::create(['name' => 'A', 'nick' => '~A']);
  \M\Owner::create(['name' => 'C', 'nick' => '~C']);
  \M\Owner::create(['name' => 'B', 'nick' => '~B']);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToMany9 1');
    count($article->$key) == 1 || GG('test2BelongToMany9 2');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToMany9 3');
    $article = \M\Book::one(2);
    !isset(array_shift($article->$key)->name) || GG('test2BelongToMany9 4');
    $article = \M\Book::one(2);
    array_shift($article->$key)->nick == '~B' || GG('test2BelongToMany9 5');
    
    $article = \M\Book::one(3);
    is_array($article->$key) || GG('test2BelongToMany9 6');
    count($article->$key) == 0 || GG('test2BelongToMany9 7');
  }
}
function test2BelongToOne9(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA', 'fake_id' => 1]);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'fake_id' => 3]);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'fake_id' => 4]);
  \M\Owner::create(['name' => 'A', 'nick' => '~A']);
  \M\Owner::create(['name' => 'C', 'nick' => '~C']);
  \M\Owner::create(['name' => 'B', 'nick' => '~B']);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key !== null || GG('test2BelongToOne9 1');
    $article->$key instanceof \M\Owner || GG('test2BelongToOne9 2');
    !isset($article->$key->name) || GG('test2BelongToOne9 3');
    $article->$key->nick == '~B' || GG('test2BelongToOne9 4');

    $article = \M\Book::one(3);
    $article->$key === null || GG('test2BelongToOne9 5');
  }
}
function test2BelongToManyA(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'fake_id' => 1, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'fake_id' => 3, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'fake_id' => 4, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A', 'fake_id' => 2]);
  \M\Owner::create(['name' => 'C', 'fake_id' => 3]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 1]);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToManyA 1');
    count($article->$key) == 1 || GG('test2BelongToManyA 2');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToManyA 3');
    $article = \M\Book::one(2);
    array_shift($article->$key)->name == 'C' || GG('test2BelongToManyA 4');
    
    $article = \M\Book::one(3);
    is_array($article->$key) || GG('test2BelongToManyA 5');
    count($article->$key) == 0 || GG('test2BelongToManyA 6');
  }
}
function test2BelongToOneA(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'fake_id' => 1, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'fake_id' => 3, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'fake_id' => 4, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'A', 'fake_id' => 2]);
  \M\Owner::create(['name' => 'C', 'fake_id' => 3]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 1]);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key !== null || GG('test2BelongToOneA 1');
    $article->$key instanceof \M\Owner || GG('test2BelongToOneA 2');
    $article->$key->name == 'C' || GG('test2BelongToOneA 3');
    $article = \M\Book::one(3);
    $article->$key === null || GG('test2BelongToOneA 4');
  }
}
function test2BelongToManyB(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'fake_id' => 1, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'fake_id' => 5, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'fake_id' => 4, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'CCbCC', 'fake_id' => 1]);
  \M\Owner::create(['name' => 'BBaBB', 'fake_id' => 3]);
  \M\Owner::create(['name' => 'aacaa', 'fake_id' => 1]);

  foreach ($keys as $key) {
    $article = \M\Book::one(1);
    is_array($article->$key) || GG('test2BelongToManyB 1');
    count($article->$key) == 1 || GG('test2BelongToManyB 2');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToManyB 3');
    $article = \M\Book::one(1);
    array_shift($article->$key)->name == 'aacaa' || GG('test2BelongToManyB 4');

    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToManyB 5');
    count($article->$key) == 0 || GG('test2BelongToManyB 6');
  }
}
function test2BelongToOneB(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'fake_id' => 1, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'fake_id' => 5, 'title' => 'BBB']);
  \M\Book::create(['owner_id' => 3, 'fake_id' => 4, 'title' => 'CCC']);
  \M\Owner::create(['name' => 'CCbCC', 'fake_id' => 1]);
  \M\Owner::create(['name' => 'BBaBB', 'fake_id' => 3]);
  \M\Owner::create(['name' => 'aacaa', 'fake_id' => 1]);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key === null || GG('test2BelongToOneB 1');

    $article = \M\Book::one(1);
    $article->$key !== null || GG('test2BelongToOneB 2');
    $article->$key instanceof \M\Owner || GG('test2BelongToOneB 3');
    $article->$key->name == 'aacaa' || GG('test2BelongToOneB 4');
  }
}
function test2BelongToManyC(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA', 'fake_id' => 1]);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'fake_id' => 3]);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'fake_id' => 4]);
  \M\Owner::create(['name' => 'A', 'nick' => '~A', 'fake_id' => 2]);
  \M\Owner::create(['name' => 'C', 'nick' => '~C', 'fake_id' => 3]);
  \M\Owner::create(['name' => 'B', 'nick' => '~B', 'fake_id' => 1]);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    is_array($article->$key) || GG('test2BelongToManyC 1');
    count($article->$key) == 1 || GG('test2BelongToManyC 2');
    array_shift($article->$key) instanceof \M\Owner || GG('test2BelongToManyC 3');
    $article = \M\Book::one(2);
    !isset(array_shift($article->$key)->name) || GG('test2BelongToManyC 4');
    $article = \M\Book::one(2);
    array_shift($article->$key)->nick == '~C' || GG('test2BelongToManyC 5');
    
    $article = \M\Book::one(3);
    is_array($article->$key) || GG('test2BelongToManyC 6');
    count($article->$key) == 0 || GG('test2BelongToManyC 7');
  }
}
function test2BelongToOneC(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();

  \M\Book::create(['owner_id' => 2, 'title' => 'AAA', 'fake_id' => 1]);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'fake_id' => 3]);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'fake_id' => 4]);
  \M\Owner::create(['name' => 'A', 'nick' => '~A', 'fake_id' => 2]);
  \M\Owner::create(['name' => 'C', 'nick' => '~C', 'fake_id' => 3]);
  \M\Owner::create(['name' => 'B', 'nick' => '~B', 'fake_id' => 1]);

  foreach ($keys as $key) {
    $article = \M\Book::one(2);
    $article->$key !== null || GG('test2BelongToOneC 1');
    $article->$key instanceof \M\Owner || GG('test2BelongToOneC 2');
    !isset($article->$key->name) || GG('test2BelongToOneC 3');
    $article->$key->nick == '~C' || GG('test2BelongToOneC 4');

    $article = \M\Book::one(3);
    $article->$key === null || GG('test2BelongToOneC 5');
  }
}
function test2HasMany1(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Book::create(['owner_id' => 1, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasMany1 1');
    count($member->$key) == 2 || GG('test2HasMany1 2');
    array_shift($member->$key)->title == 'AAA' || GG('test2HasMany1 3');
    array_shift($member->$key)->title == 'BBB' || GG('test2HasMany1 4');
    
    $member = \M\Owner::one(2);
    is_array($member->$key) || GG('test2HasMany1 5');
    count($member->$key) == 0 || GG('test2HasMany1 6');
  }
}
function test2HasOne1(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC']);
  \M\Book::create(['owner_id' => 1, 'title' => 'AAA']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB']);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) && GG('test2HasOne1 1');
    $member->$key instanceof \M\Book || GG('test2HasOne1 2');
    $member->$key->title = 'AAA' || GG('test2HasOne1 3');
   
    $member = \M\Owner::one(2);
    $member->$key === null || GG('test2HasOne1 4');
  }
}
function test2HasMany2(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 1, 'title' => 'AAA', 'score' => 6]);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'score' => 3]);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'score' => 12]);
  \M\Book::create(['owner_id' => 1, 'title' => 'DDD', 'score' => 10]);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasMany2 1');
    count($member->$key) == 2 || GG('test2HasMany2 2');
    array_shift($member->$key)->title == 'AAA' || GG('test2HasMany2 3');
    array_shift($member->$key)->title == 'DDD' || GG('test2HasMany2 4');
    $member = \M\Owner::one(2);
    is_array($member->$key) || GG('test2HasMany2 5');
    count($member->$key) == 0 || GG('test2HasMany2 6');
  }
}
function test2HasOne2(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 3, 'title' => 'AAA', 'score' => 10]);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'score' => 2]);
  \M\Book::create(['owner_id' => 1, 'title' => 'CCC', 'score' => 10]);
  \M\Book::create(['owner_id' => 1, 'title' => 'DDD', 'score' => 12]);

  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) && GG('test2HasOne2 1');
    $member->$key instanceof \M\Book || GG('test2HasOne2 2');
    $member->$key->title = 'CCC' || GG('test2HasOne2 3');
   
    $member = \M\Owner::one(2);
    $member->$key === null || GG('test2HasOne2 4');
  }
}
function test2HasMany3(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 1, 'title' => 'AAA', 'content' => 'aa - 6']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'content' => 'aa - 3']);
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'content' => 'aa - 12']);
  \M\Book::create(['owner_id' => 1, 'title' => 'DDD', 'content' => 'aa - 10']);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasMany3 1');
    count($member->$key) == 3 || GG('test2HasMany3 2');
    isset(array_shift($member->$key)->title) && GG('test2HasMany3 3');
    isset(array_shift($member->$key)->title) && GG('test2HasMany3 4');
    isset(array_shift($member->$key)->title) && GG('test2HasMany3 5');
    
    $member = \M\Owner::one(1);
    array_shift($member->$key)->content == 'aa - 6'  || GG('test2HasMany3 6');
    array_shift($member->$key)->content == 'aa - 3'  || GG('test2HasMany3 7');
    array_shift($member->$key)->content == 'aa - 10' || GG('test2HasMany3 8');
  }
}
function test2HasOne3(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 3, 'title' => 'AAA', 'content' => 'AAA - 1']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'content' => 'BBB - 2']);

  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) && GG('test2HasOne3 1');
    $member->$key instanceof \M\Book || GG('test2HasOne3 2');
    isset($member->$key->title) && GG('test2HasOne3 3');
    $member->$key->content == 'BBB - 2' || GG('test2HasOne3 4');
   
    $member = \M\Owner::one(2);
    $member->$key === null || GG('test2HasOne3 5');
  }
}
function test2HasMany4(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'fake_id' => 1]);
  \M\Book::create(['owner_id' => 1, 'title' => 'AAA', 'fake_id' => 3]);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'fake_id' => 1]);
  \M\Book::create(['owner_id' => 1, 'title' => 'DDD', 'fake_id' => 1]);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasMany4 1');
    count($member->$key) == 3 || GG('test2HasMany4 2');
    array_shift($member->$key)->title == 'CCC' || GG('test2HasMany4 3');
    array_shift($member->$key)->title == 'BBB' || GG('test2HasMany4 4');
    array_shift($member->$key)->title == 'DDD' || GG('test2HasMany4 5');
    
    $member = \M\Owner::one(2);
    is_array($member->$key) || GG('test2HasMany4 6');
    count($member->$key) == 0 || GG('test2HasMany4 7');
  }
}
function test2HasOne4(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'fake_id' => 3]);
  \M\Book::create(['owner_id' => 1, 'title' => 'AAA', 'fake_id' => 1]);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'fake_id' => 1]);
  \M\Book::create(['owner_id' => 1, 'title' => 'DDD', 'fake_id' => 1]);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) && GG('test2HasOne4 1');
    $member->$key instanceof \M\Book || GG('test2HasOne4 2');
    $member->$key->title == 'AAA' || GG('test2HasOne4 3');
    $member = \M\Owner::one(2);
    $member->$key === null || GG('test2HasOne3 4');
  }
}
function test2HasMany5(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'fake_id' => 1, 'score' => 2]);
  \M\Book::create(['owner_id' => 1, 'title' => 'AAA', 'fake_id' => 3, 'score' => 10]);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'fake_id' => 1, 'score' => 2]);
  \M\Book::create(['owner_id' => 1, 'title' => 'DDD', 'fake_id' => 1, 'score' => 13]);
  \M\Book::create(['owner_id' => 1, 'title' => 'EEE', 'fake_id' => 1, 'score' => 4]);
  \M\Book::create(['owner_id' => 1, 'title' => 'FFF', 'fake_id' => 1, 'score' => 20]);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasMany5 1');
    count($member->$key) == 2 || GG('test2HasMany5 2');

    array_shift($member->$key)->title == 'DDD' || GG('test2HasMany5 3');
    array_shift($member->$key)->title == 'FFF' || GG('test2HasMany5 4');
    
    $member = \M\Owner::one(2);
    is_array($member->$key) || GG('test2HasMany5 5');
    count($member->$key) == 0 || GG('test2HasMany5 6');
  }
}
function test2HasOne5(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'fake_id' => 1, 'score' => 2]);
  \M\Book::create(['owner_id' => 1, 'title' => 'AAA', 'fake_id' => 3, 'score' => 10]);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'fake_id' => 1, 'score' => 2]);
  \M\Book::create(['owner_id' => 1, 'title' => 'DDD', 'fake_id' => 1, 'score' => 13]);
  \M\Book::create(['owner_id' => 1, 'title' => 'EEE', 'fake_id' => 1, 'score' => 4]);
  \M\Book::create(['owner_id' => 1, 'title' => 'FFF', 'fake_id' => 1, 'score' => 20]);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) && GG('test2HasOne5 1');
    $member->$key instanceof \M\Book || GG('test2HasOne5 2');
    $member->$key->title == 'DDD' || GG('test2HasOne5 3');

    $member = \M\Owner::one(2);
    $member->$key === null || GG('test2HasOne5 4');
  }
}
function test2HasMany6(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'fake_id' => 3, 'content' => 'CCC - 2']);
  \M\Book::create(['owner_id' => 1, 'title' => 'AAA', 'fake_id' => 1, 'content' => 'AAA - 10']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'fake_id' => 1, 'content' => 'BBB - 2']);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasMany6 1');
    count($member->$key) == 2 || GG('test2HasMany6 2');
    array_shift($member->$key) instanceof \M\Book || GG('test2HasMany6 3');
    array_shift($member->$key) instanceof \M\Book || GG('test2HasMany6 4');

    $member = \M\Owner::one(1);
    isset(array_shift($member->$key)->title) && GG('test2HasMany6 5');
    isset(array_shift($member->$key)->title) && GG('test2HasMany6 6');

    $member = \M\Owner::one(1);
    array_shift($member->$key)->content == 'AAA - 10' || GG('test2HasMany6 7');
    array_shift($member->$key)->content == 'BBB - 2' || GG('test2HasMany6 8');

    $member = \M\Owner::one(2);
    is_array($member->$key) || GG('test2HasMany6 9');
    count($member->$key) == 0 || GG('test2HasMany6 10');
  }
}
function test2HasOne6(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['owner_id' => 3, 'title' => 'CCC', 'fake_id' => 3, 'content' => 'CCC - 2']);
  \M\Book::create(['owner_id' => 1, 'title' => 'AAA', 'fake_id' => 1, 'content' => 'AAA - 10']);
  \M\Book::create(['owner_id' => 1, 'title' => 'BBB', 'fake_id' => 1, 'content' => 'BBB - 2']);
  \M\Owner::create(['name' => 'A']);
  \M\Owner::create(['name' => 'B']);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    $member->$key !== null || GG('test2HasOne6 1');
    $member->$key instanceof \M\Book || GG('test2HasOne6 2');
    isset($member->$key->title) && GG('test2HasOne6 3');
    $member->$key->content == 'AAA - 10' || GG('test2HasOne6 4');

    $member = \M\Owner::one(2);
    $member->$key === null || GG('test2HasOne6 5');
  }
}
function test2HasMany7(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4]);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 10]);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 10]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 10]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasMany7 1');
    count($member->$key) == 0 || GG('test2HasMany7 2');

    $member = \M\Owner::one(2);
    is_array($member->$key) || GG('test2HasMany7 3');
    count($member->$key) == 2 || GG('test2HasMany7 4');
    array_shift($member->$key) instanceof \M\Book || GG('test2HasMany7 5');
    array_shift($member->$key) instanceof \M\Book || GG('test2HasMany7 6');

    $member = \M\Owner::one(2);
    array_shift($member->$key)->title == 'AAA' || GG('test2HasMany7 7');
    array_shift($member->$key)->title == 'BBB' || GG('test2HasMany7 8');

    $member = \M\Owner::one(3);
    is_array($member->$key) || GG('test2HasMany7 9');
    count($member->$key) == 0 || GG('test2HasMany7 10');
  }
}
function test2HasOne7(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4]);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 10]);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 10]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 10]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    $member->$key === null || GG('test2HasOne7 2');

    $member = \M\Owner::one(2);
    $member->$key !== null || GG('test2HasOne7 3');
    $member->$key instanceof \M\Book || GG('test2HasOne7 4');
    $member->$key->title == 'AAA' || GG('test2HasOne7 5');

    $member = \M\Owner::one(3);
    $member->$key === null || GG('test2HasOne7 6');
  }
}
function test2HasMany8(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4, 'score' => 10]);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 20, 'score' => 3]);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 20, 'score' => 12]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 20]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasMany8 1');
    count($member->$key) == 0 || GG('test2HasMany8 2');

    $member = \M\Owner::one(2);
    is_array($member->$key) || GG('test2HasMany8 3');
    count($member->$key) == 1 || GG('test2HasMany8 4');
    array_shift($member->$key) instanceof \M\Book || GG('test2HasMany8 5');

    $member = \M\Owner::one(2);
    array_shift($member->$key)->title == 'BBB' || GG('test2HasMany8 6');

    $member = \M\Owner::one(3);
    is_array($member->$key) || GG('test2HasMany8 7');
    count($member->$key) == 0 || GG('test2HasMany8 8');
  }
}
function test2HasOne8(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4, 'score' => 10]);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 20, 'score' => 3]);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 20, 'score' => 12]);
  \M\Book::create(['title' => 'DDD', 'owner_id' => 20, 'score' => 17]);

  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 20]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    $member->$key === null || GG('test2HasOne8 2');

    $member = \M\Owner::one(2);
    $member->$key instanceof \M\Book || GG('test2HasOne8 3');
    $member->$key->title == 'BBB' || GG('test2HasOne8 4');

    $member = \M\Owner::one(3);
    $member->$key === null || GG('test2HasOne8 4');
  }
}
function test2HasMany9(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4, 'content' => 'CCC 10']);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 20, 'content' => 'AAA 3']);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 20, 'content' => 'BBB 12']);
  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 20]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasMany9 1');
    count($member->$key) == 0 || GG('test2HasMany9 2');

    $member = \M\Owner::one(2);
    is_array($member->$key) || GG('test2HasMany9 3');
    count($member->$key) == 2 || GG('test2HasMany9 4');
    array_shift($member->$key) instanceof \M\Book || GG('test2HasMany9 5');
    array_shift($member->$key) instanceof \M\Book || GG('test2HasMany9 6');

    $member = \M\Owner::one(2);
    isset(array_shift($member->$key)->title) && GG('test2HasMany9 7');
    isset(array_shift($member->$key)->title) && GG('test2HasMany9 8');

    $member = \M\Owner::one(2);
    array_shift($member->$key)->content = 'AAA 3' || GG('test2HasMany9 9');
    array_shift($member->$key)->content = 'BBB 12' || GG('test2HasMany9 10');

    $member = \M\Owner::one(3);
    is_array($member->$key) || GG('test2HasMany9 11');
    count($member->$key) == 0 || GG('test2HasMany9 12');
  }
}
function test2HasOne9(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4, 'content' => 'CCC - 10']);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 20, 'content' => 'AAA - 3']);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 20, 'content' => 'BBB - 12']);
  \M\Book::create(['title' => 'DDD', 'owner_id' => 20, 'content' => 'DDD - 17']);

  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 20]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    $member->$key === null || GG('test2HasOne9 2');

    $member = \M\Owner::one(2);
    $member->$key instanceof \M\Book || GG('test2HasOne9 3');
    isset($member->$key->title) && GG('test2HasOne9 4');
    $member->$key->content == 'AAA - 3' || GG('test2HasOne9 5');

    $member = \M\Owner::one(3);
    $member->$key === null || GG('test2HasOne9 4');
  }
}
function test2HasManyA(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4, 'fake_id' => 10]);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 10, 'fake_id' => 10]);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 10, 'fake_id' => 3]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 10]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasManyA 1');
    count($member->$key) == 0 || GG('test2HasManyA 2');

    $member = \M\Owner::one(2);
    is_array($member->$key) || GG('test2HasManyA 3');
    count($member->$key) == 2 || GG('test2HasManyA 4');
    array_shift($member->$key) instanceof \M\Book || GG('test2HasManyA 5');
    array_shift($member->$key) instanceof \M\Book || GG('test2HasManyA 6');

    $member = \M\Owner::one(2);
    array_shift($member->$key)->title == 'CCC' || GG('test2HasManyA 7');
    array_shift($member->$key)->title == 'AAA' || GG('test2HasManyA 8');

    $member = \M\Owner::one(3);
    is_array($member->$key) || GG('test2HasManyA 9');
    count($member->$key) == 0 || GG('test2HasManyA 10');
  }
}
function test2HasOneA(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'DDD', 'owner_id' => 4, 'fake_id' => 2]);
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4, 'fake_id' => 10]);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 10, 'fake_id' => 10]);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 10, 'fake_id' => 3]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 10]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    $member->$key === null || GG('test2HasOneA 2');

    $member = \M\Owner::one(2);
    $member->$key !== null || GG('test2HasOneA 3');
    $member->$key instanceof \M\Book || GG('test2HasOneA 4');
    $member->$key->title == 'CCC' || GG('test2HasOneA 5');

    $member = \M\Owner::one(3);
    $member->$key === null || GG('test2HasOneA 6');
  }
}
function test2HasManyB(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'DDD', 'owner_id' => 4, 'score' => 10, 'fake_id' => 52]);
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4, 'score' => 2, 'fake_id' => 20]);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 20, 'score' => 10, 'fake_id' => 20]);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 20, 'score' => 12, 'fake_id' => 52]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 20]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasManyB 1');
    count($member->$key) == 0 || GG('test2HasManyB 2');

    $member = \M\Owner::one(2);
    is_array($member->$key) || GG('test2HasManyB 3');
    count($member->$key) == 1 || GG('test2HasManyB 4');
    array_shift($member->$key) instanceof \M\Book || GG('test2HasManyB 5');

    $member = \M\Owner::one(2);
    array_shift($member->$key)->title == 'AAA' || GG('test2HasManyB 6');

    $member = \M\Owner::one(3);
    is_array($member->$key) || GG('test2HasManyB 7');
    count($member->$key) == 0 || GG('test2HasManyB 8');
  }
}
function test2HasOneB(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'DDD', 'owner_id' => 4, 'score' => 10, 'fake_id' => 52]);
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4, 'score' => 2, 'fake_id' => 20]);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 20, 'score' => 10, 'fake_id' => 20]);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 20, 'score' => 12, 'fake_id' => 52]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 20]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    $member->$key === null || GG('test2HasOneB 2');

    $member = \M\Owner::one(2);
    $member->$key instanceof \M\Book || GG('test2HasOneB 3');
    $member->$key->title == 'AAA' || GG('test2HasOneB 4');

    $member = \M\Owner::one(3);
    $member->$key === null || GG('test2HasOneB 5');
  }
}
function test2HasManyC(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4, 'content' => 'CCC 10', 'fake_id' => 33]);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 20, 'content' => 'AAA 3', 'fake_id' => 20]);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 20, 'content' => 'BBB 12', 'fake_id' => 21]);
  \M\Book::create(['title' => 'DDD', 'owner_id' => 20, 'content' => 'DDD 12', 'fake_id' => 21]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 20]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    is_array($member->$key) || GG('test2HasManyC 1');
    count($member->$key) == 0 || GG('test2HasManyC 2');

    $member = \M\Owner::one(2);
    is_array($member->$key) || GG('test2HasManyC 3');
    count($member->$key) == 1 || GG('test2HasManyC 4');
    array_shift($member->$key) instanceof \M\Book || GG('test2HasManyC 5');

    $member = \M\Owner::one(2);
    isset(array_shift($member->$key)->title) && GG('test2HasManyC 6');

    $member = \M\Owner::one(2);
    array_shift($member->$key)->content = 'AAA 3' || GG('test2HasManyC 7');

    $member = \M\Owner::one(3);
    is_array($member->$key) || GG('test2HasManyC 8');
    count($member->$key) == 0 || GG('test2HasManyC 9');
  }
}
function test2HasOneC(...$keys) {
  \M\Book::truncate();
  \M\Owner::truncate();
  \M\Book::create(['title' => 'CCC', 'owner_id' => 4, 'content' => 'CCC - 10', 'fake_id' => 201]);
  \M\Book::create(['title' => 'AAA', 'owner_id' => 20, 'content' => 'AAA - 3', 'fake_id' => 20]);
  \M\Book::create(['title' => 'BBB', 'owner_id' => 20, 'content' => 'BBB - 12', 'fake_id' => 20]);
  \M\Book::create(['title' => 'DDD', 'owner_id' => 20, 'content' => 'DDD - 17', 'fake_id' => 203]);

  \M\Owner::create(['name' => 'A', 'fake_id' => 5]);
  \M\Owner::create(['name' => 'A', 'fake_id' => 20]);
  \M\Owner::create(['name' => 'B', 'fake_id' => 6]);

  foreach ($keys as $key) {
    $member = \M\Owner::one(1);
    $member->$key === null || GG('test2HasOneC 2');

    $member = \M\Owner::one(2);
    $member->$key instanceof \M\Book || GG('test2HasOneC 3');
    isset($member->$key->title) && GG('test2HasOneC 4');
    $member->$key->content == 'AAA - 3' || GG('test2HasOneC 5');

    $member = \M\Owner::one(3);
    $member->$key === null || GG('test2HasOneC 6');
  }
}

function test2Relation() {
  test2BelongToMany1('b1_01', 'b1_02', 'b1_03', 'b1_04', 'b1_05', 'b1_06', 'b1_07', 'b1_08');
  test2BelongToOne1('b1_09', 'b1_11', 'b1_12', 'b1_13', 'b1_14', 'b1_15', 'b1_16', 'b1_17');
  test2BelongToMany2('b1_18', 'b1_19', 'b1_21', 'b1_22', 'b1_23', 'b1_24', 'b1_25', 'b1_26');
  test2BelongToOne2('b1_27', 'b1_28', 'b1_29', 'b1_30', 'b1_31', 'b1_32', 'b1_33', 'b1_34');
  test2BelongToMany3('b1_35', 'b1_36', 'b1_37', 'b1_38');
  test2BelongToOne3('b1_39', 'b1_40', 'b1_41', 'b1_42');
  
  test2BelongToMany4('b2_01', 'b2_02', 'b2_03', 'b2_04');
  test2BelongToOne4('b2_05', 'b2_06', 'b2_07', 'b2_08');
  test2BelongToMany5('b2_09', 'b2_10', 'b2_11', 'b2_12');
  test2BelongToOne5('b2_13', 'b2_14', 'b2_15', 'b2_16');
  test2BelongToMany6('b2_17', 'b2_18');
  test2BelongToOne6('b2_19', 'b2_20');

  test2BelongToMany7('b3_01', 'b3_02', 'b3_03', 'b3_04');
  test2BelongToOne7('b3_05', 'b3_06', 'b3_07', 'b3_08');
  test2BelongToMany8('b3_09', 'b3_10', 'b3_11', 'b3_12');
  test2BelongToOne8('b3_13', 'b3_14', 'b3_15', 'b3_16');
  test2BelongToMany9('b3_17', 'b3_18');
  test2BelongToOne9('b3_19', 'b3_20');

  test2BelongToManyA('b4_01', 'b4_02');
  test2BelongToOneA('b4_03', 'b4_04');
  test2BelongToManyB('b4_05', 'b4_06');
  test2BelongToOneB('b4_07', 'b4_08');
  test2BelongToManyC('b4_09');
  test2BelongToOneC('b4_10');

  test2HasMany1('a1_01', 'a1_02', 'a1_03', 'a1_04', 'a1_05', 'a1_06', 'a1_07', 'a1_08', 'a1_09', 'a1_10');
  test2HasOne1('a1_11', 'a1_12', 'a1_13', 'a1_14', 'a1_15', 'a1_16', 'a1_17', 'a1_18');
  test2HasMany2('a1_19', 'a1_20', 'a1_21', 'a1_22', 'a1_23', 'a1_24', 'a1_25', 'a1_26');
  test2HasOne2('a1_27', 'a1_28', 'a1_29', 'a1_30', 'a1_31', 'a1_32', 'a1_33', 'a1_34');
  test2HasMany3('a1_35', 'a1_36', 'a1_37', 'a1_38');
  test2HasOne3('a1_39', 'a1_40', 'a1_41', 'a1_42');
  
  test2HasMany4('a2_01', 'a2_02', 'a2_03', 'a2_04', 'a2_05');
  test2HasOne4('a2_06', 'a2_07', 'a2_08', 'a2_09');
  test2HasMany5('a2_10', 'a2_11', 'a2_12', 'a2_13');
  test2HasOne5('a2_14', 'a2_15', 'a2_16', 'a2_17');
  test2HasMany6('a2_18', 'a2_19');
  test2HasOne6('a2_20', 'a2_21');

  test2HasMany7('a3_01', 'a3_02', 'a3_03', 'a3_04');
  test2HasOne7('a3_05', 'a3_06', 'a3_07', 'a3_08');
  test2HasMany8('a3_09', 'a3_10', 'a3_11', 'a3_12');
  test2HasOne8('a3_13', 'a3_14', 'a3_15', 'a3_16');
  test2HasMany9('a3_17', 'a3_18');
  test2HasOne9('a3_19', 'a3_20');

  test2HasManyA('a4_01', 'a4_02');
  test2HasOneA('a4_03', 'a4_04');

  test2HasManyB('a4_05', 'a4_06');
  test2HasOneB('a4_07', 'a4_08');
  test2HasManyC('a4_09');
  test2HasOneC('a4_10');
}

echo "test2Relation";
test2Relation();
echo " - ok\n";
