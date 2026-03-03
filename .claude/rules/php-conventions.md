# PHP 開發規範

## 執行環境

**PHP 版本：7.4.33 或以上**，開發時務必確認語法與函式相容此版本，禁止使用 PHP 8.0+ 才有的特性（如 named arguments、union types、match、nullsafe operator `?->` 等）。

本機無 PHP，所有 PHP 指令必須透過 Docker 容器執行：

```bash
docker exec php zsh -c "cd ~/Workspace/99_Maple-ORM && <command>"
```

**重要：** 容器內使用 **zsh**，不要用 bash。

## 命名空間

```
Orm/Model.php             → namespace Orm;
Orm/Helper.php            → namespace Orm;
Orm/Core/Builder.php      → namespace Orm\Core;
Orm/Core/Plugin.php       → namespace Orm\Core;
Orm/Core/Plugin/DateTime.php → namespace Orm\Core\Plugin;
```

## 命名慣例

- **類別**：PascalCase（`Builder`、`DateTime`、`Uploader`）
- **方法**：camelCase（`getInstance()`、`getHashids()`）
- **常數**：CONSTANT_CASE（`PRIMARY_ID`、`CASE_CAMEL`）
- **Private/Protected 變數**：`$_` 前綴（`$_instances`、`$_hostname`）
- **參數**：`$camelCase`

```php
// ✓ 正確：private/protected 使用 $_ 前綴
private static array $_instances = [];
private string $_hostname;
protected $_value;

// ✗ 錯誤：缺少前綴
private static array $instances = [];
private string $hostname;
```

## 回傳值約定

ORM 的寫入操作**失敗回傳 null**，不會 throw。修改這些方法時必須維持此約定：

| 方法 | 成功 | 失敗 |
|------|------|------|
| `create()` | `?static` | `null` |
| `save()` | `?self` | `null` |
| `delete()` | `?self` | `null` |
| `creates()` | `?int` | `null` |
| `updates()` | `?int` | `null` |
| `deletes()` | `?int` | `null` |

## Null 合併運算子（`??`）

單層 `??` 可以使用，但禁止串接兩個以上，改用 `if/elseif` 拆開：

```php
// ✓ 正確：單層
$name = $data['name'] ?? '';

// ✗ 錯誤：兩個以上串接，難以閱讀
$value = $a ?? $b ?? '';

// ✓ 正確：拆開寫
$value = '';
if (isset($a)) {
  $value = $a;
} elseif (isset($b)) {
  $value = $b;
}
```

## Nullable 型別宣告

使用 `?type` 語法，不要用 `type = null`：

```php
// ✓ 正確
public function s3Path(?string $version = null): string

// ✗ 錯誤
public function s3Path(string $version = null): string
```

## 條件判斷：先排除再處理

優先處理可提前回傳的情況，減少巢狀層級：

```php
// ✓ 正確：先排除
if ($value === null) {
  return null;
}

// 主要邏輯...

// ✗ 錯誤：否定條件包裝主要邏輯
if ($value !== null) {
  // 一大段邏輯...
}
```

## 關聯式陣列 `=>` 對齊

所有關聯式陣列，將 `=>` 對齊方便瀏覽：

```php
// ✓ 正確：對齊 =>
$config = [
  'hostname' => 'localhost',
  'username' => 'root',
  'password' => '',
  'database' => 'test',
  'charset'  => 'utf8mb4',
];

// ✗ 錯誤：=> 未對齊
$config = [
  'hostname' => 'localhost',
  'username' => 'root',
  'password' => '',
  'database' => 'test',
  'charset' => 'utf8mb4',
];
```

## API 簽名一致性

Builder 的鏈式方法（`where`、`order`、`limit` 等）在 Model 靜態方法與 Builder 實例方法間必須保持一致的參數簽名。修改其中一處時，確認另一處同步更新。

## Plugin 開發規範

自訂 Plugin 必須實作以下抽象方法：

```php
abstract function toArray(bool $isRaw = false): mixed;
abstract function toSqlString(): ?string;
abstract function updateValue($value): self;
abstract function __toString(): string;
abstract static function allowTypes(): array;
```

- `allowTypes()` 回傳的型別字串必須與 `Column::getType()` 匹配
- `toSqlString()` 回傳值將直接用於 SQL 綁定，注意 null 處理
- `updateValue()` 透過 `parent::_setValue()` 更新內部值

## 測試撰寫慣例

新增功能時同步新增或更新 `Test/` 中的測試：

```php
// 斷言模式：條件不符 throw Exception
if ($result !== $expected) {
  throw new Exception('描述預期行為');
}

// 對應的 Model 放在 Model/<功能分類>/
// 例如 Model/Select/User.php 對應 Test/2-select.php
```

## 禁止修改項目

- `Autoload.php` 的自動載入邏輯不需變動（除非新增頂層命名空間）
- 測試結尾的 `Tmp/`、`Storage/` 清理由 `Test.php` 統一執行
