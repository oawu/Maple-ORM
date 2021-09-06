<?php

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

if ($error = \M\Core\Connection::instance()->query($sql))
  throw new Exception($error);

function create() {
  
  if (\M\CreateUser::truncate() !== true)
    throw new Exception('Truncate 失敗');
  // 正常 1
  if (\M\CreateUser::create(['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []]) === null) throw new Exception('Create 失敗');
  // 正常 2
  if (\M\CreateUser::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null) throw new Exception('Create 失敗');
  
  // 缺欄位
  if (\M\CreateUser::create(['b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null && \M\Model::$lastLog != '「a1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(['a1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null && \M\Model::$lastLog != '「b1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(['a1' => '', 'b1' => '', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null && \M\Model::$lastLog != '「c1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null && \M\Model::$lastLog != '「d1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null && \M\Model::$lastLog != '「f1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null && \M\Model::$lastLog != '「g1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null && \M\Model::$lastLog != '「h1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'j1' => 1.1, 'k1' => []]) === null && \M\Model::$lastLog != '「i1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'k1' => []]) === null && \M\Model::$lastLog != '「j1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => []]) === null && \M\Model::$lastLog != '「k1」欄位不可以為 NULL') throw new Exception('Create 失敗');

  // 格式
  $params = ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []];
  if (\M\CreateUser::create(array_merge($params, ['c0' => 'c'])) === null && \M\Model::$lastLog != '「c0」欄位格式為「enum」，選項有「a、b」，您給予的值為：c，不在選項內') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['d0' => 'c'])) === null && \M\Model::$lastLog != '「d0」欄位值「c」無法轉為 M\Core\Plugin\DateTime 格式') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['e0' => 'c'])) === null && \M\Model::$lastLog != '「e0」欄位值「c」無法轉為 M\Core\Plugin\DateTime 格式') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['f0' => 'c'])) === null && \M\Model::$lastLog != '「f0」欄位值「c」無法轉為 M\Core\Plugin\DateTime 格式') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['g0' => 'c'])) === null && \M\Model::$lastLog != '「g0」欄位值「c」無法轉為 M\Core\Plugin\DateTime 格式') throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['k0' => ''])) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['k0' => 'abc'])) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['k0' => '123'])) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['k0' => 123])) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['k0' => []])) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['k0' => ['']])) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['k0' => [0]])) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['k0' => ['abc']])) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['k0' => ['123']])) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['k0' => ['123']])) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::create(array_merge($params, ['k0' => null])) === null) throw new Exception('Create 失敗');

  if (\M\CreateUser::truncate() !== true)
    throw new Exception('Truncate 失敗');

  if (\M\CreateUser::creates([
    ['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []],
    ['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []],
  ]) != 2) throw new Exception('Create s 失敗');

  if (\M\CreateUser::creates([
    ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []],
    ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []],
  ]) != 2) throw new Exception('Create s 失敗');
    
  if (\M\CreateUser::creates([
    ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]
  ]) !== 1)
    throw new Exception('Create s 失敗');

  if (\M\CreateUser::creates([$params, ['b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]]) === null && \M\Model::$lastLog != '「a1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([['a1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null && \M\Model::$lastLog != '「b1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, ['a1' => '', 'b1' => '', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null && \M\Model::$lastLog != '「c1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([['a1' => '', 'b1' => '', 'c1' => 'a', 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]]) === null && \M\Model::$lastLog != '「d1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null && \M\Model::$lastLog != '「f1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null && \M\Model::$lastLog != '「g1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null && \M\Model::$lastLog != '「h1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]) === null && \M\Model::$lastLog != '「i1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'k1' => []], $params]) === null && \M\Model::$lastLog != '「j1」欄位不可以為 NULL') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => []], $params]) === null && \M\Model::$lastLog != '「k1」欄位不可以為 NULL') throw new Exception('Create 失敗');

  if (\M\CreateUser::creates([$params, array_merge($params, ['c0' => 'c'])]) === null && \M\Model::$lastLog != '「c0」欄位格式為「enum」，選項有「a、b」，您給予的值為：c，不在選項內') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([array_merge($params, ['d0' => 'c']), $params]) === null && \M\Model::$lastLog != '「d0」欄位值「c」無法轉為 M\Core\Plugin\DateTime 格式') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['e0' => 'c']), $params]) === null && \M\Model::$lastLog != '「e0」欄位值「c」無法轉為 M\Core\Plugin\DateTime 格式') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['f0' => 'c']), $params]) === null && \M\Model::$lastLog != '「f0」欄位值「c」無法轉為 M\Core\Plugin\DateTime 格式') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['g0' => 'c']), $params]) === null && \M\Model::$lastLog != '「g0」欄位值「c」無法轉為 M\Core\Plugin\DateTime 格式') throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['k0' => '']), $params]) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['k0' => 'abc']), $params]) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['k0' => '123']), $params]) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['k0' => 123]), $params]) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['k0' => []]), $params]) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['k0' => ['']]), $params]) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['k0' => [0]]), $params]) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['k0' => ['abc']]), $params]) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['k0' => ['123']]), $params]) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['k0' => ['123']]), $params]) === null) throw new Exception('Create 失敗');
  if (\M\CreateUser::creates([$params, array_merge($params, ['k0' => null]), $params]) === null) throw new Exception('Create 失敗');

  if (\M\CreateUser::truncate() !== true)
    throw new Exception('Truncate 失敗');

  // 正常 1
  $user = \M\CreateUser::create(['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []]);
  if ($user === null)                                throw new Exception('Create 失敗');
  if ($user->id !== 1)                               throw new Exception('Create result 失敗');
  if ($user->a0 !== '')                              throw new Exception('Create result 失敗');
  if ($user->a1 !== '')                              throw new Exception('Create result 失敗');
  if ($user->a2 !== '')                              throw new Exception('Create result 失敗');
  if ($user->b0 !== '')                              throw new Exception('Create result 失敗');
  if ($user->b1 !== '')                              throw new Exception('Create result 失敗');
  if ($user->c0 !== 'a')                             throw new Exception('Create result 失敗');
  if ($user->c1 !== 'a')                             throw new Exception('Create result 失敗');
  if ($user->c2 !== 'a')                             throw new Exception('Create result 失敗');
  if ($user->d0->format() !== "2020-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->d1->format() !== "2020-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->d2->format() !== "2020-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->e0->format() !== "2020-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->e1->format() !== "2020-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->f0->format() !== '2020-10-22')          throw new Exception('Create result 失敗');
  if ($user->f1->format() !== '2020-10-22')          throw new Exception('Create result 失敗');
  if ($user->f2->format() !== '2020-10-22')          throw new Exception('Create result 失敗');
  if ($user->g0->format() !== '00:00:00')            throw new Exception('Create result 失敗');
  if ($user->g1->format() !== '00:00:00')            throw new Exception('Create result 失敗');
  if ($user->g2->format() !== '00:00:00')            throw new Exception('Create result 失敗');
  if ($user->h0 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->h1 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->h2 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->i0 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->i1 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->i2 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->j0 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->j1 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->j2 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->k0 !== [])                              throw new Exception('Create result 失敗');
  if ($user->k1 !== [])                              throw new Exception('Create result 失敗');

  if (\M\CreateUser::truncate() !== true)
    throw new Exception('Truncate 失敗');

  // 正常 1
  $user = \M\CreateUser::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]);
  if ($user === null)                                throw new Exception('Create 失敗');
  if ($user->id !== 1)                               throw new Exception('Create result 失敗');
  if ($user->a0 !== null)                            throw new Exception('Create result 失敗');
  if ($user->a1 !== '')                              throw new Exception('Create result 失敗');
  if ($user->a2 !== '')                              throw new Exception('Create result 失敗');
  if ($user->b0 !== null)                            throw new Exception('Create result 失敗');
  if ($user->b1 !== '')                              throw new Exception('Create result 失敗');
  if ($user->c0 !== null)                            throw new Exception('Create result 失敗');
  if ($user->c1 !== 'a')                             throw new Exception('Create result 失敗');
  if ($user->c2 !== 'a')                             throw new Exception('Create result 失敗');
  if ($user->d0->value !== null)                     throw new Exception('Create result 失敗');
  if ($user->d1->format() !== "2020-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->d2->isEmpty() !== false)                throw new Exception('Create result 失敗');
  if ($user->e0->value !== null)                     throw new Exception('Create result 失敗');
  if ($user->e1->isEmpty() !== false)                throw new Exception('Create result 失敗');
  if ($user->f0->value !== null)                     throw new Exception('Create result 失敗');
  if ($user->f1->format() !== '2020-10-22')          throw new Exception('Create result 失敗');
  if ($user->f2->isEmpty() !== false)                throw new Exception('Create result 失敗');
  if ($user->g0->value !== null)                     throw new Exception('Create result 失敗');
  if ($user->g1->format() !== '00:00:00')            throw new Exception('Create result 失敗');
  if ($user->g2->isEmpty() !== false)                throw new Exception('Create result 失敗');
  if ($user->h0 !== null)                            throw new Exception('Create result 失敗');
  if ($user->h1 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->h2 !== 0.0)                             throw new Exception('Create result 失敗');
  if ($user->i0 !== null)                            throw new Exception('Create result 失敗');
  if ($user->i1 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->i2 !== 0.0)                             throw new Exception('Create result 失敗');
  if ($user->j0 !== null)                            throw new Exception('Create result 失敗');
  if ($user->j1 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->j2 !== 0.0)                             throw new Exception('Create result 失敗');
  if ($user->k0 !== null)                            throw new Exception('Create result 失敗');
  if ($user->k1 !== [])                              throw new Exception('Create result 失敗');

  if (\M\CreateUser::truncate() !== true)
    throw new Exception('Truncate 失敗');

  if (\M\CreateUser::creates([
    ['a0' => '1', 'a1' => '1', 'a2' => '1', 'b0' => '1', 'b1' => '1', 'c0' => 'b', 'c1' => 'b', 'c2' => 'b', 'd0' => new DateTime("2021-10-22 12:12:12"), 'd1' => new DateTime("2021-10-22 12:12:12"), 'd2' => new DateTime("2021-10-22 12:12:12"), 'e0' => new DateTime("2021-10-22 12:12:12"), 'e1' => new DateTime("2021-10-22 12:12:12"), 'f0' => '2021-10-22', 'f1' => '2021-10-22', 'f2' => '2021-10-22', 'g0' => '01:00:00', 'g1' => '01:00:00', 'g2' => '01:00:00', 'h0' => 2.1, 'h1' => 2.1, 'h2' => 2.1, 'i0' => 2.1, 'i1' => 2.1, 'i2' => 2.1, 'j0' => 2.1, 'j1' => 2.1, 'j2' => 2.1, 'k0' => [1], 'k1' => [2]],
    ['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []],
    ['a0' => '2', 'a1' => '2', 'a2' => '2', 'b0' => '1', 'b1' => '1', 'c0' => 'b', 'c1' => 'b', 'c2' => 'b', 'd0' => new DateTime("2021-10-22 12:12:12"), 'd1' => new DateTime("2021-10-22 12:12:12"), 'd2' => new DateTime("2021-10-22 12:12:12"), 'e0' => new DateTime("2021-10-22 12:12:12"), 'e1' => new DateTime("2021-10-22 12:12:12"), 'f0' => '2021-10-22', 'f1' => '2021-10-22', 'f2' => '2021-10-22', 'g0' => '01:00:00', 'g1' => '01:00:00', 'g2' => '01:00:00', 'h0' => 2.1, 'h1' => 2.1, 'h2' => 2.1, 'i0' => 2.1, 'i1' => 2.1, 'i2' => 2.1, 'j0' => 2.1, 'j1' => 2.1, 'j2' => 2.1, 'k0' => [1], 'k1' => [2]],
  ]) != 3) throw new Exception('Create s 失敗');

  $user = \M\CreateUser::one(1);
  if ($user === null)                                throw new Exception('Create 失敗');
  if ($user->id !== 1)                               throw new Exception('Create result 失敗');
  if ($user->a0 !== '1')                             throw new Exception('Create result 失敗');
  if ($user->a1 !== '1')                             throw new Exception('Create result 失敗');
  if ($user->a2 !== '1')                             throw new Exception('Create result 失敗');
  if ($user->b0 !== '1')                             throw new Exception('Create result 失敗');
  if ($user->b1 !== '1')                             throw new Exception('Create result 失敗');
  if ($user->c0 !== 'b')                             throw new Exception('Create result 失敗');
  if ($user->c1 !== 'b')                             throw new Exception('Create result 失敗');
  if ($user->c2 !== 'b')                             throw new Exception('Create result 失敗');
  if ($user->d0->format() !== "2021-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->d1->format() !== "2021-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->d2->format() !== "2021-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->e0->format() !== "2021-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->e1->format() !== "2021-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->f0->format() !== '2021-10-22')          throw new Exception('Create result 失敗');
  if ($user->f1->format() !== '2021-10-22')          throw new Exception('Create result 失敗');
  if ($user->f2->format() !== '2021-10-22')          throw new Exception('Create result 失敗');
  if ($user->g0->format() !== '01:00:00')            throw new Exception('Create result 失敗');
  if ($user->g1->format() !== '01:00:00')            throw new Exception('Create result 失敗');
  if ($user->g2->format() !== '01:00:00')            throw new Exception('Create result 失敗');
  if ($user->h0 !== 2.1)                             throw new Exception('Create result 失敗');
  if ($user->h1 !== 2.1)                             throw new Exception('Create result 失敗');
  if ($user->h2 !== 2.1)                             throw new Exception('Create result 失敗');
  if ($user->i0 !== 2.1)                             throw new Exception('Create result 失敗');
  if ($user->i1 !== 2.1)                             throw new Exception('Create result 失敗');
  if ($user->i2 !== 2.1)                             throw new Exception('Create result 失敗');
  if ($user->j0 !== 2.1)                             throw new Exception('Create result 失敗');
  if ($user->j1 !== 2.1)                             throw new Exception('Create result 失敗');
  if ($user->j2 !== 2.1)                             throw new Exception('Create result 失敗');
  if ($user->k0 !== [1])                             throw new Exception('Create result 失敗');
  if ($user->k1 !== [2])                             throw new Exception('Create result 失敗');

  $user = \M\CreateUser::one(2);
  if ($user === null)                                throw new Exception('Create 失敗');
  if ($user->id !== 2)                               throw new Exception('Create result 失敗');
  if ($user->a0 !== '')                              throw new Exception('Create result 失敗');
  if ($user->a1 !== '')                              throw new Exception('Create result 失敗');
  if ($user->a2 !== '')                              throw new Exception('Create result 失敗');
  if ($user->b0 !== '')                              throw new Exception('Create result 失敗');
  if ($user->b1 !== '')                              throw new Exception('Create result 失敗');
  if ($user->c0 !== 'a')                             throw new Exception('Create result 失敗');
  if ($user->c1 !== 'a')                             throw new Exception('Create result 失敗');
  if ($user->c2 !== 'a')                             throw new Exception('Create result 失敗');
  if ($user->d0->format() !== "2020-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->d1->format() !== "2020-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->d2->format() !== "2020-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->e0->format() !== "2020-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->e1->format() !== "2020-10-22 12:12:12") throw new Exception('Create result 失敗');
  if ($user->f0->format() !== '2020-10-22')          throw new Exception('Create result 失敗');
  if ($user->f1->format() !== '2020-10-22')          throw new Exception('Create result 失敗');
  if ($user->f2->format() !== '2020-10-22')          throw new Exception('Create result 失敗');
  if ($user->g0->format() !== '00:00:00')            throw new Exception('Create result 失敗');
  if ($user->g1->format() !== '00:00:00')            throw new Exception('Create result 失敗');
  if ($user->g2->format() !== '00:00:00')            throw new Exception('Create result 失敗');
  if ($user->h0 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->h1 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->h2 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->i0 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->i1 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->i2 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->j0 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->j1 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->j2 !== 1.1)                             throw new Exception('Create result 失敗');
  if ($user->k0 !== [])                              throw new Exception('Create result 失敗');
  if ($user->k1 !== [])                              throw new Exception('Create result 失敗');

  \M\CreateUser::closeDB();
}

create();
