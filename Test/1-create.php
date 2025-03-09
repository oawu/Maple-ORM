<?php

use \Orm\Model;
use \Orm\Core\Connection;

include '0.php';

$sql = "CREATE TABLE `CreateUser` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `a0` varchar(190) CHARACTER SET utf8mb4 NULL,
  `a1` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `a2` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',

  `b0` text COLLATE utf8mb4_unicode_ci NULL,
  `b1` text COLLATE utf8mb4_unicode_ci NOT NULL,

  `c0` enum('a','b') COLLATE utf8mb4_unicode_ci NULL,
  `c1` enum('a','b') COLLATE utf8mb4_unicode_ci NOT NULL,
  `c2` enum('a','b') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'a',

  `d0` datetime NULL,
  `d1` datetime NOT NULL,
  `d2` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

  `e0` timestamp NULL,
  `e1` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  `f0` date NULL,
  `f1` date NOT NULL,
  `f2` date NOT NULL DEFAULT '1900-01-01',

  `g0` time NULL,
  `g1` time NOT NULL,
  `g2` time NOT NULL DEFAULT '00:00:00',

  `h0` float NULL,
  `h1` float NOT NULL,
  `h2` float NOT NULL DEFAULT 0.0,

  `i0` double NULL,
  `i1` double NOT NULL,
  `i2` double NOT NULL DEFAULT 0.0,

  `j0` decimal(10,2),
  `j1` decimal(10,2) NOT NULL,
  `j2` decimal(10,2) NOT NULL DEFAULT 0.0,

  `k0` json NULL,
  `k1` json NOT NULL,

  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";


if ($error = Connection::instance()->runQuery($sql)) {
  throw $error;
}


function create() {
  if (\Model\Create\User::truncate() !== true) {
    throw new Exception();
  }


  if (\Model\Create\User::create(['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []]) === null) {
    throw new Exception();
  }


  if (\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null) {
    throw new Exception();
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「a1」不可以為 NULL';
  if (\Model\Create\User::create(['b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「b1」不可以為 NULL';
  if (\Model\Create\User::create(['a1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「c1」不可以為 NULL';
  if (\Model\Create\User::create(['a1' => '', 'b1' => '', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「d1」不可以為 NULL';
  if (\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「f1」不可以為 NULL';
  if (\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「g1」不可以為 NULL';
  if (\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「h1」不可以為 NULL';
  if (\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「i1」不可以為 NULL';
  if (\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「j1」不可以為 NULL';
  if (\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'k1' => []]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「k1」不可以為 NULL';
  if (\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => []]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  // 格式
  $params = ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []];

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「c0」格式為「enum」，選項有「a、b」，欄位值「c」不在選項內';
  if (\Model\Create\User::create(array_merge($params, ['c0' => 'c'])) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「d0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式';
  if (\Model\Create\User::create(array_merge($params, ['d0' => 'c'])) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「e0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式';
  if (\Model\Create\User::create(array_merge($params, ['e0' => 'c'])) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「f0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式';
  if (\Model\Create\User::create(array_merge($params, ['f0' => 'c'])) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '實體 Model 時發生錯誤，錯誤原因：欄位「g0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式';
  if (\Model\Create\User::create(array_merge($params, ['g0' => 'c'])) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  if (\Model\Create\User::create(array_merge($params, ['k0' => ''])) === null) {
    throw new Exception();
  }

  if (\Model\Create\User::create(array_merge($params, ['k0' => 'abc'])) === null) {
    throw new Exception();
  }

  if (\Model\Create\User::create(array_merge($params, ['k0' => '123'])) === null) {
    throw new Exception();
  }

  if (\Model\Create\User::create(array_merge($params, ['k0' => []])) === null) {
    throw new Exception();
  }

  if (\Model\Create\User::create(array_merge($params, ['k0' => ['']])) === null) {
    throw new Exception();
  }

  if (\Model\Create\User::create(array_merge($params, ['k0' => [0]])) === null) {
    throw new Exception();
  }

  if (\Model\Create\User::create(array_merge($params, ['k0' => ['abc']])) === null) {
    throw new Exception();
  }

  if (\Model\Create\User::create(array_merge($params, ['k0' => ['123']])) === null) {
    throw new Exception();
  }

  if (\Model\Create\User::create(array_merge($params, ['k0' => [123]])) === null) {
    throw new Exception();
  }

  if (\Model\Create\User::create(array_merge($params, ['k0' => null])) === null) {
    throw new Exception();
  }

  if (\Model\Create\User::truncate() !== true) {
    throw new Exception();
  }

  if (\Model\Create\User::creates([
    ['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []],
    ['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []],
  ]) != 2) {
    throw new Exception();
  }

  if (\Model\Create\User::creates([
    ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []],
    ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []],
  ]) != 2) {
    throw new Exception();
  }


  if (\Model\Create\User::creates([
    ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]
  ]) !== 1) {
    throw new Exception();
  }

  $error = '新增資料錯誤，錯誤原因：欄位「a1」不可以為 NULL';
  if (\Model\Create\User::creates([$params, ['b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  $error = '新增資料錯誤，錯誤原因：欄位「b1」不可以為 NULL';
  if (\Model\Create\User::creates([['a1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  $error = '新增資料錯誤，錯誤原因：欄位「c1」不可以為 NULL';
  if (\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  $error = '新增資料錯誤，錯誤原因：欄位「d1」不可以為 NULL';
  if (\Model\Create\User::creates([['a1' => '', 'b1' => '', 'c1' => 'a', 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  $error = '新增資料錯誤，錯誤原因：欄位「f1」不可以為 NULL';
  if (\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  $error = '新增資料錯誤，錯誤原因：欄位「g1」不可以為 NULL';
  if (\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  $error = '新增資料錯誤，錯誤原因：欄位「h1」不可以為 NULL';
  if (\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  $error = '新增資料錯誤，錯誤原因：欄位「i1」不可以為 NULL';
  if (\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  $error = '新增資料錯誤，錯誤原因：欄位「j1」不可以為 NULL';
  if (\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'k1' => []], $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  $error = '新增資料錯誤，錯誤原因：欄位「k1」不可以為 NULL';
  if (\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => []], $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }

  $error = '新增資料錯誤，錯誤原因：欄位「c0」格式為「enum」，選項有「a、b」，欄位值「c」不在選項內';
  if (\Model\Create\User::creates([$params, array_merge($params, ['c0' => 'c'])]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  //  && Model::$lastLog != '「d0」欄位值「c」無法轉為 Orm\Core\Plugin\DateTime 格式'
  $error = '新增資料錯誤，錯誤原因：欄位「d0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式';
  if (\Model\Create\User::creates([array_merge($params, ['d0' => 'c']), $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  //  && Model::$lastLog != '「e0」欄位值「c」無法轉為 Orm\Core\Plugin\DateTime 格式'
  $error = '新增資料錯誤，錯誤原因：欄位「e0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式';
  if (\Model\Create\User::creates([$params, array_merge($params, ['e0' => 'c']), $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  //  && Model::$lastLog != '「f0」欄位值「c」無法轉為 Orm\Core\Plugin\DateTime 格式'
  $error = '新增資料錯誤，錯誤原因：欄位「f0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式';
  if (\Model\Create\User::creates([$params, array_merge($params, ['f0' => 'c']), $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  //  && Model::$lastLog != '「g0」欄位值「c」無法轉為 Orm\Core\Plugin\DateTime 格式'
  $error = '新增資料錯誤，錯誤原因：欄位「g0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式';
  if (\Model\Create\User::creates([$params, array_merge($params, ['g0' => 'c']), $params]) === null) {
    if (Model::getLastLog() != $error) {
      throw new Exception();
    }
  }
  if (\Model\Create\User::creates([$params, array_merge($params, ['k0' => '']), $params]) === null) {
    throw new Exception();
  }
  if (\Model\Create\User::creates([$params, array_merge($params, ['k0' => 'abc']), $params]) === null) {
    throw new Exception();
  }
  if (\Model\Create\User::creates([$params, array_merge($params, ['k0' => '123']), $params]) === null) {
    throw new Exception();
  }
  if (\Model\Create\User::creates([$params, array_merge($params, ['k0' => 123]), $params]) === null) {
    throw new Exception();
  }
  if (\Model\Create\User::creates([$params, array_merge($params, ['k0' => []]), $params]) === null) {
    throw new Exception();
  }
  if (\Model\Create\User::creates([$params, array_merge($params, ['k0' => ['']]), $params]) === null) {
    throw new Exception();
  }
  if (\Model\Create\User::creates([$params, array_merge($params, ['k0' => [0]]), $params]) === null) {
    throw new Exception();
  }
  if (\Model\Create\User::creates([$params, array_merge($params, ['k0' => ['abc']]), $params]) === null) {
    throw new Exception();
  }
  if (\Model\Create\User::creates([$params, array_merge($params, ['k0' => ['123']]), $params]) === null) {
    throw new Exception();
  }
  if (\Model\Create\User::creates([$params, array_merge($params, ['k0' => [123]]), $params]) === null) {
    throw new Exception();
  }
  if (\Model\Create\User::creates([$params, array_merge($params, ['k0' => null]), $params]) === null) {
    throw new Exception();
  }

  if (\Model\Create\User::truncate() !== true) {
    throw new Exception();
  }

  // 正常 1
  $user = \Model\Create\User::create(['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []]);
  if ($user === null) {
    throw new Exception();
  }
  if ($user->id !== 1) {
    throw new Exception();
  }
  if ($user->a0 !== '') {
    throw new Exception();
  }
  if ($user->a1 !== '') {
    throw new Exception();
  }
  if ($user->a2 !== '') {
    throw new Exception();
  }
  if ($user->b0 !== '') {
    throw new Exception();
  }
  if ($user->b1 !== '') {
    throw new Exception();
  }
  if ($user->c0 !== 'a') {
    throw new Exception();
  }
  if ($user->c1 !== 'a') {
    throw new Exception();
  }
  if ($user->c2 !== 'a') {
    throw new Exception();
  }
  if ($user->d0->format() !== "2020-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->d1->format() !== "2020-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->d2->format() !== "2020-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->e0->format() !== "2020-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->e1->format() !== "2020-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->f0->format() !== '2020-10-22') {
    throw new Exception();
  }
  if ($user->f1->format() !== '2020-10-22') {
    throw new Exception();
  }
  if ($user->f2->format() !== '2020-10-22') {
    throw new Exception();
  }
  if ($user->g0->format() !== null || $user->g0->getValue() !== '00:00:00') {
    throw new Exception();
  }
  if ($user->g1->format() !== null || $user->g1->getValue() !== '00:00:00') {
    throw new Exception();
  }
  if ($user->g2->format() !== null || $user->g2->getValue() !== '00:00:00') {
    throw new Exception();
  }
  if ($user->h0 !== 1.1) {
    throw new Exception();
  }
  if ($user->h1 !== 1.1) {
    throw new Exception();
  }
  if ($user->h2 !== 1.1) {
    throw new Exception();
  }
  if ($user->i0 !== 1.1) {
    throw new Exception();
  }
  if ($user->i1 !== 1.1) {
    throw new Exception();
  }
  if ($user->i2 !== 1.1) {
    throw new Exception();
  }
  if ($user->j0 !== 1.1) {
    throw new Exception();
  }
  if ($user->j1 !== 1.1) {
    throw new Exception();
  }
  if ($user->j2 !== 1.1) {
    throw new Exception();
  }
  if ($user->k0 !== []) {
    throw new Exception();
  }
  if ($user->k1 !== []) {
    throw new Exception();
  }

  if (\Model\Create\User::truncate() !== true) {
    throw new Exception('Truncate 失敗');
  }

  // // 正常 1
  $user = \Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]);
  if ($user === null) {
    throw new Exception();
  }
  if ($user->id !== 1) {
    throw new Exception();
  }
  if ($user->a0 !== null) {
    throw new Exception();
  }
  if ($user->a1 !== '') {
    throw new Exception();
  }
  if ($user->a2 !== '') {
    throw new Exception();
  }
  if ($user->b0 !== null) {
    throw new Exception();
  }
  if ($user->b1 !== '') {
    throw new Exception();
  }
  if ($user->c0 !== null) {
    throw new Exception();
  }
  if ($user->c1 !== 'a') {
    throw new Exception();
  }
  if ($user->c2 !== 'a') {
    throw new Exception();
  }
  if ($user->d0->getValue() !== null) {
    throw new Exception();
  }
  if ($user->d1->format() !== "2020-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->d2->getValue() === '' || $user->d2->getValue() === null) {
    throw new Exception();
  }
  if ($user->e0->getValue() !== null) {
    throw new Exception();
  }
  if ($user->e1->getValue() === '' || $user->e1->getValue() === null) {
    throw new Exception();
  }
  if ($user->f0->getValue() !== null) {
    throw new Exception();
  }
  if ($user->f1->format() !== '2020-10-22') {
    throw new Exception();
  }
  if ($user->f2->getValue() === '' || $user->f2->getValue() === null) {
    throw new Exception();
  }
  if ($user->g0->getValue() !== null) {
    throw new Exception();
  }
  if ($user->g1->getValue() !== '00:00:00' || $user->g1->format() !== null) {
    throw new Exception();
  }
  if ($user->g2->getValue() === '' || $user->g2->getValue() === null) {
    throw new Exception();
  }
  if ($user->h0 !== null) {
    throw new Exception();
  }
  if ($user->h1 !== 1.1) {
    throw new Exception();
  }
  if ($user->h2 !== 0.0) {
    throw new Exception();
  }
  if ($user->i0 !== null) {
    throw new Exception();
  }
  if ($user->i1 !== 1.1) {
    throw new Exception();
  }
  if ($user->i2 !== 0.0) {
    throw new Exception();
  }
  if ($user->j0 !== null) {
    throw new Exception();
  }
  if ($user->j1 !== 1.1) {
    throw new Exception();
  }
  if ($user->j2 !== 0.0) {
    throw new Exception();
  }
  if ($user->k0 !== null) {
    throw new Exception();
  }
  if ($user->k1 !== []) {
    throw new Exception();
  }

  if (\Model\Create\User::truncate() !== true) {
    throw new Exception('Truncate 失敗');
  }

  if (\Model\Create\User::creates([
    ['a0' => '1', 'a1' => '1', 'a2' => '1', 'b0' => '1', 'b1' => '1', 'c0' => 'b', 'c1' => 'b', 'c2' => 'b', 'd0' => new DateTime("2021-10-22 12:12:12"), 'd1' => new DateTime("2021-10-22 12:12:12"), 'd2' => new DateTime("2021-10-22 12:12:12"), 'e0' => new DateTime("2021-10-22 12:12:12"), 'e1' => new DateTime("2021-10-22 12:12:12"), 'f0' => '2021-10-22', 'f1' => '2021-10-22', 'f2' => '2021-10-22', 'g0' => '01:00:00', 'g1' => '01:00:00', 'g2' => '01:00:00', 'h0' => 2.1, 'h1' => 2.1, 'h2' => 2.1, 'i0' => 2.1, 'i1' => 2.1, 'i2' => 2.1, 'j0' => 2.1, 'j1' => 2.1, 'j2' => 2.1, 'k0' => [1], 'k1' => [2]],
    ['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []],
    ['a0' => '2', 'a1' => '2', 'a2' => '2', 'b0' => '1', 'b1' => '1', 'c0' => 'b', 'c1' => 'b', 'c2' => 'b', 'd0' => new DateTime("2021-10-22 12:12:12"), 'd1' => new DateTime("2021-10-22 12:12:12"), 'd2' => new DateTime("2021-10-22 12:12:12"), 'e0' => new DateTime("2021-10-22 12:12:12"), 'e1' => new DateTime("2021-10-22 12:12:12"), 'f0' => '2021-10-22', 'f1' => '2021-10-22', 'f2' => '2021-10-22', 'g0' => '01:00:00', 'g1' => '01:00:00', 'g2' => '01:00:00', 'h0' => 2.1, 'h1' => 2.1, 'h2' => 2.1, 'i0' => 2.1, 'i1' => 2.1, 'i2' => 2.1, 'j0' => 2.1, 'j1' => 2.1, 'j2' => 2.1, 'k0' => [1], 'k1' => [2]],
  ]) != 3) {
    var_dump(Model::getLastLog());
    exit();
    throw new Exception('Create s 失敗');
  }

  $user = \Model\Create\User::one(1);
  if ($user === null) {
    throw new Exception();
  }
  if ($user->id !== 1) {
    throw new Exception();
  }
  if ($user->a0 !== '1') {
    throw new Exception();
  }
  if ($user->a1 !== '1') {
    throw new Exception();
  }
  if ($user->a2 !== '1') {
    throw new Exception();
  }
  if ($user->b0 !== '1') {
    throw new Exception();
  }
  if ($user->b1 !== '1') {
    throw new Exception();
  }
  if ($user->c0 !== 'b') {
    throw new Exception();
  }
  if ($user->c1 !== 'b') {
    throw new Exception();
  }
  if ($user->c2 !== 'b') {
    throw new Exception();
  }
  if ($user->d0->format() !== "2021-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->d1->format() !== "2021-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->d2->format() !== "2021-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->e0->format() !== "2021-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->e1->format() !== "2021-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->f0->format() !== '2021-10-22') {
    throw new Exception();
  }
  if ($user->f1->format() !== '2021-10-22') {
    throw new Exception();
  }
  if ($user->f2->format() !== '2021-10-22') {
    throw new Exception();
  }
  if ($user->g0->format() !== '01:00:00') {
    throw new Exception();
  }
  if ($user->g1->format() !== '01:00:00') {
    throw new Exception();
  }
  if ($user->g2->format() !== '01:00:00') {
    throw new Exception();
  }
  if ($user->h0 !== 2.1) {
    throw new Exception();
  }
  if ($user->h1 !== 2.1) {
    throw new Exception();
  }
  if ($user->h2 !== 2.1) {
    throw new Exception();
  }
  if ($user->i0 !== 2.1) {
    throw new Exception();
  }
  if ($user->i1 !== 2.1) {
    throw new Exception();
  }
  if ($user->i2 !== 2.1) {
    throw new Exception();
  }
  if ($user->j0 !== 2.1) {
    throw new Exception();
  }
  if ($user->j1 !== 2.1) {
    throw new Exception();
  }
  if ($user->j2 !== 2.1) {
    throw new Exception();
  }
  if ($user->k0 !== [1]) {
    throw new Exception();
  }
  if ($user->k1 !== [2]) {
    throw new Exception();
  }

  $user = \Model\Create\User::one(2);
  if ($user === null) {
    throw new Exception();
  }
  if ($user->id !== 2) {
    throw new Exception();
  }
  if ($user->a0 !== '') {
    throw new Exception();
  }
  if ($user->a1 !== '') {
    throw new Exception();
  }
  if ($user->a2 !== '') {
    throw new Exception();
  }
  if ($user->b0 !== '') {
    throw new Exception();
  }
  if ($user->b1 !== '') {
    throw new Exception();
  }
  if ($user->c0 !== 'a') {
    throw new Exception();
  }
  if ($user->c1 !== 'a') {
    throw new Exception();
  }
  if ($user->c2 !== 'a') {
    throw new Exception();
  }
  if ($user->d0->format() !== "2020-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->d1->format() !== "2020-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->d2->format() !== "2020-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->e0->format() !== "2020-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->e1->format() !== "2020-10-22 12:12:12") {
    throw new Exception();
  }
  if ($user->f0->format() !== '2020-10-22') {
    throw new Exception();
  }
  if ($user->f1->format() !== '2020-10-22') {
    throw new Exception();
  }
  if ($user->f2->format() !== '2020-10-22') {
    throw new Exception();
  }
  if ($user->g0->format() !== null || $user->g0->getValue() !== '00:00:00') {
    throw new Exception();
  }
  if ($user->g1->format() !== null || $user->g1->getValue() !== '00:00:00') {
    throw new Exception();
  }
  if ($user->g2->format() !== null || $user->g2->getValue() !== '00:00:00') {
    throw new Exception();
  }
  if ($user->h0 !== 1.1) {
    throw new Exception();
  }
  if ($user->h1 !== 1.1) {
    throw new Exception();
  }
  if ($user->h2 !== 1.1) {
    throw new Exception();
  }
  if ($user->i0 !== 1.1) {
    throw new Exception();
  }
  if ($user->i1 !== 1.1) {
    throw new Exception();
  }
  if ($user->i2 !== 1.1) {
    throw new Exception();
  }
  if ($user->j0 !== 1.1) {
    throw new Exception();
  }
  if ($user->j1 !== 1.1) {
    throw new Exception();
  }
  if ($user->j2 !== 1.1) {
    throw new Exception();
  }
  if ($user->k0 !== []) {
    throw new Exception();
  }
  if ($user->k1 !== []) {
    throw new Exception();
  }

  Model::close();
}

create();
