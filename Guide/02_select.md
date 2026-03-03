# 查詢資料

## 情境設定

以下範例使用 `User` Model，對應資料表：

| 欄位 | 型別 | 說明 |
|------|------|------|
| id | int | 主鍵，自動遞增 |
| name | varchar(255) | 名稱 |
| age | int | 年齡 |
| createAt | datetime | 建立時間 |
| updateAt | datetime | 更新時間 |

範例資料：

| id | name | age |
|----|------|-----|
| 1 | OA | 18 |
| 2 | OB | 28 |
| 3 | OC | 15 |

```php
namespace Model;

class User extends \Orm\Model {}
```

---

## 查詢方法

查詢方法有四種：`one` / `first`（同義）、`last`、`all`。

| 方法 | 回傳 | 說明 |
|------|------|------|
| `one()` | `?Model` | 取得第一筆，無資料回傳 `null` |
| `first()` | `?Model` | 同 `one()` |
| `last()` | `?Model` | 取得最後一筆（反轉排序），無資料回傳 `null` |
| `all()` | `array` | 取得所有結果，無資料回傳空陣列 `[]` |

```php
$user = User::one();
echo $user->name; // OA

$user = User::first();
echo $user->name; // OA

$user = User::last();
echo $user->name; // OC

$users = User::all();
foreach ($users as $user) {
  echo $user->name; // OA、OB、OC
}
```

### one() 簡寫

`one()` 可直接傳入參數，作為快速查詢：

```php
// 傳入 1 個值 → 以主鍵查詢
$user = User::one(2);
// 等同：User::where('id', 2)->limit(1)->one()
echo $user->name; // OB

// 傳入 2 個值 → where(key, value)
$user = User::one('name', 'OC');
// 等同：User::where('name', 'OC')->limit(1)->one()

// 傳入 3 個值 → where(key, operator, value)
$user = User::one('age', '>', 20);
// 等同：User::where('age', '>', 20)->limit(1)->one()

// 無結果回傳 null
$user = User::one(999);
// $user === null
```

`first()` 和 `last()` 同樣支援此簡寫。

---

## count()

計算符合條件的筆數。成功回傳整數，失敗回傳 `null`。

```php
$total = User::count();
echo $total; // 3

$total = User::where('age', '>', 20)->count();
echo $total; // 1
```

---

## where()

`where()` 是最常用的條件方法，支援多種參數形式：

### 1 個參數

```php
// 傳入整數或字串 → 以主鍵 id 查詢
User::where(1)->one();
// SQL: WHERE `id` = 1

// 傳入 null → IS NULL
User::where(null)->all();
// SQL: WHERE `id` IS NULL

// 傳入陣列 → IN
User::where([1, 3])->all();
// SQL: WHERE `id` IN (1, 3)

// 傳入關聯式陣列 → 多條件 AND
User::where(['name' => 'OA', 'age' => 18])->one();
// SQL: WHERE `name` = 'OA' AND `age` = 18

// 關聯式陣列的值也可以是陣列（IN）或 null（IS NULL）
User::where(['id' => [1, 2], 'name' => 'OA'])->all();
// SQL: WHERE `id` IN (1, 2) AND `name` = 'OA'
```

### 2 個參數

```php
// where(key, value)
User::where('name', 'OA')->one();
// SQL: WHERE `name` = 'OA'

// value 為 null → IS NULL
User::where('name', null)->all();
// SQL: WHERE `name` IS NULL

// value 為陣列 → IN
User::where('id', [1, 3])->all();
// SQL: WHERE `id` IN (1, 3)
```

### 3 個參數

```php
// where(key, operator, value)
User::where('age', '>', 20)->all();
// SQL: WHERE `age` > 20

User::where('age', '>=', 18)->all();
// SQL: WHERE `age` >= 18

User::where('name', '!=', 'OA')->all();
// SQL: WHERE `name` != 'OA'

User::where('name', 'LIKE', '%O%')->all();
// SQL: WHERE `name` LIKE '%O%'

User::where('name', 'IS NOT', null)->all();
// SQL: WHERE `name` IS NOT NULL
```

支援的運算子：`=`、`!=`、`<>`、`>`、`>=`、`<`、`<=`、`LIKE`、`NOT LIKE`、`IS NOT`、`IN`、`NOT IN`、`BETWEEN`。

### 鏈式呼叫

多個 `where()` 以 AND 連接：

```php
User::where('age', '>', 15)->where('age', '<', 30)->all();
// SQL: WHERE `age` > 15 AND `age` < 30
```

---

## whereIn / whereNotIn

```php
User::whereIn('id', [1, 3])->all();
// SQL: WHERE `id` IN (1, 3)

User::whereNotIn('id', [2])->all();
// SQL: WHERE `id` NOT IN (2)
```

簡寫別名：`in()` = `whereIn()`、`notIn()` = `whereNotIn()`。

> 空陣列時的行為：`whereIn('id', [])` → `WHERE 1=0`（永不成立）；`whereNotIn('id', [])` → `WHERE 1=1`（永遠成立）。

---

## whereBetween

```php
User::whereBetween('age', 15, 25)->all();
// SQL: WHERE `age` BETWEEN 15 AND 25
```

簡寫別名：`between()` = `whereBetween()`。

---

## orWhere 系列

`orWhere` 以 OR 連接條件：

```php
User::where('name', 'OA')->orWhere('name', 'OC')->all();
// SQL: WHERE `name` = 'OA' OR `name` = 'OC'
```

所有 OR 版本：

| 方法 | 簡寫 | 說明 |
|------|------|------|
| `orWhere(...$args)` | `or(...$args)` | OR 條件 |
| `orWhereIn(key, vals)` | `orIn(key, vals)` | OR IN |
| `orWhereNotIn(key, vals)` | `orNotIn(key, vals)` | OR NOT IN |
| `orWhereBetween(key, v1, v2)` | `orBetween(key, v1, v2)` | OR BETWEEN |

```php
User::where('age', '>', 25)->orWhereBetween('age', 10, 16)->all();
// SQL: WHERE `age` > 25 OR `age` BETWEEN 10 AND 16
// 結果：OB (28), OC (15)
```

---

## whereGroup / orWhereGroup

將條件包在括號中，產生群組條件：

```php
User::where('age', '>', 10)->whereGroup(function ($builder) {
  $builder->where('name', 'OA')->orWhere('name', 'OC');
})->all();
// SQL: WHERE `age` > 10 AND (`name` = 'OA' OR `name` = 'OC')
// 結果：OA (18), OC (15)
```

```php
User::where('age', '>', 25)->orWhereGroup(function ($builder) {
  $builder->where('name', 'OC')->where('age', '<', 20);
})->all();
// SQL: WHERE `age` > 25 OR (`name` = 'OC' AND `age` < 20)
// 結果：OB (28), OC (15)
```

**方法簽名**：

- `whereGroup(callable $callback): Builder` — callback 接收 `Builder` 參數，AND 群組
- `orWhereGroup(callable $callback): Builder` — callback 接收 `Builder` 參數，OR 群組

---

## select()

指定查詢欄位。預設查詢所有欄位（`table.*`）。

```php
User::select('id', 'name')->all();
// SQL: SELECT `id`, `name` FROM `User`

// 也可以用逗號分隔
User::select('id, name')->all();

// 重設為全部欄位
User::select('*')->all();
```

**方法簽名**：`select(string ...$args): Builder`

---

## order()

指定排序。

```php
User::order('age DESC')->all();
// SQL: ORDER BY age DESC

User::order('age DESC', 'id ASC')->all();
// SQL: ORDER BY age DESC, id ASC
```

**方法簽名**：`order(string ...$args): Builder`

---

## limit / offset

```php
User::limit(2)->all();
// SQL: LIMIT 2

User::limit(2)->offset(1)->all();
// SQL: LIMIT 1, 2（offset, limit）
```

---

## group / having

分組查詢。

```php
User::select('age, COUNT(*) as cnt')->group('age')->all();
// SQL: SELECT age, COUNT(*) as cnt FROM `User` GROUP BY age

User::select('age, COUNT(*) as cnt')->group('age')->having('cnt > 1')->all();
// SQL: ... GROUP BY age HAVING cnt > 1
```

**方法簽名**：

- `group(?string $group): Builder`
- `having(?string $having): Builder`

---

## lockForUpdate()

悲觀鎖定。在 `SELECT` 語句尾端加上 `FOR UPDATE`，需搭配 `transaction()` 使用：

```php
$user = transaction('', function () {
  return User::where('id', 1)->lockForUpdate()->one();
});
// SQL: SELECT * FROM `User` WHERE `id` = 1 FOR UPDATE
```

**方法簽名**：`lockForUpdate(): Builder`

---

## byKey()

將 `all()` 的結果按指定欄位分組為關聯式陣列。

### 單一 key

```php
$users = User::byKey('id')->all();
// [
//   1 => [User(id=1)],
//   2 => [User(id=2)],
//   3 => [User(id=3)],
// ]
```

### 多重 key

多個 key 以 `\x00`（null byte）連接：

```php
$users = User::byKey('age', 'name')->all();
// [
//   "18\x00OA" => [User(id=1)],
//   "28\x00OB" => [User(id=2)],
//   "15\x00OC" => [User(id=3)],
// ]
```

**方法簽名**：`byKey(string ...$keys): Builder`

---

## 指定資料庫

透過 `db()` 指定從哪個資料庫查詢：

```php
$user = User::db('R')->where('id', 1)->one();
```

**方法簽名**：`static db(?string $db = null): Builder`

---

## 鏈式查詢總覽

所有條件方法可自由組合：

```php
$users = User::where('age', '>', 10)
  ->whereIn('id', [1, 2, 3])
  ->select('id', 'name', 'age')
  ->order('age DESC')
  ->limit(10)
  ->offset(0)
  ->all();
```

可用方法摘要：

| 分類 | 方法 |
|------|------|
| 條件 | `where`、`whereIn`（`in`）、`whereNotIn`（`notIn`）、`whereBetween`（`between`） |
| OR 條件 | `orWhere`（`or`）、`orWhereIn`（`orIn`）、`orWhereNotIn`（`orNotIn`）、`orWhereBetween`（`orBetween`） |
| 群組條件 | `whereGroup`、`orWhereGroup` |
| 欄位 | `select` |
| 排序 | `order` |
| 分組 | `group`、`having` |
| 分頁 | `limit`、`offset` |
| 鎖定 | `lockForUpdate` |
| 分類 | `byKey` |
| 預載關聯 | `relation`（詳見 [07_relation.md](07_relation.md)） |
| 指定資料庫 | `db` |
