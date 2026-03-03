# 更新資料

## 情境設定

以下範例使用 `User` Model，對應資料表：

| 欄位 | 型別 | 說明 |
|------|------|------|
| id | int | 主鍵，自動遞增 |
| name | varchar(255) | 名稱 |
| age | int | 年齡 |
| createAt | datetime | 建立時間 |
| updateAt | datetime | 更新時間（自動更新） |

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

## save()

更新單筆資料。成功回傳 `$this`（Model 自身），失敗回傳 `null`。

```php
$user = User::one(2);
echo $user->name; // OB

$user->name = 'OD';
$result = $user->save();

if ($result) {
  echo $user->name; // OD
} else {
  // 更新失敗
}
```

**方法簽名**：`save(?int &$count = 0): ?self`

### Dirty Tracking

`save()` 只更新有變更的欄位。若沒有任何欄位被修改，不會執行 SQL，直接回傳 `$this`。

```php
$user = User::one(1);
$user->save(); // 沒有變更，不執行 SQL，回傳 $this
```

### 自動更新時間

當有欄位被修改時，`updateAt` 會自動更新為當前時間（除非已手動設定）。

### 取得影響筆數

透過 by-reference 參數 `$count` 取得影響的列數：

```php
$user = User::one(1);
$user->name = 'New Name';

$count = 0;
$user->save($count);
echo $count; // 1
```

---

## set()

批次設定屬性，可選擇是否自動 `save()`。

```php
$user = User::one(1);

// 只設定屬性，不存檔
$user->set(['name' => 'AA', 'age' => 20]);
// 此時還需要手動 $user->save()

// 設定屬性並自動存檔
$user->set(['name' => 'AA', 'age' => 20], [], true);
```

**方法簽名**：`set(array $attrs = [], array $allow = [], bool $save = false): ?self`

| 參數 | 說明 |
|------|------|
| `$attrs` | 欄位與值的關聯式陣列 |
| `$allow` | 白名單陣列，限制只能設定哪些欄位（空陣列 = 不限制） |
| `$save` | `true` 時自動呼叫 `save()`，回傳值同 `save()` |

### allow 白名單

```php
$data = ['name' => 'AA', 'age' => 20, 'role' => 'admin'];

// 只允許設定 name 和 age
$user->set($data, ['name', 'age']);
```

---

## updates()

批次更新多筆資料。成功回傳影響筆數，失敗回傳 `null`。

```php
$count = User::where('age', '>', 20)->updates([
  'name' => 'Senior'
]);
echo $count; // 1（OB 被更新）
```

**方法簽名**：`static updates(array $sets = []): ?int`

`updates()` 可搭配所有條件方法：

```php
$count = User::where('id', '>', 1)->whereIn('age', [15, 28])->updates([
  'name' => 'Updated'
]);
echo $count; // 2
```

> `updates()` 也會自動更新 `updateAt`，但不會更動 `createAt`。
