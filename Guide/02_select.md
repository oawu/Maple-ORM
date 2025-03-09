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
namespace App\Model;

class User extends \Orm\Model {}
```

## 查詢方式
查詢方式主要有四種，分別是 `one`、`first`、`last`、`all`。

* `one`、`first` 與 `last` 皆是取得 **單筆結果** 方式，而 `all` 則是取得 **多筆結果**。
* `one`、`first` 是取得結果的 **第一筆**
* `last` 是取得結果的 **最後一筆**
* `all` 是取得所有結果，以物件的 **陣列** 方式呈現。

```php
$user = \App\Model\User::one();
echo $user->name; // OA

$user = \App\Model\User::first();
echo $user->name; // OA

$user = \App\Model\User::last();
echo $user->name; // OC

$users = \App\Model\User::all();
foreach ($users as $user) {
  echo $user->name;
  // 會依序印出 OA、OB、OC
}
```

## 使用條件

規則基本上如下：

```
 Model::
   [where, whereIn, whereNotIn, whereBetween, select, order, group, having, limit, offset, byKey, relation]
   [where, whereIn, whereNotIn, whereBetween, select, order, group, having, limit, offset, byKey, relation, orWhere, orWhereIn, orWhereNotIn, orWhereBetween]*
   [one, first, last, all, count, update, delete]()

 Model::
   [create, creates, truncate, one, first, last, all, count, update, delete]()
```

```php
$user = \App\Model\User::where('age', '<', 20)->and('id', '>', 1)->one();
echo $user->name; // OC
```

如果只是為了**查詢某一筆 id** 的話，也可以再簡寫如下：

```php
$user = \App\Model\User::one(2);
echo $user->name; // OB
```

## Where IN

```php
$users = \App\Model\User::all([1, 3]); // id IN [1, 3]
foreach ($users as $user) {
  echo $user->name;
  // 會依序印出 OA、OC
}

$users = \App\Model\User::whereIn('id', [1, 3])->all();
foreach ($users as $user) {
  echo $user->name;
  // 會依序印出 OA、OC
}
```

## 進階條件
加入 `limit`、`offset`

```php
$user = \App\Model\User::limit(11)->offset(1)->where('id', '>', 0)->one();
echo $user->name; // OB
```

以上介紹的條件，不只在 `one` 可以用，如開始提到的 `one`、`first`、`last`、`all` 皆可使用。

```php
$users = \App\Model\User::where('id', '>', 1)->all();
foreach ($users as $user) {
  echo $user->name;
  // 會依序印出 OB、OC
}
```


## 進階方法

除 `one`、`first`、`last`、`all` 方法外，也有個方法 `count` 可以提供快速的查詢筆數，其範例如下：

```php
$total = \App\Model\User::count();
echo $total; // 3
```

count 亦可配合上述的條件情境使用。

```php
$total = \App\Model\User::where('id', '>', 1)->count();
echo $total; // 2
```


## 型態
由 `one`、`first`、`last` 所取得的資料皆會包裝成該 Model 的物件，配合其欄位建立對應的物件變數（未使用 select 情況下）。

如果取不到任何資料時，則會回 `null`，如下範例：

```php
$user = \App\Model\User::one(4); // id = 4
echo $user ? $user->name : 'NULL'; // NULL

$user = \App\Model\User::one(3); // id = 3
echo $user ? $user->name : 'NULL'; // OC
```

由 `all` 所取得的資料就會包裝成該 Model 物件的陣列型態，如果取不到任何資料時，則會回傳 **空陣列**。

```php
$users = \App\Model\User::all();
echo count($users); // 3
echo $users[1]->name; // OB

$users = \App\Model\User::all('id', [4, 5, 6]);
echo count($users); // 0
```

## byKey
由 `all` 的結果可以取得查詢後的 **Model 陣列**，但有時候我們希望藉由某個欄位做分類，如下面例子需要依據取得後的物件 `id` 做排序，正常寫法為：

```php
$users = \App\Model\User::all();
$byIdUsers = [];
foreach ($users as $user) {
  $byIdUsers[$user->id] ?? $byIdUsers[$user->id] = [];
  array_push($byIdUsers[$user->id], $user);
}
var_dump($byIdUsers);
```

可以使用 `byKey` 函式也可以達到相同的效果。

```php
$users = \App\Model\User::byKey('id')->all();
var_dump($users);
```