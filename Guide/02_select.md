# select

## 情境
### 資料庫
![](imgs/02-01.png)

<!--
#### 格式
| 欄位 | 格式  |
|---|---|
| id | INT | 
| name | VARCHAR |
| age | INT |

#### 資料
| id | name | age |
|---|---|---|
| 1 | OA | 18 |
| 2 | OB | 28 |
| 3 | OC | 15 |
-->

### Model

```php
namespace M;

class User extends Model {}
```

## 查詢方式
查詢方式主要有四種，分別是 `one`、`first`、`last`、`all`。

* `one`、`first` 與 `last` 皆是取得 **單筆結果** 方式，而 `all` 則是取得 **多筆結果**。
* `one`、`first` 是取得結果的 **第一筆**
* `last` 是取得結果的 **最後一筆**
* `all` 是取得所有結果，以物件的 **陣列** 方式呈現。

```php
$user = \M\User::one();
echo $user->name; // OA

$user = \M\User::first();
echo $user->name; // OA

$user = \M\User::last();
echo $user->name; // OC

$users = \M\User::all();
foreach ($users as $user) {
  echo $user->name;
  // 會依序印出 OA、OB、OC
}
```

## 使用條件
主要分兩種方式：

1. **方法優先**
2. **條件優先**

```php
// 方法優先
$user = \M\User::one(['where' => ['age < ? AND id > ?', 20, 1]]);
echo $user->name; // OC

// 條件優先
$user = \M\User::where('age < ?', 20)->and('id > ?', 1)->one();
echo $user->name; // OC
```

## 簡寫
在沒有其他條前如 `select`、`limit`、`offset`、`order` 時，會以「**方法優先**」為主要寫法，並且可採用**簡寫**的方式。

```php
$user = \M\User::one('age < ? AND id > ?', 20, 1);
echo $user->name; // OC
```

如果只是為了**查詢某一筆 id** 的話，也可以再簡寫如下：

```php
$user = \M\User::one(2);
echo $user->name; // OB
```

> 這邊可能會有疑問，為何要用 `["id > ?", 1]` 的方式，而並不是直接 `"id > 1"`，主要是以 PHP PDO 的 `prepare` 與 `execute` 角度設計，參考[此篇](https://www.php.net/manual/en/pdo.prepare.php)

> 採用此方式可以增加安全性，防止可能的 sql injection，當然如果有些確定值要直接 "id > 1" 也是可以的，如果是字串的查詢，仍建議拆開，如下範例：

```php
$user = \M\User::one(['where' => 'age < 20 AND id > 1']);
echo $user->name; // OC

$user = \M\User::one('age < 20 AND id > 1');
echo $user->name; // OC

$user = \M\User::where('age < 20 AND id > 1')->one();
echo $user->name; // OC
```


## Where IN
寫法必須採用 `(?)` 並給予對應的陣列參數。

```php
// 方法優先的寫法
$users = \M\User::all('id IN (?)', [1, 3]);
foreach ($users as $user) {
  echo $user->name;
  // 會依序印出 OA、OC
}

// 條件優先的寫法
$users = \M\User::where('id IN (?)', [1, 3])->all();
foreach ($users as $user) {
  echo $user->name;
  // 會依序印出 OA、OC
}
```

## 進階條件
需要加入 `limit`、`offset` 時就不可簡寫。

```php
// 方法優先的寫法
$user = \M\User::one([
  'limit' => 10,
  'offset' => 1,
  'where' => ['id > ?', 0]]);
echo $user->name; // OB

// 條件優先的寫法
$user = \M\User::where('id > ?', 0)->one([
  'limit' => 11,
  'offset' => 1]);
echo $user->name; // OB
```

以上介紹的條件，不只在 `one` 可以用，如開始提到的 `one`、`first`、`last`、`all` 皆可使用。

```php
// 方法優先的寫法
$users = \M\User::all('id > ?', 1);
foreach ($users as $user) {
  echo $user->name;
  // 會依序印出 OB、OC
}

// 條件優先的寫法
$users = \M\User::where('id > ?', 1)->all();
foreach ($users as $user) {
  echo $user->name;
  // 會依序印出 OB、OC
}
```


## 進階方法

除 `one`、`first`、`last`、`all` 方法外，也有個方法 `count` 可以提供快速的查詢筆數，其範例如下：

```php
$total = \M\User::count();
echo $total; // 3
```

count 亦可配合上述的條件情境使用。

```php
// 方法優先的寫法
$total = \M\User::count('id > 1');
echo $total; // 2

// 條件優先的寫法
$total = \M\User::where('id > ?', 1)->count();
echo $total; // 2
```


## 型態
由 `one`、`first`、`last` 所取得的資料皆會包裝成該 Model 的物件，配合其欄位建立對應的物件變數（未使用 select 情況下）。

如果取不到任何資料時，則會回 `null`，如下範例：

```php
$user = \M\User::one(4); // id = 4
echo $user ? $user->name : 'NULL'; // NULL

$user = \M\User::one(3); // id = 3
echo $user ? $user->name : 'NULL'; // OC
```

由 `all` 所取得的資料就會包裝成該 Model 物件的陣列型態，如果取不到任何資料時，則會回傳 **空陣列**。

```php
$users = \M\User::all();
echo count($users); // 3
echo $users[1]->name; // OB

$users = \M\User::all('id IN (?)', [4, 5, 6]);
echo count($users); // 0
```
