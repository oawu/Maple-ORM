# 刪除資料

## 情境設定

以下範例使用 `User` Model，對應資料表：

| 欄位 | 型別 | 說明 |
|------|------|------|
| id | int | 主鍵，自動遞增 |
| name | varchar(255) | 名稱 |
| age | int | 年齡 |

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

## delete()

刪除單筆資料。成功回傳 `$this`（Model 自身），失敗回傳 `null`。

```php
$user = User::one(2);
$result = $user->delete();

if ($result) {
  echo '刪除成功';
  // $result === $user（同一物件）
}

// 確認已刪除
$user = User::one(2);
// $user === null
```

**方法簽名**：`delete(?int &$count = 0): ?self`

### 取得影響筆數

透過 by-reference 參數 `$count` 取得影響的列數：

```php
$user = User::one(1);

$count = 0;
$user->delete($count);
echo $count; // 1
```

---

## deletes()

批次刪除多筆資料。成功回傳刪除筆數，失敗回傳 `null`。

```php
$count = User::where('id', '>', 1)->deletes();
echo $count; // 2（OB、OC 被刪除）

$total = User::count();
echo $total; // 1（只剩 OA）
```

**方法簽名**：`static deletes(): ?int`

`deletes()` 可搭配所有條件方法：

```php
$count = User::whereIn('id', [1, 3])->deletes();
echo $count; // 2
```

---

## truncate()

清空整張資料表（`TRUNCATE TABLE`）。成功回傳 `true`，失敗回傳 `false`。

```php
$result = User::truncate();
// SQL: TRUNCATE TABLE `User`
```

**方法簽名**：`static truncate(?string $db = null): bool`

| 參數 | 說明 |
|------|------|
| `$db` | 指定資料庫 key（`null` 使用預設） |

> `TRUNCATE` 會重設 AUTO_INCREMENT 計數器，與 `DELETE` 不同。

---

## afterDeletes

Model 可定義 `$afterDeletes` 靜態屬性，在 `delete()` 成功後自動執行一系列方法。

```php
class User extends \Orm\Model {
  static $afterDeletes = ['cleanProfile', 'cleanArticles'];

  public function cleanProfile() {
    return Profile::where('userId', $this->id)->deletes();
  }

  public function cleanArticles($prevResult) {
    // $prevResult 是上一步 cleanProfile() 的回傳值
    return Article::where('userId', $this->id)->deletes();
  }
}
```

**執行規則**：

- 按陣列順序依次執行
- 每個方法接收**前一步的回傳值**作為參數（第一步無參數）
- 任一步回傳 falsy 值會**中斷**後續步驟
- **不影響已刪除的資料**：即使 callback 失敗，`delete()` 仍回傳成功
- 失敗訊息會透過 Log Func 記錄

> `afterDeletes` 僅適用於 `delete()`，不適用於 `deletes()`。
