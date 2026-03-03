# 新增資料

## 情境設定

以下範例使用 `User` Model，對應資料表：

| 欄位 | 型別 | 說明 |
|------|------|------|
| id | int | 主鍵，自動遞增 |
| name | varchar(255) | 名稱 |
| age | int | 年齡 |
| createAt | datetime | 建立時間（自動填入） |
| updateAt | datetime | 更新時間（自動填入） |

```php
namespace Model;

class User extends \Orm\Model {}
```

---

## create()

新增單筆資料。成功回傳 Model 實例，失敗回傳 `null`。

```php
$user = User::create([
  'name' => 'OA',
  'age'  => 18,
]);

if ($user) {
  echo $user->id;   // 1（自動遞增）
  echo $user->name; // OA
  echo $user->age;  // 18
} else {
  // 新增失敗，$user 為 null
}
```

**方法簽名**：`static create(array $attrs = [], array $allow = [], ?string $db = null): ?self`

| 參數 | 說明 |
|------|------|
| `$attrs` | 欄位與值的關聯式陣列 |
| `$allow` | 白名單陣列，限制只能寫入哪些欄位（空陣列 = 不限制） |
| `$db` | 指定資料庫 key（`null` 使用預設） |

### allow 白名單

當資料來源不確定（如外部 API 回傳）時，用 `$allow` 過濾安全欄位：

```php
$data = ['name' => 'OA', 'age' => 18, 'role' => 'admin'];

// 只允許寫入 name 和 age，role 會被忽略
$user = User::create($data, ['name', 'age']);
```

自己組裝的資料（key 確定）不需要 allow。

### 自動填入時間

`createAt` 和 `updateAt` 欄位會自動填入當前時間，不需要手動設定。

### 指定資料庫

```php
// 寫入 'W' 資料庫
$user = User::create(['name' => 'OA'], [], 'W');
```

---

## creates()

批次新增多筆資料。成功回傳新增筆數，失敗回傳 `null`。

```php
$count = User::creates([
  ['name' => 'A', 'age' => 10],
  ['name' => 'B', 'age' => 20],
]);

// $count === 2（新增 2 筆）
```

**方法簽名**：`static creates(array $rows = [], int $limit = 50, ?string $db = null): ?int`

| 參數 | 說明 |
|------|------|
| `$rows` | 二維陣列，每個元素為一筆資料 |
| `$limit` | 每批次最多幾筆（預設 50），超過會自動分批 INSERT |
| `$db` | 指定資料庫 key |

### 分批新增

當資料量大時，會自動分批執行 INSERT：

```php
$rows = [
  ['name' => 'A', 'age' => 10],
  ['name' => 'B', 'age' => 20],
  // ... 共 26 筆
  ['name' => 'Y', 'age' => 31],
  ['name' => 'Z', 'age' => 26],
];

// 每 10 筆一批，26 筆會分 3 次 INSERT
$count = User::creates($rows, 10);
// $count === 26（成功）或 null（失敗）
```

### 指定資料庫

```php
$count = User::creates($rows, 50, 'W');
```

---

## afterCreates

Model 可定義 `$afterCreates` 靜態屬性，在 `create()` 成功後自動執行一系列方法。

```php
class User extends \Orm\Model {
  static $afterCreates = ['initProfile', 'sendWelcome'];

  public function initProfile() {
    return Profile::create(['userId' => $this->id]);
  }

  public function sendWelcome($profile) {
    // $profile 是上一步 initProfile() 的回傳值
    // 寄送歡迎信...
    return true;
  }
}
```

**執行規則**：

- 按陣列順序依次執行
- 每個方法接收**前一步的回傳值**作為參數（第一步無參數）
- 任一步回傳 falsy 值會**中斷**後續步驟
- **不影響已新增的資料**：即使 callback 失敗，`create()` 仍回傳成功建立的 Model 實例
- 失敗訊息會透過 Log Func 記錄

> `afterCreates` 僅適用於 `create()`，不適用於 `creates()`。
