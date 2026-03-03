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
  // === truncate ===
  title('truncate');
  check(\Model\Create\User::truncate() === true);


  // === 單筆建立（完整欄位） ===
  title('單筆建立（完整欄位）');
  check(\Model\Create\User::create(['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []]) !== null);


  // === 單筆建立（最小欄位） ===
  title('單筆建立（最小欄位）');
  check(\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]) !== null);


  // === NOT NULL 驗證 ===
  title('NOT NULL 驗證');
  checkError(\Model\Create\User::create(['b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]), '實體 Model 時發生錯誤，錯誤原因：欄位「a1」不可以為 NULL');

  checkError(\Model\Create\User::create(['a1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]), '實體 Model 時發生錯誤，錯誤原因：欄位「b1」不可以為 NULL');

  checkError(\Model\Create\User::create(['a1' => '', 'b1' => '', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]), '實體 Model 時發生錯誤，錯誤原因：欄位「c1」不可以為 NULL');

  checkError(\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]), '實體 Model 時發生錯誤，錯誤原因：欄位「d1」不可以為 NULL');

  checkError(\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]), '實體 Model 時發生錯誤，錯誤原因：欄位「f1」不可以為 NULL');

  checkError(\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]), '實體 Model 時發生錯誤，錯誤原因：欄位「g1」不可以為 NULL');

  checkError(\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]), '實體 Model 時發生錯誤，錯誤原因：欄位「h1」不可以為 NULL');

  checkError(\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'j1' => 1.1, 'k1' => []]), '實體 Model 時發生錯誤，錯誤原因：欄位「i1」不可以為 NULL');

  checkError(\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'k1' => []]), '實體 Model 時發生錯誤，錯誤原因：欄位「j1」不可以為 NULL');

  checkError(\Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => []]), '實體 Model 時發生錯誤，錯誤原因：欄位「k1」不可以為 NULL');


  // === 格式驗證 ===
  title('格式驗證');
  $params = ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []];

  checkError(\Model\Create\User::create(array_merge($params, ['c0' => 'c'])), '實體 Model 時發生錯誤，錯誤原因：欄位「c0」格式為「enum」，選項有「a、b」，欄位值「c」不在選項內');
  checkError(\Model\Create\User::create(array_merge($params, ['d0' => 'c'])), '實體 Model 時發生錯誤，錯誤原因：欄位「d0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式');
  checkError(\Model\Create\User::create(array_merge($params, ['e0' => 'c'])), '實體 Model 時發生錯誤，錯誤原因：欄位「e0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式');
  checkError(\Model\Create\User::create(array_merge($params, ['f0' => 'c'])), '實體 Model 時發生錯誤，錯誤原因：欄位「f0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式');
  checkError(\Model\Create\User::create(array_merge($params, ['g0' => 'c'])), '實體 Model 時發生錯誤，錯誤原因：欄位「g0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式');


  // === JSON 欄位 ===
  title('JSON 欄位');
  check(\Model\Create\User::create(array_merge($params, ['k0' => ''])) !== null);
  check(\Model\Create\User::create(array_merge($params, ['k0' => 'abc'])) !== null);
  check(\Model\Create\User::create(array_merge($params, ['k0' => '123'])) !== null);
  check(\Model\Create\User::create(array_merge($params, ['k0' => []])) !== null);
  check(\Model\Create\User::create(array_merge($params, ['k0' => ['']])) !== null);
  check(\Model\Create\User::create(array_merge($params, ['k0' => [0]])) !== null);
  check(\Model\Create\User::create(array_merge($params, ['k0' => ['abc']])) !== null);
  check(\Model\Create\User::create(array_merge($params, ['k0' => ['123']])) !== null);
  check(\Model\Create\User::create(array_merge($params, ['k0' => [123]])) !== null);
  check(\Model\Create\User::create(array_merge($params, ['k0' => null])) !== null);

  check(\Model\Create\User::truncate() === true);


  // === 批次建立 ===
  title('批次建立');
  check(\Model\Create\User::creates([
    ['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []],
    ['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []],
  ]) == 2);

  check(\Model\Create\User::creates([
    ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []],
    ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []],
  ]) == 2);

  check(\Model\Create\User::creates([
    ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]
  ]) === 1);


  // === 批次 NOT NULL 驗證 ===
  title('批次 NOT NULL 驗證');
  checkError(\Model\Create\User::creates([$params, ['b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]]), '新增資料錯誤，錯誤原因：欄位「a1」不可以為 NULL');
  checkError(\Model\Create\User::creates([['a1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]), '新增資料錯誤，錯誤原因：欄位「b1」不可以為 NULL');
  checkError(\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]), '新增資料錯誤，錯誤原因：欄位「c1」不可以為 NULL');
  checkError(\Model\Create\User::creates([['a1' => '', 'b1' => '', 'c1' => 'a', 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]]), '新增資料錯誤，錯誤原因：欄位「d1」不可以為 NULL');
  checkError(\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]), '新增資料錯誤，錯誤原因：欄位「f1」不可以為 NULL');
  checkError(\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]), '新增資料錯誤，錯誤原因：欄位「g1」不可以為 NULL');
  checkError(\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'i1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]), '新增資料錯誤，錯誤原因：欄位「h1」不可以為 NULL');
  checkError(\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'j1' => 1.1, 'k1' => []], $params]), '新增資料錯誤，錯誤原因：欄位「i1」不可以為 NULL');
  checkError(\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'k1' => []], $params]), '新增資料錯誤，錯誤原因：欄位「j1」不可以為 NULL');
  checkError(\Model\Create\User::creates([$params, ['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => []], $params]), '新增資料錯誤，錯誤原因：欄位「k1」不可以為 NULL');


  // === 批次格式驗證 ===
  title('批次格式驗證');
  checkError(\Model\Create\User::creates([$params, array_merge($params, ['c0' => 'c'])]), '新增資料錯誤，錯誤原因：欄位「c0」格式為「enum」，選項有「a、b」，欄位值「c」不在選項內');
  checkError(\Model\Create\User::creates([array_merge($params, ['d0' => 'c']), $params]), '新增資料錯誤，錯誤原因：欄位「d0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式');
  checkError(\Model\Create\User::creates([$params, array_merge($params, ['e0' => 'c']), $params]), '新增資料錯誤，錯誤原因：欄位「e0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式');
  checkError(\Model\Create\User::creates([$params, array_merge($params, ['f0' => 'c']), $params]), '新增資料錯誤，錯誤原因：欄位「f0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式');
  checkError(\Model\Create\User::creates([$params, array_merge($params, ['g0' => 'c']), $params]), '新增資料錯誤，錯誤原因：欄位「g0」值「c」無法轉為 Orm\Core\Plugin\DateTime 格式');


  // === 批次 JSON 欄位 ===
  title('批次 JSON 欄位');
  check(\Model\Create\User::creates([$params, array_merge($params, ['k0' => '']), $params]) !== null);
  check(\Model\Create\User::creates([$params, array_merge($params, ['k0' => 'abc']), $params]) !== null);
  check(\Model\Create\User::creates([$params, array_merge($params, ['k0' => '123']), $params]) !== null);
  check(\Model\Create\User::creates([$params, array_merge($params, ['k0' => 123]), $params]) !== null);
  check(\Model\Create\User::creates([$params, array_merge($params, ['k0' => []]), $params]) !== null);
  check(\Model\Create\User::creates([$params, array_merge($params, ['k0' => ['']]), $params]) !== null);
  check(\Model\Create\User::creates([$params, array_merge($params, ['k0' => [0]]), $params]) !== null);
  check(\Model\Create\User::creates([$params, array_merge($params, ['k0' => ['abc']]), $params]) !== null);
  check(\Model\Create\User::creates([$params, array_merge($params, ['k0' => ['123']]), $params]) !== null);
  check(\Model\Create\User::creates([$params, array_merge($params, ['k0' => [123]]), $params]) !== null);
  check(\Model\Create\User::creates([$params, array_merge($params, ['k0' => null]), $params]) !== null);


  // === 驗證欄位值（完整） ===
  title('驗證欄位值（完整）');
  check(\Model\Create\User::truncate() === true);

  $user = \Model\Create\User::create(['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []]);
  check($user !== null);
  check($user->id === 1);
  check($user->a0 === '');
  check($user->a1 === '');
  check($user->a2 === '');
  check($user->b0 === '');
  check($user->b1 === '');
  check($user->c0 === 'a');
  check($user->c1 === 'a');
  check($user->c2 === 'a');
  check($user->d0->format() === "2020-10-22 12:12:12");
  check($user->d1->format() === "2020-10-22 12:12:12");
  check($user->d2->format() === "2020-10-22 12:12:12");
  check($user->e0->format() === "2020-10-22 12:12:12");
  check($user->e1->format() === "2020-10-22 12:12:12");
  check($user->f0->format() === '2020-10-22');
  check($user->f1->format() === '2020-10-22');
  check($user->f2->format() === '2020-10-22');
  check($user->g0->format() === null && $user->g0->getValue() === '00:00:00');
  check($user->g1->format() === null && $user->g1->getValue() === '00:00:00');
  check($user->g2->format() === null && $user->g2->getValue() === '00:00:00');
  check($user->h0 === 1.1);
  check($user->h1 === 1.1);
  check($user->h2 === 1.1);
  check($user->i0 === 1.1);
  check($user->i1 === 1.1);
  check($user->i2 === 1.1);
  check($user->j0 === 1.1);
  check($user->j1 === 1.1);
  check($user->j2 === 1.1);
  check($user->k0 === []);
  check($user->k1 === []);


  // === 驗證欄位值（預設） ===
  title('驗證欄位值（預設）');
  check(\Model\Create\User::truncate() === true);

  $user = \Model\Create\User::create(['a1' => '', 'b1' => '', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'f1' => '2020-10-22', 'g1' => '00:00:00', 'h1' => 1.1, 'i1' => 1.1, 'j1' => 1.1, 'k1' => []]);
  check($user !== null);
  check($user->id === 1);
  check($user->a0 === null);
  check($user->a1 === '');
  check($user->a2 === '');
  check($user->b0 === null);
  check($user->b1 === '');
  check($user->c0 === null);
  check($user->c1 === 'a');
  check($user->c2 === 'a');
  check($user->d0->getValue() === null);
  check($user->d1->format() === "2020-10-22 12:12:12");
  check($user->d2->getValue() !== '' && $user->d2->getValue() !== null);
  check($user->e0->getValue() === null);
  check($user->e1->getValue() !== '' && $user->e1->getValue() !== null);
  check($user->f0->getValue() === null);
  check($user->f1->format() === '2020-10-22');
  check($user->f2->getValue() !== '' && $user->f2->getValue() !== null);
  check($user->g0->getValue() === null);
  check($user->g1->getValue() === '00:00:00' && $user->g1->format() === null);
  check($user->g2->getValue() !== '' && $user->g2->getValue() !== null);
  check($user->h0 === null);
  check($user->h1 === 1.1);
  check($user->h2 === 0.0);
  check($user->i0 === null);
  check($user->i1 === 1.1);
  check($user->i2 === 0.0);
  check($user->j0 === null);
  check($user->j1 === 1.1);
  check($user->j2 === 0.0);
  check($user->k0 === null);
  check($user->k1 === []);


  // === 驗證 creates 結果 ===
  title('驗證 creates 結果');
  check(\Model\Create\User::truncate() === true);

  check(\Model\Create\User::creates([
    ['a0' => '1', 'a1' => '1', 'a2' => '1', 'b0' => '1', 'b1' => '1', 'c0' => 'b', 'c1' => 'b', 'c2' => 'b', 'd0' => new DateTime("2021-10-22 12:12:12"), 'd1' => new DateTime("2021-10-22 12:12:12"), 'd2' => new DateTime("2021-10-22 12:12:12"), 'e0' => new DateTime("2021-10-22 12:12:12"), 'e1' => new DateTime("2021-10-22 12:12:12"), 'f0' => '2021-10-22', 'f1' => '2021-10-22', 'f2' => '2021-10-22', 'g0' => '01:00:00', 'g1' => '01:00:00', 'g2' => '01:00:00', 'h0' => 2.1, 'h1' => 2.1, 'h2' => 2.1, 'i0' => 2.1, 'i1' => 2.1, 'i2' => 2.1, 'j0' => 2.1, 'j1' => 2.1, 'j2' => 2.1, 'k0' => [1], 'k1' => [2]],
    ['a0' => '', 'a1' => '', 'a2' => '', 'b0' => '', 'b1' => '', 'c0' => 'a', 'c1' => 'a', 'c2' => 'a', 'd0' => new DateTime("2020-10-22 12:12:12"), 'd1' => new DateTime("2020-10-22 12:12:12"), 'd2' => new DateTime("2020-10-22 12:12:12"), 'e0' => new DateTime("2020-10-22 12:12:12"), 'e1' => new DateTime("2020-10-22 12:12:12"), 'f0' => '2020-10-22', 'f1' => '2020-10-22', 'f2' => '2020-10-22', 'g0' => '00:00:00', 'g1' => '00:00:00', 'g2' => '00:00:00', 'h0' => 1.1, 'h1' => 1.1, 'h2' => 1.1, 'i0' => 1.1, 'i1' => 1.1, 'i2' => 1.1, 'j0' => 1.1, 'j1' => 1.1, 'j2' => 1.1, 'k0' => [], 'k1' => []],
    ['a0' => '2', 'a1' => '2', 'a2' => '2', 'b0' => '1', 'b1' => '1', 'c0' => 'b', 'c1' => 'b', 'c2' => 'b', 'd0' => new DateTime("2021-10-22 12:12:12"), 'd1' => new DateTime("2021-10-22 12:12:12"), 'd2' => new DateTime("2021-10-22 12:12:12"), 'e0' => new DateTime("2021-10-22 12:12:12"), 'e1' => new DateTime("2021-10-22 12:12:12"), 'f0' => '2021-10-22', 'f1' => '2021-10-22', 'f2' => '2021-10-22', 'g0' => '01:00:00', 'g1' => '01:00:00', 'g2' => '01:00:00', 'h0' => 2.1, 'h1' => 2.1, 'h2' => 2.1, 'i0' => 2.1, 'i1' => 2.1, 'i2' => 2.1, 'j0' => 2.1, 'j1' => 2.1, 'j2' => 2.1, 'k0' => [1], 'k1' => [2]],
  ]) == 3);

  $user = \Model\Create\User::one(1);
  check($user !== null);
  check($user->id === 1);
  check($user->a0 === '1');
  check($user->a1 === '1');
  check($user->a2 === '1');
  check($user->b0 === '1');
  check($user->b1 === '1');
  check($user->c0 === 'b');
  check($user->c1 === 'b');
  check($user->c2 === 'b');
  check($user->d0->format() === "2021-10-22 12:12:12");
  check($user->d1->format() === "2021-10-22 12:12:12");
  check($user->d2->format() === "2021-10-22 12:12:12");
  check($user->e0->format() === "2021-10-22 12:12:12");
  check($user->e1->format() === "2021-10-22 12:12:12");
  check($user->f0->format() === '2021-10-22');
  check($user->f1->format() === '2021-10-22');
  check($user->f2->format() === '2021-10-22');
  check($user->g0->format() === '01:00:00');
  check($user->g1->format() === '01:00:00');
  check($user->g2->format() === '01:00:00');
  check($user->h0 === 2.1);
  check($user->h1 === 2.1);
  check($user->h2 === 2.1);
  check($user->i0 === 2.1);
  check($user->i1 === 2.1);
  check($user->i2 === 2.1);
  check($user->j0 === 2.1);
  check($user->j1 === 2.1);
  check($user->j2 === 2.1);
  check($user->k0 === [1]);
  check($user->k1 === [2]);

  $user = \Model\Create\User::one(2);
  check($user !== null);
  check($user->id === 2);
  check($user->a0 === '');
  check($user->a1 === '');
  check($user->a2 === '');
  check($user->b0 === '');
  check($user->b1 === '');
  check($user->c0 === 'a');
  check($user->c1 === 'a');
  check($user->c2 === 'a');
  check($user->d0->format() === "2020-10-22 12:12:12");
  check($user->d1->format() === "2020-10-22 12:12:12");
  check($user->d2->format() === "2020-10-22 12:12:12");
  check($user->e0->format() === "2020-10-22 12:12:12");
  check($user->e1->format() === "2020-10-22 12:12:12");
  check($user->f0->format() === '2020-10-22');
  check($user->f1->format() === '2020-10-22');
  check($user->f2->format() === '2020-10-22');
  check($user->g0->format() === null && $user->g0->getValue() === '00:00:00');
  check($user->g1->format() === null && $user->g1->getValue() === '00:00:00');
  check($user->g2->format() === null && $user->g2->getValue() === '00:00:00');
  check($user->h0 === 1.1);
  check($user->h1 === 1.1);
  check($user->h2 === 1.1);
  check($user->i0 === 1.1);
  check($user->i1 === 1.1);
  check($user->i2 === 1.1);
  check($user->j0 === 1.1);
  check($user->j1 === 1.1);
  check($user->j2 === 1.1);
  check($user->k0 === []);
  check($user->k1 === []);

  Model::close();
}

create();
