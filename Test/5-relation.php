<?php

use \Orm\Model;
use \Orm\Core\Connection;

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

if ($error = Connection::instance()->runQuery($sql)) {
  throw $error;
}

function has() {
  // ========
  $u = \Model\Relation\User::one();
  if (count($u->articles) != 2) {
    throw new Exception();
  }
  if ($u->articles[0]->id != 2) {
    throw new Exception();
  }
  $as = $u->articles()->where('id', '>', 2)->all();
  if (count($as) != 1) {
    throw new Exception();
  }
  if ($as[0]->id != 3) {
    throw new Exception();
  }
  $a = $u->articles()->where('id', '>', 2)->one();
  if (is_array($a)) {
    throw new Exception();
  }
  if ($a->id != 3) {
    throw new Exception();
  }

  count($u->articles);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'ccf8bfa9d31fefdb97b8b4a76debbabf';
  if ($log !== $_log) {
    throw new Exception();
  }

  $u = \Model\Relation\User::one();
  if ($u->article->id != 2) {
    throw new Exception();
  }
  $as = $u->article()->where('id', '>', 2)->all();
  if (count($as) != 1) {
    throw new Exception();
  }
  if ($as[0]->id != 3) {
    throw new Exception();
  }
  $a = $u->article()->where('id', '>', 2)->one();
  if (is_array($a)) {
    throw new Exception();
  }
  if ($a->id != 3) {
    throw new Exception();
  }

  $tmp = $u->article->id;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'ccf8bfa9d31fefdb97b8b4a76debbabf';
  if ($log !== $_log) {
    throw new Exception();
  }


  $u = \Model\Relation\User::last();
  if (count($u->articles) != 0) {
    throw new Exception();
  }
  if ($u->article !== null) {
    throw new Exception();
  }

  // ========
  $u = \Model\Relation\User::one();
  if (count($u->article2s) != 1) {
    throw new Exception();
  }
  if ($u->article2s[0]->id != 3) {
    throw new Exception();
  }
  $as = $u->article2s()->where('id', '<', 3)->all();
  if (count($as) != 0) {
    throw new Exception();
  }
  $a = $u->article2s()->where('id', '<', 3)->one();
  if (is_array($a)) {
    throw new Exception();
  }

  count($u->article2s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'ed560b3f9edc96b7b8021072fd58804c';
  if ($log !== $_log) {
    throw new Exception();
  }

  $u = \Model\Relation\User::one();
  if ($u->article2->id != 3) {
    throw new Exception();
  }

  $as = $u->article2s()->where('id', '>', 2)->all();
  if (count($as) != 1) {
    throw new Exception();
  }
  $a = $u->article2s()->where('id', '>', 1)->one();
  if (is_array($a)) {
    throw new Exception();
  }

  $tmp = $u->article2->id;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '96a081300900544b9ad6f15db048b92b';
  if ($log !== $_log) {
    throw new Exception();
  }

  $u = \Model\Relation\User::last();
  if (count($u->article2s) != 0) {
    throw new Exception();
  }
  if ($u->article2 !== null) {
    throw new Exception();
  }

  // ========
  $u = \Model\Relation\User::one();
  if (count($u->article3s) != 1) {
    throw new Exception();
  }
  if ($u->article3s[0]->id != 1) {
    throw new Exception();
  }
  $as = $u->article3s()->where('id', '>', 2)->all();
  if (count($as) != 0) {
    throw new Exception();
  }
  $a = $u->article3s()->where('id', '<', 2)->one();
  if (is_array($a)) {
    throw new Exception();
  }
  if ($a->id != 1) {
    throw new Exception();
  }

  count($u->article3s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'fad64a7f1f8c0def41dc10ce23954f09';
  if ($log !== $_log) {
    throw new Exception();
  }


  $u = \Model\Relation\User::one();
  if ($u->article3->id != 1) {
    throw new Exception();
  }
  $as = $u->article3()->where('id', '>', 2)->all();
  if (count($as) != 0) {
    throw new Exception();
  }
  $a = $u->article3()->where('id', '<', 2)->one();
  if (is_array($a)) {
    throw new Exception();
  }
  if ($a->id != 1) {
    throw new Exception();
  }

  $tmp = $u->article3->id;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'fad64a7f1f8c0def41dc10ce23954f09';
  if ($log !== $_log) {
    throw new Exception();
  }


  $u = \Model\Relation\User::last();
  if (count($u->article3s) != 0) {
    throw new Exception();
  }
  if ($u->article3 !== null) {
    throw new Exception();
  }

  // ========
  $u = \Model\Relation\User::one();
  if (count($u->article4s) != 1) {
    throw new Exception();
  }
  if ($u->article4s[0]->id != 2) {
    throw new Exception();
  }
  $as = $u->article4s()->where('id', '>', 1)->all();
  if (count($as) != 1) {
    throw new Exception();
  }
  if ($as[0]->id != 2) {
    throw new Exception();
  }
  $a = $u->article4s()->where('id', '>', 1)->one();
  if (is_array($a)) {
    throw new Exception();
  }
  if ($a->id != 2) {
    throw new Exception();
  }

  count($u->article4s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '4f96b99edf212d1fe28fa8a4b214a82c';
  if ($log !== $_log) {
    throw new Exception();
  }


  $u = \Model\Relation\User::one();
  if ($u->article4->id != 2) {
    throw new Exception();
  }
  $as = $u->article4()->where('id', '>', 1)->all();
  if (count($as) != 1) {
    throw new Exception();
  }
  if ($as[0]->id != 2) {
    throw new Exception();
  }
  $a = $u->article4()->where('id', '>', 1)->one();
  if (is_array($a)) {
    throw new Exception();
  }
  if ($a->id != 2) {
    throw new Exception();
  }

  $tmp = $u->article4->id;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '4f96b99edf212d1fe28fa8a4b214a82c';
  if ($log !== $_log) {
    throw new Exception();
  }


  $u = \Model\Relation\User::last();
  if (count($u->article4s) != 0) {
    throw new Exception();
  }
  if ($u->article4 !== null) {
    throw new Exception();
  }
}
function belongs()
{
  // ========
  $a = \Model\Relation\Article::one();
  if ($a->user === null) {
    throw new Exception();
  }
  if ($a->user->id != 2) {
    throw new Exception();
  }

  $tmp = $a->user;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '33c0a0c5ac016fbb0e99bd49066a118b';
  if ($log !== $_log) {
    throw new Exception();
  }

  $us = $a->user()->all();
  if (count($us) != 1) {
    throw new Exception();
  }
  $us = $a->user()->where('id', '>', 3)->all();

  if (count($us) != 0) {
    throw new Exception();
  }
  $u = $a->user()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 2) {
    throw new Exception();
  }
  $u = $a->user()->where([3])->one();
  if ($u !== null) {
    throw new Exception();
  }

  $a = \Model\Relation\Article::last();
  if ($a->user !== null) {
    throw new Exception();
  }

  // ----------

  $a = \Model\Relation\Article::one();
  if (count($a->users) != 1) {
    throw new Exception();
  }
  if ($a->users[0]->id != 2) {
    throw new Exception();
  }

  count($a->users);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'd79d1764e7588b31752a69c8ce3bb490';
  if ($log !== $_log) {
    throw new Exception();
  }


  $us = $a->users()->all();
  if (count($us) != 1) {
    throw new Exception();
  }
  $us = $a->users()->where('id', '>', 3)->all();
  if (count($us) != 0) {
    throw new Exception();
  }
  $u = $a->users()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 2) {
    throw new Exception();
  }
  $u = $a->users()->where([3])->one();
  if ($u !== null) {
    throw new Exception();
  }

  $us = $a->users()->where('id', '>', 2)->all();
  if (count($us) != 0) {
    throw new Exception();
  }
  $u = $a->users()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 2) {
    throw new Exception();
  }
  $u = $a->users()->where([3])->one();
  if ($u !== null) {
    throw new Exception();
  }

  $a = \Model\Relation\Article::last();
  if ($a->users !== []) {
    throw new Exception();
  }
  // ========
  $a = \Model\Relation\Article::one();
  if ($a->user2 === null) {
    throw new Exception();
  }
  if ($a->user2->id != 3) {
    throw new Exception();
  }

  $tmp = $a->user2;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'e6e36593620b3cb3346d4270d274e53d';
  if ($log !== $_log) {
    throw new Exception();
  }


  $us = $a->user2()->all();
  if (count($us) != 1) {
    throw new Exception();
  }
  $us = $a->user2()->where('id', '>', 3)->all();

  if (count($us) != 0) {
    throw new Exception();
  }
  $u = $a->user2()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 3) {
    throw new Exception();
  }
  $u = $a->user2()->where([1])->one();
  if ($u !== null) {
    throw new Exception();
  }

  $a = \Model\Relation\Article::last();
  if ($a->user2 !== null) {
    throw new Exception();
  }

  // ----------

  $a = \Model\Relation\Article::one();
  if (count($a->user2s) != 1) {
    throw new Exception();
  }
  if ($a->user2s[0]->id != 3) {
    throw new Exception();
  }

  count($a->user2s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'e94fd9123615266a2fb19bb7db386519';
  if ($log !== $_log) {
    throw new Exception();
  }

  $us = $a->user2s()->all();
  if (count($us) != 1) {
    throw new Exception();
  }
  $us = $a->user2s()->where('id', '>', 3)->all();
  if (count($us) != 0) {
    throw new Exception();
  }
  $u = $a->user2s()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 3) {
    throw new Exception();
  }
  $u = $a->user2s()->where([1])->one();
  if ($u !== null) {
    throw new Exception();
  }

  $us = $a->user2s()->where('id', '>', 2)->all();
  if (count($us) != 1) {
    throw new Exception();
  }
  $u = $a->user2s()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 3) {
    throw new Exception();
  }
  $u = $a->user2s()->where([2])->one();
  if ($u !== null) {
    throw new Exception();
  }

  $a = \Model\Relation\Article::last();
  if ($a->user2s !== []) {
    throw new Exception();
  }
  // ========
  $a = \Model\Relation\Article::one();
  if ($a->user4 === null) {
    throw new Exception();
  }
  if ($a->user4->id != 3) {
    throw new Exception();
  }

  $tmp = $a->user4;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '7cce91a7f58f475e941409df6f67194e';
  if ($log !== $_log) {
    throw new Exception();
  }

  $us = $a->user4()->all();
  if (count($us) != 1) {
    throw new Exception();
  }
  $us = $a->user4()->where('id', '>', 2)->all();

  if (count($us) != 1) {
    throw new Exception();
  }
  if ($us[0]->id != 3) {
    throw new Exception();
  }
  $u = $a->user4()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 3) {
    throw new Exception();
  }
  $u = $a->user4()->where([3])->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 3) {
    throw new Exception();
  }

  $a = \Model\Relation\Article::last();
  if ($a->user4 !== null) {
    throw new Exception();
  }

  // // ----------

  $a = \Model\Relation\Article::one();
  if (count($a->user4s) != 1) {
    throw new Exception();
  }
  if ($a->user4s[0]->id != 3) {
    throw new Exception();
  }

  count($a->user4s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '4624db1c35c9dda94229c967f1fad263';
  if ($log !== $_log) {
    throw new Exception();
  }

  $us = $a->user4s()->all();
  if (count($us) != 1) {
    throw new Exception();
  }
  $us = $a->user4s()->where('id', '>', 2)->all();
  if (count($us) != 1) {
    throw new Exception();
  }
  if ($us[0]->id != 3) {
    throw new Exception();
  }
  $u = $a->user4s()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 3) {
    throw new Exception();
  }
  $u = $a->user4s()->where([2, 3, 4])->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 3) {
    throw new Exception();
  }

  $us = $a->user4s()->where('id', '>', 2)->all();
  if (count($us) != 1) {
    throw new Exception();
  }
  $u = $a->user4s()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 3) {
    throw new Exception();
  }
  $u = $a->user4s()->where([2, 3])->one();
  if ($u === null) {
    throw new Exception();
  }

  $a = \Model\Relation\Article::last();
  if ($a->user4s !== []) {
    throw new Exception();
  }
  // ========
  $a = \Model\Relation\Article::one();
  if ($a->user3 === null) {
    throw new Exception();
  }
  if ($a->user3->id != 1) {
    throw new Exception();
  }

  $tmp = $a->user3;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '4ab815e5ae55be28f268fff2f45d1ab9';
  if ($log !== $_log) {
    throw new Exception();
  }


  $us = $a->user3()->all();
  if (count($us) != 2) {
    throw new Exception();
  }
  $us = $a->user3()->where('id', '>', 2)->all();

  if (count($us) != 1) {
    throw new Exception();
  }
  if ($us[0]->id != 4) {
    throw new Exception();
  }
  $u = $a->user3()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 1) {
    throw new Exception();
  }
  $u = $a->user3()->where([2, 4])->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 4) {
    throw new Exception();
  }

  $a = \Model\Relation\Article::last();
  if ($a->user3 !== null) {
    throw new Exception();
  }

  // // ----------

  $a = \Model\Relation\Article::one();
  if (count($a->user3s) != 2) {
    throw new Exception();
  }
  if ($a->user3s[0]->id != 1) {
    throw new Exception();
  }

  count($a->user3s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'c25e3e6f81f66fa252cd936fb918f499';
  if ($log !== $_log) {
    throw new Exception();
  }

  $us = $a->user3s()->all();
  if (count($us) != 2) {
    throw new Exception();
  }
  $us = $a->user3s()->where('id', '>', 2)->all();
  if (count($us) != 1) {
    throw new Exception();
  }
  if ($us[0]->id != 4) {
    throw new Exception();
  }
  $u = $a->user3s()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 1) {
    throw new Exception();
  }
  $u = $a->user3s()->where([2, 4])->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 4) {
    throw new Exception();
  }

  $us = $a->user3s()->where('id', '>', 2)->all();
  if (count($us) != 1) {
    throw new Exception();
  }
  $u = $a->user3s()->one();
  if ($u === null) {
    throw new Exception();
  }
  if ($u->id != 1) {
    throw new Exception();
  }
  $u = $a->user3s()->where([2, 3])->one();
  if ($u !== null) {
    throw new Exception();
  }

  $a = \Model\Relation\Article::last();
  if ($a->user3s !== []) {
    throw new Exception();
  }
}
function hasMerge()
{
  // ===
  $users = \Model\Relation\User::relation('article')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]))
    throw new Exception();

  if ($users[0]->article->id != 2) {
    throw new Exception();
  }
  if ($users[1]->article->id != 1) {
    throw new Exception();
  }
  if ($users[2]->article !== null) {
    throw new Exception();
  }
  if ($users[3]->article !== null) {
    throw new Exception();
  }
  if ($users[4]->article !== null) {
    throw new Exception();
  }
  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if ($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5])

    $users = \Model\Relation\User::relation('articles')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]))
    throw new Exception();

  if (count($users[0]->articles) != 2) {
    throw new Exception();
  }
  if ($users[0]->articles[0]->id != 2) {
    throw new Exception();
  }
  if ($users[0]->articles[1]->id != 3) {
    throw new Exception();
  }
  if (count($users[1]->articles) != 1) {
    throw new Exception();
  }
  if ($users[1]->articles[0]->id != 1) {
    throw new Exception();
  }
  if (count($users[2]->articles) != 0) {
    throw new Exception();
  }
  if (count($users[3]->articles) != 0) {
    throw new Exception();
  }
  if (count($users[4]->articles) != 0) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]))
    throw new Exception();

  // ===
  $users = \Model\Relation\User::relation('article2')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]))
    throw new Exception();

  if ($users[0]->article2->id != 3) {
    throw new Exception();
  }
  if ($users[1]->article2->id != 2) {
    throw new Exception();
  }
  if ($users[2]->article2->id != 1) {
    throw new Exception();
  }
  if ($users[3]->article2 !== null) {
    throw new Exception();
  }
  if ($users[4]->article2 !== null) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]))
    throw new Exception();

  $users = \Model\Relation\User::relation('article2s')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]))
    throw new Exception();

  if (count($users[0]->article2s) != 1) {
    throw new Exception();
  }
  if (count($users[1]->article2s) != 1) {
    throw new Exception();
  }
  if (count($users[2]->article2s) != 1) {
    throw new Exception();
  }
  if (count($users[3]->article2s) != 0) {
    throw new Exception();
  }
  if (count($users[4]->article2s) != 0) {
    throw new Exception();
  }
  if ($users[0]->article2s[0]->id != 3) {
    throw new Exception();
  }
  if ($users[1]->article2s[0]->id != 2) {
    throw new Exception();
  }
  if ($users[2]->article2s[0]->id != 1) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]))
    throw new Exception();

  // ===
  $users = \Model\Relation\User::relation('article3')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]))
    throw new Exception();

  if ($users[0]->article3->id != 1) {
    throw new Exception();
  }
  if ($users[1]->article3->id != 2) {
    throw new Exception();
  }
  if ($users[2]->article3 !== null) {
    throw new Exception();
  }
  if ($users[3]->article3->id != 1) {
    throw new Exception();
  }
  if ($users[4]->article3 !== null) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]))
    throw new Exception();

  $users = \Model\Relation\User::relation('article3s')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]))
    throw new Exception();

  if (count($users[0]->article3s) != 1) {
    throw new Exception();
  }
  if ($users[0]->article3s[0]->id != 1) {
    throw new Exception();
  }
  if (count($users[1]->article3s) != 2) {
    throw new Exception();
  }
  if ($users[1]->article3s[0]->id != 2) {
    throw new Exception();
  }
  if ($users[1]->article3s[1]->id != 3) {
    throw new Exception();
  }
  if (count($users[2]->article3s) != 0) {
    throw new Exception();
  }

  if (count($users[3]->article3s) != 1) {
    throw new Exception();
  }
  if ($users[3]->article3s[0]->id != 1) {
    throw new Exception();
  }
  if (count($users[4]->article3s) != 0) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]))
    throw new Exception();


  // ===
  $users = \Model\Relation\User::relation('article4')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]))
    throw new Exception();

  if ($users[0]->article4->id != 2) {
    throw new Exception();
  }
  if ($users[1]->article4->id != 3) {
    throw new Exception();
  }
  if ($users[2]->article4->id != 1) {
    throw new Exception();
  }
  if ($users[3]->article4->id != 2) {
    throw new Exception();
  }
  if ($users[4]->article4 !== null) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]))
    throw new Exception();

  $users = \Model\Relation\User::relation('article4s')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]))
    throw new Exception();

  if (count($users[0]->article4s) != 1) {
    throw new Exception();
  }
  if ($users[0]->article4s[0]->id != 2) {
    throw new Exception();
  }
  if (count($users[1]->article4s) != 1) {
    throw new Exception();
  }
  if ($users[1]->article4s[0]->id != 3) {
    throw new Exception();
  }
  if (count($users[2]->article4s) != 1) {
    throw new Exception();
  }
  if ($users[2]->article4s[0]->id != 1) {
    throw new Exception();
  }
  if (count($users[3]->article4s) != 1) {
    throw new Exception();
  }
  if ($users[3]->article4s[0]->id != 2) {
    throw new Exception();
  }
  if (count($users[4]->article4s) != 0) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]))
    throw new Exception();
}
function belongsMerge()
{
  // ===
  $articles = \Model\Relation\Article::relation('user')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?);' && $vals == [2, 1, 10]))
    throw new Exception();

  if ($articles[0]->user->id != 2) {
    throw new Exception();
  }
  if ($articles[1]->user->id != 1) {
    throw new Exception();
  }
  if ($articles[2]->user->id != 1) {
    throw new Exception();
  }
  if ($articles[3]->user !== null) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?);' && $vals == [2, 1, 10]))
    throw new Exception();


  $articles = \Model\Relation\Article::relation('users')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?);' && $vals == [2, 1, 10]))
    throw new Exception();

  if (count($articles[0]->users) != 1) {
    throw new Exception();
  }
  if (count($articles[1]->users) != 1) {
    throw new Exception();
  }
  if (count($articles[2]->users) != 1) {
    throw new Exception();
  }
  if (count($articles[3]->users) != 0) {
    throw new Exception();
  }
  if ($articles[0]->users[0]->id != 2) {
    throw new Exception();
  }
  if ($articles[1]->users[0]->id != 1) {
    throw new Exception();
  }
  if ($articles[2]->users[0]->id != 1) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?);' && $vals == [2, 1, 10]))
    throw new Exception();


  // ===
  $articles = \Model\Relation\Article::relation('user2')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]))
    throw new Exception();

  if ($articles[0]->user2->id != 3) {
    throw new Exception();
  }
  if ($articles[1]->user2->id != 2) {
    throw new Exception();
  }
  if ($articles[2]->user2->id != 1) {
    throw new Exception();
  }
  if ($articles[3]->user2 !== null) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]))
    throw new Exception();


  $articles = \Model\Relation\Article::relation('user2s')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]))
    throw new Exception();

  if (count($articles[0]->user2s) != 1) {
    throw new Exception();
  }
  if (count($articles[1]->user2s) != 1) {
    throw new Exception();
  }
  if (count($articles[2]->user2s) != 1) {
    throw new Exception();
  }
  if (count($articles[3]->user2s) != 0) {
    throw new Exception();
  }
  if ($articles[0]->user2s[0]->id != 3) {
    throw new Exception();
  }
  if ($articles[1]->user2s[0]->id != 2) {
    throw new Exception();
  }
  if ($articles[2]->user2s[0]->id != 1) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]))
    throw new Exception();


  // ===
  $articles = \Model\Relation\Article::relation('user3')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?);' && $vals == [2, 1, 10]))
    throw new Exception();

  if ($articles[0]->user3->id != 1) {
    throw new Exception();
  }
  if ($articles[1]->user3->id != 2) {
    throw new Exception();
  }
  if ($articles[2]->user3->id != 2) {
    throw new Exception();
  }
  if ($articles[3]->user3 !== null) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?);' && $vals == [2, 1, 10]))
    throw new Exception();

  $articles = \Model\Relation\Article::relation('user3s')->all();


  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?);' && $vals == [2, 1, 10]))
    throw new Exception();

  if (count($articles[0]->user3s) != 2) {
    throw new Exception();
  }
  if (count($articles[1]->user3s) != 1) {
    throw new Exception();
  }
  if (count($articles[2]->user3s) != 1) {
    throw new Exception();
  }
  if (count($articles[3]->user3s) != 0) {
    throw new Exception();
  }

  if ($articles[0]->user3s[0]->id != 1) {
    throw new Exception();
  }
  if ($articles[0]->user3s[1]->id != 4) {
    throw new Exception();
  }
  if ($articles[1]->user3s[0]->id != 2) {
    throw new Exception();
  }
  if ($articles[2]->user3s[0]->id != 2) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?);' && $vals == [2, 1, 10]))
    throw new Exception();


  // ===
  $articles = \Model\Relation\Article::relation('user4')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]))
    throw new Exception();

  if ($articles[0]->user4->id != 3) {
    throw new Exception();
  }
  if ($articles[1]->user4->id != 1) {
    throw new Exception();
  }
  if ($articles[2]->user4->id != 2) {
    throw new Exception();
  }
  if ($articles[3]->user4 !== null) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]))
    throw new Exception();


  $articles = \Model\Relation\Article::relation('user4s')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]))
    throw new Exception();

  if (count($articles[0]->user4s) != 1) {
    throw new Exception();
  }
  if (count($articles[1]->user4s) != 2) {
    throw new Exception();
  }
  if (count($articles[2]->user4s) != 1) {
    throw new Exception();
  }
  if (count($articles[3]->user4s) != 0) {
    throw new Exception();
  }

  if ($articles[0]->user4s[0]->id != 3) {
    throw new Exception();
  }
  if ($articles[1]->user4s[0]->id != 1) {
    throw new Exception();
  }
  if ($articles[1]->user4s[1]->id != 4) {
    throw new Exception();
  }
  if ($articles[2]->user4s[0]->id != 2) {
    throw new Exception();
  }

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if (!($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]))
    throw new Exception();
}
function resetRelationDate()
{
  \Model\Relation\User::truncate();
  \Model\Relation\User::creates([
    ['uId' => 2, 'name' => 'OA'],
    ['uId' => 1, 'name' => 'OB'],
    ['uId' => 3, 'name' => 'OC'],
    ['uId' => 2, 'name' => 'OC'],
    ['uId' => 11, 'name' => 'OC'],
  ]);
  \Model\Relation\Article::truncate();
  \Model\Relation\Article::creates([
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
