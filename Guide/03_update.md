# update

## 情境
### 資料庫
![](imgs/03-01.png)

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

## 單筆更新
Model 物件的欄位若有更新，即可使用 `save` method 將變更的資訊更新至資料庫，更新成功則回傳**該原本物件**，失敗則回傳 **null**。

```php
$user = \App\Model\User::one(2);
echo $user->name; // OB

$user->name = 'OD';
$result = $user->save();

echo $result ? '更新成功' : '更新失敗';
```

## 多筆更新
有兩種方式，可採用 `updates` 的方式來完成多筆更新，成功即回傳 **筆數**，失敗則 **null**。

* 第一參數為將更新的欄位與值。

```php
$count = \App\Model\User::where('id', '>', 1)->updates([
  'name' => 'OD'
]);

echo $count !== null ? '成功' . $count . '筆資料' : '更新失敗';

// where 優先的方式
$result = \App\Model\User::where('id > ?', 1)->updates([
  'name' => 'OD'
]);
echo $result ? '更新成功' : '更新失敗';
```

