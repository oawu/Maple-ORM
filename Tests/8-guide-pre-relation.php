<?php

namespace M {
  use \M\Core\Connection;

  class User81 extends Model {
    static $tableName = 'User';
    static $relations = [
      'articles' => '<= Article81'
    ];
  }

  class Article81 extends Model {
    static $tableName = 'Article';
    static $relations = [
      'comments' => '<= Comment81'
    ];
  }


  class Comment81 extends Model {
    static $tableName = 'Comment';
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
  $sql = "CREATE TABLE `Comment` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `articleId` int(10) DEFAULT NULL,
    `content` varchar(190) CHARACTER SET utf8mb4 DEFAULT NULL,
    `updateAt` datetime DEFAULT NULL,
    `createAt` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

  if (Connection::instance()->query($sql)) {
    var_dump('create Comment table Error');
    exit(1);
  }
}
namespace {
  
  echo "relation guide Test";

  $reset = function() {
    \M\User81::truncate();
    \M\Article81::truncate();

    \M\User81::creates([
      ['name' => 'OA', 'age' => 18],
      ['name' => 'OB', 'age' => 28],
      ['name' => 'OC', 'age' => 15],
    ]);
    
    \M\Article81::creates([
      ['userId' => 1, 'title' => '文章 1', 'pageView' => 11],
      ['userId' => 2, 'title' => '文章 2', 'pageView' => 23],
      ['userId' => 1, 'title' => '文章 3', 'pageView' => 31],
      ['userId' => 1, 'title' => '文章 4', 'pageView' => 42],
      ['userId' => 4, 'title' => '文章 5', 'pageView' => 42],
    ]);
    
    \M\Comment81::creates([
      ['articleId' => 2, 'content' => '留言 1'],
      ['articleId' => 2, 'content' => '留言 2'],
      ['articleId' => 1, 'content' => '留言 3'],
      ['articleId' => 1, 'content' => '留言 4'],
      ['articleId' => 3, 'content' => '留言 5'],
    ]);
  };

  $reset();

  $user = \M\User81::one(1);
  foreach ($user->articles as $article)
    $article;

  $users = \M\User81::all(['pre-relation' => ['articles']]);
  foreach ($users as $user)
    foreach ($user->articles as $article)
      $article;


  $users = \M\User81::all();

  // 取得所有 User ID
  $userIds = array_map(function($user) {
    return $user->id;
  }, $users);

  // 取出這些 User 的所有 Article
  $allArticles = \M\Article81::all('userId IN (?)', $userIds);

  // 以 userId 為 key 組合出各個 User 所屬的 Article
  $articles = [];
  foreach ($allArticles as $article) {
    if (!isset($articles[$article->userId])) {
      $articles[$article->userId] = [];
    }
    array_push($articles[$article->userId], $article);
  }

  foreach ($users as $user) {
    $user->name;

    $userArticles = isset($articles[$user->id]) ? $articles[$user->id] : [];

    foreach ($userArticles as $article) {
      $article->title;
    }
  }

  $users = \M\User81::all(['pre' => 'articles.comments']);
  foreach ($users as $user) {
    $user->name;

    foreach ($user->articles as $article) {
      $article->title;

      foreach ($article->comments as $comment) {
        $comment->content;
      }
    }
  }

  echo " - ok\n";
}