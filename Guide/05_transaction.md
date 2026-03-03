# 交易機制

## transaction()

資料庫交易（Transaction），確保多步寫入操作的原子性。

```php
$result = \Orm\Helper::transaction('', static function () {
  $user = User::create(['name' => 'OA', 'age' => 18]);

  if (!$user) {
    return null; // 回傳 falsy → rollback
  }

  return $user; // 回傳 truthy → commit
});
```

**方法簽名**：`\Orm\Helper::transaction(?string $db, callable $closure, ?array &$errors = null): mixed`

| 參數 | 說明 |
|------|------|
| `$db` | 資料庫 key（`''` 或 `null` 為預設資料庫） |
| `$closure` | 要執行的 closure |
| `$errors` | by-reference，取得錯誤訊息陣列 |

### 回傳值規則

- closure 回傳 **truthy** → **commit** → `transaction()` 回傳 closure 的回傳值
- closure 回傳 **falsy** → **rollback** → `transaction()` 回傳 `null`
- closure 發生 **Exception** → **rollback** → `transaction()` 回傳 `null`

PHP 中的 falsy 值：`null`、`false`、`0`、`[]`、`""`、`"0"`

```php
// 成功範例
$user = \Orm\Helper::transaction('', static function () {
  return User::create(['name' => 'OA']);
});
// $user 為 User 實例

// 失敗範例（回傳 false → rollback）
$result = \Orm\Helper::transaction('', static function () {
  User::create(['name' => 'OA']);
  return false;
});
// $result === null，且 User 不會被新增
```

---

## 取得錯誤訊息

透過 `$errors` 參數取得交易過程中的錯誤：

```php
$errors = null;
$result = \Orm\Helper::transaction('', static function () {
  return User::create(['name' => 'OA']);
}, $errors);

if ($result) {
  // 成功，$errors === null
} else {
  // 失敗，$errors 為字串陣列
  var_dump($errors); // ['錯誤訊息...']
}
```

---

## rollback()

在 closure 中手動觸發 rollback：

```php
$result = \Orm\Helper::transaction('', static function () {
  $user = User::create(['name' => 'OA']) ?? \Orm\Helper::rollback('建立使用者失敗');

  $profile = Profile::create(['userId' => $user->id]) ?? \Orm\Helper::rollback('建立檔案失敗');

  return $profile;
});
```

**方法簽名**：`\Orm\Helper::rollback(?string $message = null): never`

| 參數 | 說明 |
|------|------|
| `$message` | 錯誤訊息（`null` 時使用最後一筆 Log） |

`rollback()` 內部會拋出 Exception，由 `transaction()` 捕獲後自動 rollback。

---

## 多步寫入範例

```php
// 資料準備放在 transaction 外面
$userParam = [
  'name'  => 'OA',
  'email' => 'oa@example.com',
];

$result = \Orm\Helper::transaction('', static function () use ($userParam) {
  // 建立使用者
  $user = User::create($userParam) ?? \Orm\Helper::rollback('建立使用者失敗');

  // 建立關聯資料（依賴 $user->id，所以放在 transaction 內）
  $profile = Profile::create([
    'userId' => $user->id,
    'bio'    => '',
  ]) ?? \Orm\Helper::rollback('建立個人檔案失敗');

  return $user;
});
```

---

## 注意事項

- `transaction()` 不會自動 rollback ORM 的 `create()` / `save()` / `delete()` 失敗，需透過 `?? \Orm\Helper::rollback()` 或 `?? error()` 手動檢查
- 屬性設定、讀取查詢等非寫入操作放在 `transaction()` 外面
- `transaction()` 內只放寫入動作（`save()`、`create()`、`delete()`）
- 同一動作的多步寫入應整併為一個 `transaction()`
