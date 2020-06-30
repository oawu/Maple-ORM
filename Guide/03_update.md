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
namespace M;

class User extends Model {}
```

## 單筆更新
Model 物件的欄位若有更新，即可使用 `save` method 將變更的資訊更新至資料庫，更新成功則回傳**該原本物件**，失敗則回傳 **false**。

```php
$user = \M\User::one(2);
echo $user->name; // OB

$user->name = 'OD';
$result = $user->save();

echo $result ? '更新成功' : '更新失敗';
```

## 多筆更新
有兩種方式，可採用 `updateAll` 或者 `where 優先` 的方式來完成多筆更新，成功即回傳 **true**，失敗則 **false**，通常會以 `where 優先` 的方式為主要寫法。

* updateAll 方式，第一參數為將更新的欄位與值，第二參數後即為條件。
* where 優先，則是先下好 where 條件後，在 update 要更改的欄位與值。

```php
// updateAll 的方式
$result = \M\User::updateAll([
  'name' => 'OD'
], 'id > ?', 1);

echo $result ? '更新成功' : '更新失敗';

// where 優先的方式
$result = \M\User::where('id > ?', 1)->update([
  'name' => 'OD'
]);
echo $result ? '更新成功' : '更新失敗';
```

