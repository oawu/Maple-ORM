<?php

include '0.php';

$sql = "CREATE TABLE `RelationUser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uId` int(10) unsigned NOT NULL DEFAULT 0,

  `name` varchar(190) CHARACTER SET utf8mb4 NULL,

  `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
  
  CREATE TABLE `RelationArticle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aId` int(10) unsigned NOT NULL DEFAULT 0,

  `relationUserId` int(10) unsigned NOT NULL DEFAULT 0,
  `title` varchar(190) CHARACTER SET utf8mb4 NULL,

  `updateAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($error = \M\Core\Connection::instance()->query($sql))
  throw new Exception($error);

function has() {
  // ========
    $u = \M\RelationUser::one();
    if (count($u->articles) != 2) throw new Exception();
    if ($u->articles[0]->id != 2) throw new Exception();
    $as = $u->articles()->where('id', '>', 2)->all();
    if (count($as) != 1) throw new Exception();
    if ($as[0]->id != 3) throw new Exception();
    $a = $u->articles()->where('id', '>', 2)->one();
    if (is_array($a)) throw new Exception();
    if ($a->id != 3)  throw new Exception();
    \M\Model::$query = null;
    count($u->articles);
    if (\M\Model::$query !== null) throw new Exception();

    $u = \M\RelationUser::one();
    if ($u->article->id != 2) throw new Exception();
    $as = $u->article()->where('id', '>', 2)->all();
    if (count($as) != 1) throw new Exception();
    if ($as[0]->id != 3) throw new Exception();
    $a = $u->article()->where('id', '>', 2)->one();
    if (is_array($a)) throw new Exception();
    if ($a->id != 3)  throw new Exception();
    \M\Model::$query = null;
    $tmp = $u->article->id;
    if (\M\Model::$query !== null) throw new Exception();

    $u = \M\RelationUser::last();
    if (count($u->articles) != 0) throw new Exception();
    if ($u->article !== null) throw new Exception();

  // ========
    $u = \M\RelationUser::one();
    if (count($u->article2s) != 1) throw new Exception();
    if ($u->article2s[0]->id != 3) throw new Exception();
    $as = $u->article2s()->where('id', '<', 3)->all();
    if (count($as) != 0) throw new Exception();
    $a = $u->article2s()->where('id', '<', 3)->one();
    if (is_array($a)) throw new Exception();
    \M\Model::$query = null;
    count($u->article2s);
    if (\M\Model::$query !== null) throw new Exception();

    $u = \M\RelationUser::one();
    if ($u->article2->id != 3) throw new Exception();

    $as = $u->article2s()->where('id', '>', 2)->all();
    if (count($as) != 1) throw new Exception();
    $a = $u->article2s()->where('id', '>', 1)->one();
    if (is_array($a)) throw new Exception();
    \M\Model::$query = null;
    $tmp = $u->article2->id;
    if (\M\Model::$query !== null) throw new Exception();

    $u = \M\RelationUser::last();
    if (count($u->article2s) != 0) throw new Exception();
    if ($u->article2 !== null) throw new Exception();

  // ========
    $u = \M\RelationUser::one();
    if (count($u->article3s) != 1) throw new Exception();
    if ($u->article3s[0]->id != 1) throw new Exception();
    $as = $u->article3s()->where('id', '>', 2)->all();
    if (count($as) != 0) throw new Exception();
    $a = $u->article3s()->where('id', '<', 2)->one();
    if (is_array($a)) throw new Exception();
    if ($a->id != 1)  throw new Exception();
    \M\Model::$query = null;
    count($u->article3s);
    if (\M\Model::$query !== null) throw new Exception();

    $u = \M\RelationUser::one();
    if ($u->article3->id != 1) throw new Exception();
    $as = $u->article3()->where('id', '>', 2)->all();
    if (count($as) != 0) throw new Exception();
    $a = $u->article3()->where('id', '<', 2)->one();
    if (is_array($a)) throw new Exception();
    if ($a->id != 1)  throw new Exception();
    \M\Model::$query = null;
    $tmp = $u->article3->id;
    if (\M\Model::$query !== null) throw new Exception();

    $u = \M\RelationUser::last();
    if (count($u->article3s) != 0) throw new Exception();
    if ($u->article3 !== null) throw new Exception();

  // ========
    $u = \M\RelationUser::one();
    if (count($u->article4s) != 1) throw new Exception();
    if ($u->article4s[0]->id != 2) throw new Exception();
    $as = $u->article4s()->where('id', '>', 1)->all();
    if (count($as) != 1) throw new Exception();
    if ($as[0]->id != 2) throw new Exception();
    $a = $u->article4s()->where('id', '>', 1)->one();
    if (is_array($a)) throw new Exception();
    if ($a->id != 2)  throw new Exception();
    \M\Model::$query = null;
    count($u->article4s);
    if (\M\Model::$query !== null) throw new Exception();

    $u = \M\RelationUser::one();
    if ($u->article4->id != 2) throw new Exception();
    $as = $u->article4()->where('id', '>', 1)->all();
    if (count($as) != 1) throw new Exception();
    if ($as[0]->id != 2) throw new Exception();
    $a = $u->article4()->where('id', '>', 1)->one();
    if (is_array($a)) throw new Exception();
    if ($a->id != 2)  throw new Exception();
    \M\Model::$query = null;
    $tmp = $u->article4->id;
    if (\M\Model::$query !== null) throw new Exception();

    $u = \M\RelationUser::last();
    if (count($u->article4s) != 0) throw new Exception();
    if ($u->article4 !== null) throw new Exception();
}
function belongs() {
  // ========
    $a = \M\RelationArticle::one();
    if ($a->user === null) throw new Exception();
    if ($a->user->id != 2) throw new Exception();
    \M\Model::$query = null;
    $tmp = $a->user;
    if (\M\Model::$query !== null) throw new Exception();
    $us = $a->user()->all();
    if (count($us) != 1) throw new Exception();
    $us = $a->user()->where('id', '>', 3)->all();

    if (count($us) != 0) throw new Exception();
    $u = $a->user()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 2) throw new Exception();
    $u = $a->user()->where([3])->one();
    if ($u !== null) throw new Exception();

    $a = \M\RelationArticle::last();
    if ($a->user !== null) throw new Exception();

    // ----------

    $a = \M\RelationArticle::one();
    if (count($a->users) != 1) throw new Exception();
    if ($a->users[0]->id != 2) throw new Exception();
    \M\Model::$query = null;
    count($a->users);
    if (\M\Model::$query !== null) throw new Exception();

    $us = $a->users()->all();
    if (count($us) != 1) throw new Exception();
    $us = $a->users()->where('id', '>', 3)->all();
    if (count($us) != 0) throw new Exception();
    $u = $a->users()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 2) throw new Exception();
    $u = $a->users()->where([3])->one();
    if ($u !== null) throw new Exception();

    $us = $a->users()->where('id', '>', 2)->all();
    if (count($us) != 0) throw new Exception();
    $u = $a->users()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 2) throw new Exception();
    $u = $a->users()->where([3])->one();
    if ($u !== null) throw new Exception();

    $a = \M\RelationArticle::last();
    if ($a->users !== []) throw new Exception();
  // ========
    $a = \M\RelationArticle::one();
    if ($a->user2 === null) throw new Exception();
    if ($a->user2->id != 3) throw new Exception();
    \M\Model::$query = null;
    $tmp = $a->user2;
    if (\M\Model::$query !== null) throw new Exception();
    $us = $a->user2()->all();
    if (count($us) != 1) throw new Exception();
    $us = $a->user2()->where('id', '>', 3)->all();

    if (count($us) != 0) throw new Exception();
    $u = $a->user2()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 3) throw new Exception();
    $u = $a->user2()->where([1])->one();
    if ($u !== null) throw new Exception();

    $a = \M\RelationArticle::last();
    if ($a->user2 !== null) throw new Exception();

    // ----------

    $a = \M\RelationArticle::one();
    if (count($a->user2s) != 1) throw new Exception();
    if ($a->user2s[0]->id != 3) throw new Exception();
    \M\Model::$query = null;
    count($a->user2s);
    if (\M\Model::$query !== null) throw new Exception();

    $us = $a->user2s()->all();
    if (count($us) != 1) throw new Exception();
    $us = $a->user2s()->where('id', '>', 3)->all();
    if (count($us) != 0) throw new Exception();
    $u = $a->user2s()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 3) throw new Exception();
    $u = $a->user2s()->where([1])->one();
    if ($u !== null) throw new Exception();

    $us = $a->user2s()->where('id', '>', 2)->all();
    if (count($us) != 1) throw new Exception();
    $u = $a->user2s()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 3) throw new Exception();
    $u = $a->user2s()->where([2])->one();
    if ($u !== null) throw new Exception();

    $a = \M\RelationArticle::last();
    if ($a->user2s !== []) throw new Exception();
  // ========
    $a = \M\RelationArticle::one();
    if ($a->user4 === null) throw new Exception();
    if ($a->user4->id != 3) throw new Exception();
    \M\Model::$query = null;
    $tmp = $a->user4;
    if (\M\Model::$query !== null) throw new Exception();
    $us = $a->user4()->all();
    if (count($us) != 1) throw new Exception();
    $us = $a->user4()->where('id', '>', 2)->all();

    if (count($us) != 1) throw new Exception();
    if ($us[0]->id != 3) throw new Exception();
    $u = $a->user4()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 3) throw new Exception();
    $u = $a->user4()->where([3])->one();
    if ($u === null) throw new Exception();
    if ($u->id != 3) throw new Exception();

    $a = \M\RelationArticle::last();
    if ($a->user4 !== null) throw new Exception();

    // // ----------

    $a = \M\RelationArticle::one();
    if (count($a->user4s) != 1) throw new Exception();
    if ($a->user4s[0]->id != 3) throw new Exception();
    \M\Model::$query = null;
    count($a->user4s);
    if (\M\Model::$query !== null) throw new Exception();

    $us = $a->user4s()->all();
    if (count($us) != 1) throw new Exception();
    $us = $a->user4s()->where('id', '>', 2)->all();
    if (count($us) != 1) throw new Exception();
    if ($us[0]->id != 3) throw new Exception();
    $u = $a->user4s()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 3) throw new Exception();
    $u = $a->user4s()->where([2, 3, 4])->one();
    if ($u === null) throw new Exception();
    if ($u->id != 3) throw new Exception();

    $us = $a->user4s()->where('id', '>', 2)->all();
    if (count($us) != 1) throw new Exception();
    $u = $a->user4s()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 3) throw new Exception();
    $u = $a->user4s()->where([2, 3])->one();
    if ($u === null) throw new Exception();

    $a = \M\RelationArticle::last();
    if ($a->user4s !== []) throw new Exception();
  // ========
    $a = \M\RelationArticle::one();
    if ($a->user3 === null) throw new Exception();
    if ($a->user3->id != 1) throw new Exception();
    \M\Model::$query = null;
    $tmp = $a->user3;
    if (\M\Model::$query !== null) throw new Exception();
    $us = $a->user3()->all();
    if (count($us) != 2) throw new Exception();
    $us = $a->user3()->where('id', '>', 2)->all();

    if (count($us) != 1) throw new Exception();
    if ($us[0]->id != 4) throw new Exception();
    $u = $a->user3()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 1) throw new Exception();
    $u = $a->user3()->where([2, 4])->one();
    if ($u === null) throw new Exception();
    if ($u->id != 4) throw new Exception();

    $a = \M\RelationArticle::last();
    if ($a->user3 !== null) throw new Exception();

    // // ----------

    $a = \M\RelationArticle::one();
    if (count($a->user3s) != 2) throw new Exception();
    if ($a->user3s[0]->id != 1) throw new Exception();
    \M\Model::$query = null;
    count($a->user3s);
    if (\M\Model::$query !== null) throw new Exception();

    $us = $a->user3s()->all();
    if (count($us) != 2) throw new Exception();
    $us = $a->user3s()->where('id', '>', 2)->all();
    if (count($us) != 1) throw new Exception();
    if ($us[0]->id != 4) throw new Exception();
    $u = $a->user3s()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 1) throw new Exception();
    $u = $a->user3s()->where([2, 4])->one();
    if ($u === null) throw new Exception();
    if ($u->id != 4) throw new Exception();

    $us = $a->user3s()->where('id', '>', 2)->all();
    if (count($us) != 1) throw new Exception();
    $u = $a->user3s()->one();
    if ($u === null) throw new Exception();
    if ($u->id != 1) throw new Exception();
    $u = $a->user3s()->where([2, 3])->one();
    if ($u !== null) throw new Exception();

    $a = \M\RelationArticle::last();
    if ($a->user3s !== []) throw new Exception();
}
function hasMerge() {
  // ===
    $users = \M\RelationUser::relation('article')->all();
    if (!(\M\Model::$query[0] == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?, ?);' && \M\Model::$query[1] == [1, 2, 3, 4, 5]))
      throw new Exception();

    \M\Model::$query = null;
    if ($users[0]->article->id != 2) throw new Exception();
    if ($users[1]->article->id != 1) throw new Exception();
    if ($users[2]->article !== null) throw new Exception();
    if ($users[3]->article !== null) throw new Exception();
    if ($users[4]->article !== null) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();

    $users = \M\RelationUser::relation('articles')->all();
    if (!(\M\Model::$query[0] == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?, ?);' && \M\Model::$query[1] == [1, 2, 3, 4, 5]))
      throw new Exception();

    \M\Model::$query = null;
    if (count($users[0]->articles) != 2) throw new Exception();
    if ($users[0]->articles[0]->id != 2) throw new Exception();
    if ($users[0]->articles[1]->id != 3) throw new Exception();
    if (count($users[1]->articles) != 1) throw new Exception();
    if ($users[1]->articles[0]->id != 1) throw new Exception();
    if (count($users[2]->articles) != 0) throw new Exception();
    if (count($users[3]->articles) != 0) throw new Exception();
    if (count($users[4]->articles) != 0) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();
  // ===
    $users = \M\RelationUser::relation('article2')->all();

    if (!(\M\Model::$query[0] == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?, ?);' && \M\Model::$query[1] == [1, 2, 3, 4, 5]))
      throw new Exception();

    \M\Model::$query = null;
    if ($users[0]->article2->id != 3) throw new Exception();
    if ($users[1]->article2->id != 2) throw new Exception();
    if ($users[2]->article2->id != 1) throw new Exception();
    if ($users[3]->article2 !== null) throw new Exception();
    if ($users[4]->article2 !== null) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();

    $users = \M\RelationUser::relation('article2s')->all();

    if (!(\M\Model::$query[0] == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?, ?);' && \M\Model::$query[1] == [1, 2, 3, 4, 5]))
      throw new Exception();

    \M\Model::$query = null;
    if (count($users[0]->article2s) != 1) throw new Exception();
    if (count($users[1]->article2s) != 1) throw new Exception();
    if (count($users[2]->article2s) != 1) throw new Exception();
    if (count($users[3]->article2s) != 0) throw new Exception();
    if (count($users[4]->article2s) != 0) throw new Exception();
    if ($users[0]->article2s[0]->id != 3) throw new Exception();
    if ($users[1]->article2s[0]->id != 2) throw new Exception();
    if ($users[2]->article2s[0]->id != 1) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();
  // ===
    $users = \M\RelationUser::relation('article3')->all();

    if (!(\M\Model::$query[0] == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?);' && \M\Model::$query[1] == [2, 1, 3, 11]))
      throw new Exception();

    \M\Model::$query = null;
    if ($users[0]->article3->id != 1) throw new Exception();
    if ($users[1]->article3->id != 2) throw new Exception();
    if ($users[2]->article3 !== null) throw new Exception();
    if ($users[3]->article3->id != 1) throw new Exception();
    if ($users[4]->article3 !== null) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();

    $users = \M\RelationUser::relation('article3s')->all();
    if (!(\M\Model::$query[0] == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?);' && \M\Model::$query[1] == [2, 1, 3, 11]))
      throw new Exception();

    \M\Model::$query = null;
    if (count($users[0]->article3s) != 1) throw new Exception();
    if ($users[0]->article3s[0]->id != 1) throw new Exception();
    if (count($users[1]->article3s) != 2) throw new Exception();
    if ($users[1]->article3s[0]->id != 2) throw new Exception();
    if ($users[1]->article3s[1]->id != 3) throw new Exception();
    if (count($users[2]->article3s) != 0) throw new Exception();

    if (count($users[3]->article3s) != 1) throw new Exception();
    if ($users[3]->article3s[0]->id != 1) throw new Exception();
    if (count($users[4]->article3s) != 0) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();
  // ===
    $users = \M\RelationUser::relation('article4')->all();

    if (!(\M\Model::$query[0] == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?);' && \M\Model::$query[1] == [2, 1, 3, 11]))
      throw new Exception();

    \M\Model::$query = null;
    if ($users[0]->article4->id != 2) throw new Exception();
    if ($users[1]->article4->id != 3) throw new Exception();
    if ($users[2]->article4->id != 1) throw new Exception();
    if ($users[3]->article4->id != 2) throw new Exception();
    if ($users[4]->article4 !== null) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();

    $users = \M\RelationUser::relation('article4s')->all();
    if (!(\M\Model::$query[0] == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?);' && \M\Model::$query[1] == [2, 1, 3, 11]))
      throw new Exception();

    \M\Model::$query = null;
    if (count($users[0]->article4s) != 1) throw new Exception();
    if ($users[0]->article4s[0]->id != 2) throw new Exception();
    if (count($users[1]->article4s) != 1) throw new Exception();
    if ($users[1]->article4s[0]->id != 3) throw new Exception();
    if (count($users[2]->article4s) != 1) throw new Exception();
    if ($users[2]->article4s[0]->id != 1) throw new Exception();
    if (count($users[3]->article4s) != 1) throw new Exception();
    if ($users[3]->article4s[0]->id != 2) throw new Exception();
    if (count($users[4]->article4s) != 0) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();
}
function belongsMerge() {
  // ===
    $articles = \M\RelationArticle::relation('user')->all();
    if (!(\M\Model::$query[0] == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?);' && \M\Model::$query[1] == [2, 1, 10]))
      throw new Exception();
    \M\Model::$query = null;
    if ($articles[0]->user->id != 2) throw new Exception();
    if ($articles[1]->user->id != 1) throw new Exception();
    if ($articles[2]->user->id != 1) throw new Exception();
    if ($articles[3]->user !== null) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();

    $articles = \M\RelationArticle::relation('users')->all();
    if (!(\M\Model::$query[0] == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?);' && \M\Model::$query[1] == [2, 1, 10]))
      throw new Exception();
    \M\Model::$query = null;
    if (count($articles[0]->users) != 1) throw new Exception();
    if (count($articles[1]->users) != 1) throw new Exception();
    if (count($articles[2]->users) != 1) throw new Exception();
    if (count($articles[3]->users) != 0) throw new Exception();
    if ($articles[0]->users[0]->id != 2) throw new Exception();
    if ($articles[1]->users[0]->id != 1) throw new Exception();
    if ($articles[2]->users[0]->id != 1) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();

  // ===
    $articles = \M\RelationArticle::relation('user2')->all();
    if (!(\M\Model::$query[0] == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?, ?);' && \M\Model::$query[1] == [3, 2, 1, 10]))
      throw new Exception();
    \M\Model::$query = null;
    if ($articles[0]->user2->id != 3) throw new Exception();
    if ($articles[1]->user2->id != 2) throw new Exception();
    if ($articles[2]->user2->id != 1) throw new Exception();
    if ($articles[3]->user2 !== null) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();

    $articles = \M\RelationArticle::relation('user2s')->all();
    if (!(\M\Model::$query[0] == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?, ?);' && \M\Model::$query[1] == [3, 2, 1, 10]))
      throw new Exception();
    \M\Model::$query = null;
    if (count($articles[0]->user2s) != 1) throw new Exception();
    if (count($articles[1]->user2s) != 1) throw new Exception();
    if (count($articles[2]->user2s) != 1) throw new Exception();
    if (count($articles[3]->user2s) != 0) throw new Exception();
    if ($articles[0]->user2s[0]->id != 3) throw new Exception();
    if ($articles[1]->user2s[0]->id != 2) throw new Exception();
    if ($articles[2]->user2s[0]->id != 1) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();

  // ===
    $articles = \M\RelationArticle::relation('user3')->all();

    if (!(\M\Model::$query[0] == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?);' && \M\Model::$query[1] == [2, 1, 10]))
      throw new Exception();
    \M\Model::$query = null;
    if ($articles[0]->user3->id != 1) throw new Exception();
    if ($articles[1]->user3->id != 2) throw new Exception();
    if ($articles[2]->user3->id != 2) throw new Exception();
    if ($articles[3]->user3 !== null) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();

    $articles = \M\RelationArticle::relation('user3s')->all();
    if (!(\M\Model::$query[0] == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?);' && \M\Model::$query[1] == [2, 1, 10]))
      throw new Exception();
    \M\Model::$query = null;
    if (count($articles[0]->user3s) != 2) throw new Exception();
    if (count($articles[1]->user3s) != 1) throw new Exception();
    if (count($articles[2]->user3s) != 1) throw new Exception();
    if (count($articles[3]->user3s) != 0) throw new Exception();

    if ($articles[0]->user3s[0]->id != 1) throw new Exception();
    if ($articles[0]->user3s[1]->id != 4) throw new Exception();
    if ($articles[1]->user3s[0]->id != 2) throw new Exception();
    if ($articles[2]->user3s[0]->id != 2) throw new Exception();
    if (\M\Model::$query !== null) throw new Exception();

  // ===
  $articles = \M\RelationArticle::relation('user4')->all();
  if (!(\M\Model::$query[0] == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?, ?);' && \M\Model::$query[1] == [3, 2, 1, 10]))
    throw new Exception();
  \M\Model::$query = null;

  if ($articles[0]->user4->id != 3) throw new Exception();
  if ($articles[1]->user4->id != 1) throw new Exception();
  if ($articles[2]->user4->id != 2) throw new Exception();
  if ($articles[3]->user4 !== null) throw new Exception();
  if (\M\Model::$query !== null) throw new Exception();

  $articles = \M\RelationArticle::relation('user4s')->all();
  if (!(\M\Model::$query[0] == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?, ?);' && \M\Model::$query[1] == [3, 2, 1, 10]))
    throw new Exception();
  \M\Model::$query = null;
  if (count($articles[0]->user4s) != 1) throw new Exception();
  if (count($articles[1]->user4s) != 2) throw new Exception();
  if (count($articles[2]->user4s) != 1) throw new Exception();
  if (count($articles[3]->user4s) != 0) throw new Exception();

  if ($articles[0]->user4s[0]->id != 3) throw new Exception();
  if ($articles[1]->user4s[0]->id != 1) throw new Exception();
  if ($articles[1]->user4s[1]->id != 4) throw new Exception();
  if ($articles[2]->user4s[0]->id != 2) throw new Exception();
  if (\M\Model::$query !== null) throw new Exception();
}
function resetRelationDate() {
  \M\RelationUser::truncate();
  \M\RelationUser::creates([
    ['uId' => 2, 'name' => 'OA'],
    ['uId' => 1, 'name' => 'OB'],
    ['uId' => 3, 'name' => 'OC'],
    ['uId' => 2, 'name' => 'OC'],
    ['uId' => 11, 'name' => 'OC'],
  ]);
  \M\RelationArticle::truncate();
  \M\RelationArticle::creates([
    ['aId' => 3, 'relationUserId' => 2, 'title' => 'T1'],
    ['aId' => 2, 'relationUserId' => 1, 'title' => 'T2'],
    ['aId' => 1, 'relationUserId' => 1, 'title' => 'T3'],
    ['aId' => 10, 'relationUserId' => 10, 'title' => 'T3'],
  ]);
}
resetRelationDate();

has();
belongs();
hasMerge();
belongsMerge();
