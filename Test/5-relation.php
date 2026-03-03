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
  title('hasMany / hasOne');

  $u = \Model\Relation\User::one();
  check(count($u->articles) == 2);
  check($u->articles[0]->id == 2);
  $as = $u->articles()->where('id', '>', 2)->all();
  check(count($as) == 1);
  check($as[0]->id == 3);
  $a = $u->articles()->where('id', '>', 2)->one();
  check(!is_array($a));
  check($a->id == 3);

  count($u->articles);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'ccf8bfa9d31fefdb97b8b4a76debbabf';
  check($log === $_log);

  $u = \Model\Relation\User::one();
  check($u->article->id == 2);
  $as = $u->article()->where('id', '>', 2)->all();
  check(count($as) == 1);
  check($as[0]->id == 3);
  $a = $u->article()->where('id', '>', 2)->one();
  check(!is_array($a));
  check($a->id == 3);

  $tmp = $u->article->id;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'ccf8bfa9d31fefdb97b8b4a76debbabf';
  check($log === $_log);


  $u = \Model\Relation\User::last();
  check(count($u->articles) == 0);
  check($u->article === null);

  // ========
  title('hasMany 自訂鍵（aId）');

  $u = \Model\Relation\User::one();
  check(count($u->article2s) == 1);
  check($u->article2s[0]->id == 3);
  $as = $u->article2s()->where('id', '<', 3)->all();
  check(count($as) == 0);
  $a = $u->article2s()->where('id', '<', 3)->one();
  check(!is_array($a));

  count($u->article2s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'ed560b3f9edc96b7b8021072fd58804c';
  check($log === $_log);

  $u = \Model\Relation\User::one();
  check($u->article2->id == 3);

  $as = $u->article2s()->where('id', '>', 2)->all();
  check(count($as) == 1);
  $a = $u->article2s()->where('id', '>', 1)->one();
  check(!is_array($a));

  $tmp = $u->article2->id;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '96a081300900544b9ad6f15db048b92b';
  check($log === $_log);

  $u = \Model\Relation\User::last();
  check(count($u->article2s) == 0);
  check($u->article2 === null);

  // ========
  title('hasMany 自訂鍵（uId→relationUserId）');

  $u = \Model\Relation\User::one();
  check(count($u->article3s) == 1);
  check($u->article3s[0]->id == 1);
  $as = $u->article3s()->where('id', '>', 2)->all();
  check(count($as) == 0);
  $a = $u->article3s()->where('id', '<', 2)->one();
  check(!is_array($a));
  check($a->id == 1);

  count($u->article3s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'fad64a7f1f8c0def41dc10ce23954f09';
  check($log === $_log);


  $u = \Model\Relation\User::one();
  check($u->article3->id == 1);
  $as = $u->article3()->where('id', '>', 2)->all();
  check(count($as) == 0);
  $a = $u->article3()->where('id', '<', 2)->one();
  check(!is_array($a));
  check($a->id == 1);

  $tmp = $u->article3->id;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'fad64a7f1f8c0def41dc10ce23954f09';
  check($log === $_log);


  $u = \Model\Relation\User::last();
  check(count($u->article3s) == 0);
  check($u->article3 === null);

  // ========
  title('hasMany 自訂鍵（uId→aId）');

  $u = \Model\Relation\User::one();
  check(count($u->article4s) == 1);
  check($u->article4s[0]->id == 2);
  $as = $u->article4s()->where('id', '>', 1)->all();
  check(count($as) == 1);
  check($as[0]->id == 2);
  $a = $u->article4s()->where('id', '>', 1)->one();
  check(!is_array($a));
  check($a->id == 2);

  count($u->article4s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '4f96b99edf212d1fe28fa8a4b214a82c';
  check($log === $_log);


  $u = \Model\Relation\User::one();
  check($u->article4->id == 2);
  $as = $u->article4()->where('id', '>', 1)->all();
  check(count($as) == 1);
  check($as[0]->id == 2);
  $a = $u->article4()->where('id', '>', 1)->one();
  check(!is_array($a));
  check($a->id == 2);

  $tmp = $u->article4->id;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '4f96b99edf212d1fe28fa8a4b214a82c';
  check($log === $_log);


  $u = \Model\Relation\User::last();
  check(count($u->article4s) == 0);
  check($u->article4 === null);
}
function belongs()
{
  // ========
  title('belongsTo');

  $a = \Model\Relation\Article::one();
  check($a->user !== null);
  check($a->user->id == 2);

  $tmp = $a->user;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '33c0a0c5ac016fbb0e99bd49066a118b';
  check($log === $_log);

  $us = $a->user()->all();
  check(count($us) == 1);
  $us = $a->user()->where('id', '>', 3)->all();

  check(count($us) == 0);
  $u = $a->user()->one();
  check($u !== null);
  check($u->id == 2);
  $u = $a->user()->where([3])->one();
  check($u === null);

  $a = \Model\Relation\Article::last();
  check($a->user === null);

  // ----------

  $a = \Model\Relation\Article::one();
  check(count($a->users) == 1);
  check($a->users[0]->id == 2);

  count($a->users);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'd79d1764e7588b31752a69c8ce3bb490';
  check($log === $_log);


  $us = $a->users()->all();
  check(count($us) == 1);
  $us = $a->users()->where('id', '>', 3)->all();
  check(count($us) == 0);
  $u = $a->users()->one();
  check($u !== null);
  check($u->id == 2);
  $u = $a->users()->where([3])->one();
  check($u === null);

  $us = $a->users()->where('id', '>', 2)->all();
  check(count($us) == 0);
  $u = $a->users()->one();
  check($u !== null);
  check($u->id == 2);
  $u = $a->users()->where([3])->one();
  check($u === null);

  $a = \Model\Relation\Article::last();
  check($a->users === []);

  // ========
  title('belongsTo 自訂鍵（aId）');

  $a = \Model\Relation\Article::one();
  check($a->user2 !== null);
  check($a->user2->id == 3);

  $tmp = $a->user2;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'e6e36593620b3cb3346d4270d274e53d';
  check($log === $_log);


  $us = $a->user2()->all();
  check(count($us) == 1);
  $us = $a->user2()->where('id', '>', 3)->all();

  check(count($us) == 0);
  $u = $a->user2()->one();
  check($u !== null);
  check($u->id == 3);
  $u = $a->user2()->where([1])->one();
  check($u === null);

  $a = \Model\Relation\Article::last();
  check($a->user2 === null);

  // ----------

  $a = \Model\Relation\Article::one();
  check(count($a->user2s) == 1);
  check($a->user2s[0]->id == 3);

  count($a->user2s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'e94fd9123615266a2fb19bb7db386519';
  check($log === $_log);

  $us = $a->user2s()->all();
  check(count($us) == 1);
  $us = $a->user2s()->where('id', '>', 3)->all();
  check(count($us) == 0);
  $u = $a->user2s()->one();
  check($u !== null);
  check($u->id == 3);
  $u = $a->user2s()->where([1])->one();
  check($u === null);

  $us = $a->user2s()->where('id', '>', 2)->all();
  check(count($us) == 1);
  $u = $a->user2s()->one();
  check($u !== null);
  check($u->id == 3);
  $u = $a->user2s()->where([2])->one();
  check($u === null);

  $a = \Model\Relation\Article::last();
  check($a->user2s === []);

  // ========
  title('belongsTo 自訂鍵（relationUserId→uId）');

  $a = \Model\Relation\Article::one();
  check($a->user4 !== null);
  check($a->user4->id == 3);

  $tmp = $a->user4;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '7cce91a7f58f475e941409df6f67194e';
  check($log === $_log);

  $us = $a->user4()->all();
  check(count($us) == 1);
  $us = $a->user4()->where('id', '>', 2)->all();

  check(count($us) == 1);
  check($us[0]->id == 3);
  $u = $a->user4()->one();
  check($u !== null);
  check($u->id == 3);
  $u = $a->user4()->where([3])->one();
  check($u !== null);
  check($u->id == 3);

  $a = \Model\Relation\Article::last();
  check($a->user4 === null);

  // // ----------

  $a = \Model\Relation\Article::one();
  check(count($a->user4s) == 1);
  check($a->user4s[0]->id == 3);

  count($a->user4s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '4624db1c35c9dda94229c967f1fad263';
  check($log === $_log);

  $us = $a->user4s()->all();
  check(count($us) == 1);
  $us = $a->user4s()->where('id', '>', 2)->all();
  check(count($us) == 1);
  check($us[0]->id == 3);
  $u = $a->user4s()->one();
  check($u !== null);
  check($u->id == 3);
  $u = $a->user4s()->where([2, 3, 4])->one();
  check($u !== null);
  check($u->id == 3);

  $us = $a->user4s()->where('id', '>', 2)->all();
  check(count($us) == 1);
  $u = $a->user4s()->one();
  check($u !== null);
  check($u->id == 3);
  $u = $a->user4s()->where([2, 3])->one();
  check($u !== null);

  $a = \Model\Relation\Article::last();
  check($a->user4s === []);

  // ========
  title('belongsTo 自訂鍵（uId）');

  $a = \Model\Relation\Article::one();
  check($a->user3 !== null);
  check($a->user3->id == 1);

  $tmp = $a->user3;

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = '4ab815e5ae55be28f268fff2f45d1ab9';
  check($log === $_log);


  $us = $a->user3()->all();
  check(count($us) == 2);
  $us = $a->user3()->where('id', '>', 2)->all();

  check(count($us) == 1);
  check($us[0]->id == 4);
  $u = $a->user3()->one();
  check($u !== null);
  check($u->id == 1);
  $u = $a->user3()->where([2, 4])->one();
  check($u !== null);
  check($u->id == 4);

  $a = \Model\Relation\Article::last();
  check($a->user3 === null);

  // // ----------

  $a = \Model\Relation\Article::one();
  check(count($a->user3s) == 2);
  check($a->user3s[0]->id == 1);

  count($a->user3s);

  $log = Model::getLastQueryLog();
  $log = md5(json_encode([$log['sql'], $log['vals']]));
  $_log = 'c25e3e6f81f66fa252cd936fb918f499';
  check($log === $_log);

  $us = $a->user3s()->all();
  check(count($us) == 2);
  $us = $a->user3s()->where('id', '>', 2)->all();
  check(count($us) == 1);
  check($us[0]->id == 4);
  $u = $a->user3s()->one();
  check($u !== null);
  check($u->id == 1);
  $u = $a->user3s()->where([2, 4])->one();
  check($u !== null);
  check($u->id == 4);

  $us = $a->user3s()->where('id', '>', 2)->all();
  check(count($us) == 1);
  $u = $a->user3s()->one();
  check($u !== null);
  check($u->id == 1);
  $u = $a->user3s()->where([2, 3])->one();
  check($u === null);

  $a = \Model\Relation\Article::last();
  check($a->user3s === []);
}
function hasMerge()
{
  // ===
  title('Eager Loading（has）');

  $users = \Model\Relation\User::relation('article')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]);

  check($users[0]->article->id == 2);
  check($users[1]->article->id == 1);
  check($users[2]->article === null);
  check($users[3]->article === null);
  check($users[4]->article === null);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  if ($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5])

    $users = \Model\Relation\User::relation('articles')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]);

  check(count($users[0]->articles) == 2);
  check($users[0]->articles[0]->id == 2);
  check($users[0]->articles[1]->id == 3);
  check(count($users[1]->articles) == 1);
  check($users[1]->articles[0]->id == 1);
  check(count($users[2]->articles) == 0);
  check(count($users[3]->articles) == 0);
  check(count($users[4]->articles) == 0);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]);

  // ===
  $users = \Model\Relation\User::relation('article2')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]);

  check($users[0]->article2->id == 3);
  check($users[1]->article2->id == 2);
  check($users[2]->article2->id == 1);
  check($users[3]->article2 === null);
  check($users[4]->article2 === null);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]);

  $users = \Model\Relation\User::relation('article2s')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]);

  check(count($users[0]->article2s) == 1);
  check(count($users[1]->article2s) == 1);
  check(count($users[2]->article2s) == 1);
  check(count($users[3]->article2s) == 0);
  check(count($users[4]->article2s) == 0);
  check($users[0]->article2s[0]->id == 3);
  check($users[1]->article2s[0]->id == 2);
  check($users[2]->article2s[0]->id == 1);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?, ?);' && $vals == [1, 2, 3, 4, 5]);

  // ===
  $users = \Model\Relation\User::relation('article3')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]);

  check($users[0]->article3->id == 1);
  check($users[1]->article3->id == 2);
  check($users[2]->article3 === null);
  check($users[3]->article3->id == 1);
  check($users[4]->article3 === null);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]);

  $users = \Model\Relation\User::relation('article3s')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]);

  check(count($users[0]->article3s) == 1);
  check($users[0]->article3s[0]->id == 1);
  check(count($users[1]->article3s) == 2);
  check($users[1]->article3s[0]->id == 2);
  check($users[1]->article3s[1]->id == 3);
  check(count($users[2]->article3s) == 0);

  check(count($users[3]->article3s) == 1);
  check($users[3]->article3s[0]->id == 1);
  check(count($users[4]->article3s) == 0);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`relationUserId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]);


  // ===
  $users = \Model\Relation\User::relation('article4')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]);

  check($users[0]->article4->id == 2);
  check($users[1]->article4->id == 3);
  check($users[2]->article4->id == 1);
  check($users[3]->article4->id == 2);
  check($users[4]->article4 === null);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]);

  $users = \Model\Relation\User::relation('article4s')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]);

  check(count($users[0]->article4s) == 1);
  check($users[0]->article4s[0]->id == 2);
  check(count($users[1]->article4s) == 1);
  check($users[1]->article4s[0]->id == 3);
  check(count($users[2]->article4s) == 1);
  check($users[2]->article4s[0]->id == 1);
  check(count($users[3]->article4s) == 1);
  check($users[3]->article4s[0]->id == 2);
  check(count($users[4]->article4s) == 0);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationArticle`.* FROM `RelationArticle` WHERE `RelationArticle`.`aId` IN (?, ?, ?, ?);' && $vals == [2, 1, 3, 11]);
}
function belongsMerge()
{
  // ===
  title('Eager Loading（belongs）');

  $articles = \Model\Relation\Article::relation('user')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?);' && $vals == [2, 1, 10]);

  check($articles[0]->user->id == 2);
  check($articles[1]->user->id == 1);
  check($articles[2]->user->id == 1);
  check($articles[3]->user === null);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?);' && $vals == [2, 1, 10]);


  $articles = \Model\Relation\Article::relation('users')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?);' && $vals == [2, 1, 10]);

  check(count($articles[0]->users) == 1);
  check(count($articles[1]->users) == 1);
  check(count($articles[2]->users) == 1);
  check(count($articles[3]->users) == 0);
  check($articles[0]->users[0]->id == 2);
  check($articles[1]->users[0]->id == 1);
  check($articles[2]->users[0]->id == 1);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?);' && $vals == [2, 1, 10]);


  // ===
  $articles = \Model\Relation\Article::relation('user2')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]);

  check($articles[0]->user2->id == 3);
  check($articles[1]->user2->id == 2);
  check($articles[2]->user2->id == 1);
  check($articles[3]->user2 === null);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]);


  $articles = \Model\Relation\Article::relation('user2s')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]);

  check(count($articles[0]->user2s) == 1);
  check(count($articles[1]->user2s) == 1);
  check(count($articles[2]->user2s) == 1);
  check(count($articles[3]->user2s) == 0);
  check($articles[0]->user2s[0]->id == 3);
  check($articles[1]->user2s[0]->id == 2);
  check($articles[2]->user2s[0]->id == 1);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`id` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]);


  // ===
  $articles = \Model\Relation\Article::relation('user3')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?);' && $vals == [2, 1, 10]);

  check($articles[0]->user3->id == 1);
  check($articles[1]->user3->id == 2);
  check($articles[2]->user3->id == 2);
  check($articles[3]->user3 === null);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?);' && $vals == [2, 1, 10]);

  $articles = \Model\Relation\Article::relation('user3s')->all();


  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?);' && $vals == [2, 1, 10]);

  check(count($articles[0]->user3s) == 2);
  check(count($articles[1]->user3s) == 1);
  check(count($articles[2]->user3s) == 1);
  check(count($articles[3]->user3s) == 0);

  check($articles[0]->user3s[0]->id == 1);
  check($articles[0]->user3s[1]->id == 4);
  check($articles[1]->user3s[0]->id == 2);
  check($articles[2]->user3s[0]->id == 2);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?);' && $vals == [2, 1, 10]);


  // ===
  $articles = \Model\Relation\Article::relation('user4')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]);

  check($articles[0]->user4->id == 3);
  check($articles[1]->user4->id == 1);
  check($articles[2]->user4->id == 2);
  check($articles[3]->user4 === null);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]);


  $articles = \Model\Relation\Article::relation('user4s')->all();

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]);

  check(count($articles[0]->user4s) == 1);
  check(count($articles[1]->user4s) == 2);
  check(count($articles[2]->user4s) == 1);
  check(count($articles[3]->user4s) == 0);

  check($articles[0]->user4s[0]->id == 3);
  check($articles[1]->user4s[0]->id == 1);
  check($articles[1]->user4s[1]->id == 4);
  check($articles[2]->user4s[0]->id == 2);

  ['sql' => $sql, 'vals' => $vals] = Model::getLastQueryLog();
  check($sql == 'SELECT `RelationUser`.* FROM `RelationUser` WHERE `RelationUser`.`uId` IN (?, ?, ?, ?);' && $vals == [3, 2, 1, 10]);
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
