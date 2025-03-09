<?php

include '0.php';

use \Orm\Core\Connection;
use \Model\ReadWrite\User;
use \Model\ReadWrite\Book;

$sql = "
  CREATE TABLE `ReadWriteUser` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `readWriteBookId` int DEFAULT NULL,
    `name` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `x` int DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  CREATE TABLE `ReadWriteBook` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `readWriteUserId` int DEFAULT NULL,
    `y` int DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

  INSERT INTO `ReadWriteUser` (`id`, `readWriteBookId`, `name`, `x`) VALUES
    (1, 1, 'Boa', 3),
    (2, 2, 'Bob', 2),
    (3, 3, 'Boc', 1);

  INSERT INTO `ReadWriteBook` (`id`, `title`, `readWriteUserId`, `y`) VALUES
    (1, 'B-book', 2, 2),
    (2, 'B-book', 1, 3),
    (3, 'B-book', 2, 1),
    (4, 'B-book', 1, 1),
    (5, 'B-book', 1, 2),
    (6, 'B-book', 3, 1),
    (7, 'B-book', 2, 2);";

if ($error = Connection::instance('R')->runQuery($sql)) {
  throw $error;
}

if ($error = Connection::instance('W')->runQuery($sql)) {
  throw $error;
}


$userRs = User::db('R')->where([1, 3])->all();
$userWs = User::db('W')->where([1, 2])->all();

$ids = [];
foreach ($userRs as $user) {
  $ids[] = $user->id;
  $ids[] = $user->name;
  $ids[] = $user->book1->id;
  $ids[] = $user->book1->title;

  foreach ($user->book1s as $book) {
    $ids[] = $book->id;
    $ids[] = $book->title;
  }
}
$ids[] = '';
foreach ($userWs as $user) {
  $ids[] = $user->id;
  $ids[] = $user->name;
  $ids[] = $user->book1->id;
  $ids[] = $user->book1->title;

  foreach ($user->book1s as $book) {
    $ids[] = $book->id;
    $ids[] = $book->title;
  }
}
if (md5(json_encode($ids)) != '20edceb41915aac7d1416ed8238b3f8d') {
  throw new Exception();
}

$userRs = User::db('R')->relation('book1', 'book1s')->where([1, 3])->all();
$userWs = User::db('W')->relation('book1', 'book1s')->where([1, 2])->all();

$ids = [];
foreach ($userRs as $user) {
  $ids[] = $user->id;
  $ids[] = $user->name;
  $ids[] = $user->book1->id;
  $ids[] = $user->book1->title;

  foreach ($user->book1s as $book) {
    $ids[] = $book->id;
    $ids[] = $book->title;
  }
}
$ids[] = '';
foreach ($userWs as $user) {
  $ids[] = $user->id;
  $ids[] = $user->name;
  $ids[] = $user->book1->id;
  $ids[] = $user->book1->title;

  foreach ($user->book1s as $book) {
    $ids[] = $book->id;
    $ids[] = $book->title;
  }
}
if (md5(json_encode($ids)) != '20edceb41915aac7d1416ed8238b3f8d') {
  throw new Exception();
}

$userRs = User::db('R')->where([1, 3])->all();
$userWs = User::db('W')->where([1, 2])->all();

$ids = [];
foreach ($userRs as $user) {
  $ids[] = $user->id;
  $ids[] = $user->name;
  $ids[] = $user->book2->id;
  $ids[] = $user->book2->title;
  foreach ($user->book2s as $book) {
    $ids[] = $book->id;
    $ids[] = $book->title;
  }
}
$ids[] = '';
foreach ($userWs as $user) {
  $ids[] = $user->id;
  $ids[] = $user->name;
  $ids[] = $user->book2->id;
  $ids[] = $user->book2->title;

  foreach ($user->book2s as $book) {
    $ids[] = $book->id;
    $ids[] = $book->title;
  }
}
if (md5(json_encode($ids)) != 'ecb60dc4be806b474d455b695561a485') {
  throw new Exception();
}

$userRs = User::db('R')->relation('book2', 'book2s')->where([1, 3])->all();
$userWs = User::db('W')->relation('book2', 'book2s')->where([1, 2])->all();

$ids = [];
foreach ($userRs as $user) {
  $ids[] = $user->id;
  $ids[] = $user->name;
  $ids[] = $user->book2->id;
  $ids[] = $user->book2->title;
  foreach ($user->book2s as $book) {
    $ids[] = $book->id;
    $ids[] = $book->title;
  }
}
$ids[] = '';
foreach ($userWs as $user) {
  $ids[] = $user->id;
  $ids[] = $user->name;
  $ids[] = $user->book2->id;
  $ids[] = $user->book2->title;

  foreach ($user->book2s as $book) {
    $ids[] = $book->id;
    $ids[] = $book->title;
  }
}
if (md5(json_encode($ids)) != 'ecb60dc4be806b474d455b695561a485') {
  throw new Exception();
}


// ====================================================================

$bookRs = Book::db('R')->where([1, 3])->all();
$bookWs = Book::db('W')->where([1, 2])->all();

$ids = [];
foreach ($bookRs as $book) {
  $ids[] = $book->id;
  $ids[] = $book->title;
  $ids[] = $book->user1->id;
  $ids[] = $book->user1->name;

  foreach ($book->user1s as $user) {
    $ids[] = $user->id;
    $ids[] = $user->name;
  }
}
$ids[] = '';
foreach ($bookWs as $book) {
  $ids[] = $book->id;
  $ids[] = $book->title;
  $ids[] = $book->user1->id;
  $ids[] = $book->user1->name;

  foreach ($book->user1s as $user) {
    $ids[] = $user->id;
    $ids[] = $user->name;
  }
}

if (md5(json_encode($ids)) != 'bbc1c23a0c6fb47cc2387d24af750e0e') {
  throw new Exception();
}

$bookRs = Book::db('R')->relation('user1', 'user1s')->where([1, 3])->all();
$bookWs = Book::db('W')->relation('user1', 'user1s')->where([1, 2])->all();

$ids = [];
foreach ($bookRs as $book) {
  $ids[] = $book->id;
  $ids[] = $book->title;
  $ids[] = $book->user1->id;
  $ids[] = $book->user1->name;

  foreach ($book->user1s as $user) {
    $ids[] = $user->id;
    $ids[] = $user->name;
  }
}
$ids[] = '';
foreach ($bookWs as $book) {
  $ids[] = $book->id;
  $ids[] = $book->title;
  $ids[] = $book->user1->id;
  $ids[] = $book->user1->name;

  foreach ($book->user1s as $user) {
    $ids[] = $user->id;
    $ids[] = $user->name;
  }
}

if (md5(json_encode($ids)) != 'bbc1c23a0c6fb47cc2387d24af750e0e') {
  throw new Exception();
}

$bookRs = Book::db('R')->where([1, 3])->all();
$bookWs = Book::db('W')->where([1, 2])->all();

$ids = [];
foreach ($bookRs as $book) {
  $ids[] = $book->id;
  $ids[] = $book->title;
  $ids[] = $book->user2->id;
  $ids[] = $book->user2->name;

  foreach ($book->user2s as $user) {
    $ids[] = $user->id;
    $ids[] = $user->name;
  }
}
$ids[] = '';
foreach ($bookWs as $book) {
  $ids[] = $book->id;
  $ids[] = $book->title;
  $ids[] = $book->user2->id;
  $ids[] = $book->user2->name;

  foreach ($book->user2s as $user) {
    $ids[] = $user->id;
    $ids[] = $user->name;
  }
}

if (md5(json_encode($ids)) != '013cf2d078db4330eba81e5a4f0b4ff1') {
  throw new Exception();
}

$bookRs = Book::db('R')->relation('user2', 'user2s')->where([1, 3])->all();
$bookWs = Book::db('W')->relation('user2', 'user2s')->where([1, 2])->all();

$ids = [];
foreach ($bookRs as $book) {
  $ids[] = $book->id;
  $ids[] = $book->title;
  $ids[] = $book->user2->id;
  $ids[] = $book->user2->name;

  foreach ($book->user2s as $user) {
    $ids[] = $user->id;
    $ids[] = $user->name;
  }
}
$ids[] = '';
foreach ($bookWs as $book) {
  $ids[] = $book->id;
  $ids[] = $book->title;
  $ids[] = $book->user2->id;
  $ids[] = $book->user2->name;

  foreach ($book->user2s as $user) {
    $ids[] = $user->id;
    $ids[] = $user->name;
  }
}

if (md5(json_encode($ids)) != '013cf2d078db4330eba81e5a4f0b4ff1') {
  throw new Exception();
}
