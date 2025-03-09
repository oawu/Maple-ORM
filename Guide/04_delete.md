# delete

## 情境
### 資料庫
![](imgs/04-01.png)

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

## 單筆刪除
Model 物件可使用 `delete` method 將該筆資訊從資料庫刪除，刪除成功則回傳 **true**，失敗則回傳 **false**。

```php
$user = \App\Model\User::one(2);
$result = $user->delete(); // $user->remove();

echo $result ? '刪除成功' : '刪除失敗';

$user = \App\Model\User::one(2); // 重新抓一次資料，應該會為 null
echo $user ? '未刪除' : '已經成功刪除'; // 已經成功刪除
```

## 多筆刪除
可採用 `deletes` 的方式來完成多筆刪除，成功即回傳 **筆數**，失敗則 **null**。

```php
// deleteAll 的方式
$count = \App\Model\User::where('id', '>', 1)->deletes();
echo $count ? '成功刪除' . $count . '筆資料' : '刪除失敗';

$total = \App\Model\User::count(); // 取得所有數量
echo $total; // 1
```

## 刪除之後
此功能只給 **單筆刪除** 使用！

如果每次刪除一筆 User 資料時，就要將其他資料也一並刪除，那就可以在 `afterDeletes` 內指定一個刪除完後需要做的 method，如果 `afterDeletes` 中若有一個回傳不是為 true，那此次刪除就會是失敗的，該 delete 即回傳 **false**。

通常這類功能可以用在 **計數** 功能的欄位上。

`afterDeletes` 不保證成功全跑完，失敗結束不影響新增。中間有一次斷掉後，後面的則不會做完。

```php
// 定義 Model
class User extends Model {
  static $afterDeletes = ['clean'];

  public function clean() {
    User::deleteAll();
    return true;
  }
}

// 新增一筆
$user = \App\Model\User::one(2);
$result = $user->delete();

if ($result) { // 刪除成功
  echo \App\Model\User::count(); // 0
}
```
