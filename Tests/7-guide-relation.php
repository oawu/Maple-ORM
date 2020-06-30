<?php

namespace M {
  use \M\Core\Connection;

  class User4 extends Model {
    static $tableName = 'User';
  }

  class Article4 extends Model {
    static $tableName = 'Article';
  }

  class User5 extends Model {
    static $tableName = 'User';
    static $relations = [
      'articles' => ['hasMany' => 'Article5']
    ];
  }
  class Article5 extends Model {
    static $tableName = 'Article';
    static $relations = [
      'user' => ['belongToOne' => 'User5']
    ];
  }
  class User6 extends Model {
    static $tableName = 'User';
    static $relations = [
      'article' => ['hasOne' => 'Article6']
    ];
  }
  class Article6 extends Model {
    static $tableName = 'Article';
  }
  class User7 extends Model {
    static $tableName = 'User';
    static $relations = [
      'pv20Articles' => ['hasMany' => 'Article7', 'where' => ['pageView > ?', 20]]
    ];
  }
  class Article7 extends Model {
    static $tableName = 'Article';
  }
  class User8 extends Model {
    static $tableName = 'User';
    static $relations = [
      'articles' => ['hasMany' => 'Article8']
    ];
  }
  class Article8 extends Model {
    static $tableName = 'Article';
  }

  
  Model::case(\M\Model::CASE_CAMEL);

  include '_.php';

  $sql = "CREATE TABLE `User` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(190) CHARACTER SET utf8mb4 DEFAULT NULL,
    `age` int(10) DEFAULT NULL,
    `updateAt` datetime DEFAULT NULL,
    `createAt` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

  if (Connection::instance()->query($sql)) {
    var_dump('create User table Error');
    exit(1);
  }

  $sql = "CREATE TABLE `Article` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `userId` int(10) DEFAULT NULL,
    `title` varchar(190) CHARACTER SET utf8mb4 DEFAULT NULL,
    `pageView` int(10) DEFAULT NULL,
    `updateAt` datetime DEFAULT NULL,
    `createAt` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

  if (Connection::instance()->query($sql)) {
    var_dump('create Article table Error');
    exit(1);
  }
}
namespace {
  
  echo "relation guide Test";

  $reset = function() {
    \M\User4::truncate();
    \M\Article4::truncate();

    \M\User4::creates([
      ['name' => 'OA', 'age' => 18],
      ['name' => 'OB', 'age' => 28],
      ['name' => 'OC', 'age' => 15],
    ]);
    
    \M\Article4::creates([
      ['userId' => 1, 'title' => '文章 1', 'pageView' => 11],
      ['userId' => 2, 'title' => '文章 2', 'pageView' => 23],
      ['userId' => 1, 'title' => '文章 3', 'pageView' => 31],
      ['userId' => 1, 'title' => '文章 4', 'pageView' => 42],
      ['userId' => 4, 'title' => '文章 5', 'pageView' => 42],
    ]);
  };

  $reset();

  $user = \M\User4::one(1);
  $articles = \M\Article4::all('userId = ?', $user->id);
  $ans = ['文章 1', '文章 3', '文章 4'];
  foreach ($articles as $i => $article)
    $article->title == $ans[$i] || exit('Err');

  $user = \M\User4::one(3);
  $articles = \M\Article4::all('userId = ?', $user->id);
  count($articles) == 0 || exit('Err');


  $user = \M\User5::one(1);
  foreach ($user->articles as $i => $article)
    $article->title == $ans[$i] || exit('Err');

  $user = \M\User5::one(3);
  count($user->articles) == 0 || exit('Err');

  $article = \M\Article4::one(2);
  $user = \M\User4::one('id = ?', $article->userId);
  $user->name == 'OB' || exit('Err');

  $article = \M\Article4::one(5);
  $user = \M\User4::one('id = ?', $article->userId);
  $user === null || exit('Err');

  $article = \M\Article5::one(2);
  $article->user->name == 'OB' || exit('Err');
  
  $article = \M\Article5::one(5);
  $article->user === null || exit('Err');

  $user = \M\User6::one(1);
  $user->article->title == '文章 1' || exit('Err');
  
  $user = \M\User6::one(3);
  $user->article === null || exit('Err'); // null

  $user = \M\User7::one(1);
  $ans = ['文章 3', '文章 4'];
  foreach ($user->pv20Articles as $i => $pv20Article)
    $pv20Article->title == $ans[$i] || exit('Err');

  $user = \M\User8::one(1);
  $articles = $user->relation('articles')->and('pageView > ?', 20)->all();
  $ans = ['文章 3', '文章 4'];
  foreach ($articles as $i => $article)
    $article->title == $ans[$i] || exit('Err');

  echo " - ok\n";
}