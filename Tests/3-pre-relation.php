<?php

include '_.php';

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

$sql = "CREATE TABLE `Comment` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `memberId` int(10) unsigned NOT NULL DEFAULT '0', `articleId` int(10) unsigned NOT NULL DEFAULT '0', `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `content` text COLLATE utf8mb4_unicode_ci, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
if (\M\Core\Connection::instance()->query($sql)) {
  var_dump('create Comment table Error');
  exit(1);
}

\M\Model::case(\M\Model::CASE_CAMEL);

\M\Article::creates([['memberId' => 1, 'title' => 'AAA'], ['memberId' => 1, 'title' => 'BBB'], ['memberId' => 1, 'title' => 'CCC'], ['memberId' => 2, 'title' => 'DDD'], ['memberId' => 3, 'title' => 'EEE'], ['memberId' => 3, 'title' => 'FFF']]);

\M\Member::creates([['name' => 'A'], ['name' => 'B'], ['name' => 'C']]);

\M\Comment::creates([['memberId' => 1, 'articleId' => 1, 'content' => 'Comment A'], ['memberId' => 1, 'articleId' => 1, 'content' => 'Comment B'], ['memberId' => 1, 'articleId' => 2, 'content' => 'Comment C'], ['memberId' => 1, 'articleId' => 2, 'content' => 'Comment D'], ['memberId' => 1, 'articleId' => 2, 'content' => 'Comment E'], ['memberId' => 1, 'articleId' => 3, 'content' => 'Comment F'], ['memberId' => 1, 'articleId' => 4, 'content' => 'Comment G'], ['memberId' => 1, 'articleId' => 5, 'content' => 'Comment H'], ['memberId' => 1, 'articleId' => 5, 'content' => 'Comment I'], ['memberId' => 1, 'articleId' => 6, 'content' => 'Comment J']]);

$member = \M\Member::one(1);
$member->relation('a1_01', $method)->and('title = ?', 'CCC')->first()->title == 'CCC' || QQ('Err');

$members = \M\Member::all(['pre-relation' => 'a1_01.comments']);
$ans = [3, 1, 2];
$ans2 = [[2, 3, 1], [1], [2, 1]];
$ans3 = [[['Comment A', 'Comment B'], ['Comment C', 'Comment D', 'Comment E'], ['Comment F']], [['Comment G']], [['Comment H', 'Comment I'], ['Comment J']]];

echo "test3Relation";

foreach ($members as $i => $member) {
  // echo count($member->a1_01) . "\n";
  count($member->a1_01) == $ans[$i] || QQ('Err');

  foreach ($member->a1_01 as $j => $article) {
    // echo count($article->comments) . ', ';
    count($article->comments) == $ans2[$i][$j] || QQ('Err');
    foreach ($article->comments as $k => $comment) {
      // echo $comment->content . "\n";
      $comment->content == $ans3[$i][$j][$k] || QQ('Err');
    }
  }
  // echo "\n";
}

$articles = \M\Article::all(['pre' => 'b1_09']);
$ans = ['A', 'A', 'A', 'B', 'C', 'C'];
foreach ($articles as $i => $article) {
  // echo $article->b1_09->name . "\n";
  $article->b1_09->name == $ans[$i] || QQ('Err');
}

echo " - ok\n";
