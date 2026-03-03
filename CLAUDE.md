# Maple ORM 專案指南

本文件供 Claude 及開發者快速了解專案架構。編碼規範請見 `.claude/rules/`（自動載入）。

---

## 專案概述

Maple ORM 是從 [Maple 9](https://github.com/oawu/Maple) 框架抽出的獨立 ORM 函式庫。

**技術規格：**
- PHP 7.4+
- MySQL 5.5+
- 支援駝峰（camelCase）與蛇形（snake_case）命名
- 支援多組資料庫、讀寫分離
- Plugin 系統（DateTime、Binary、Uploader）
- Uploader 支援 Local 與 S3 驅動

---

## 資料夾結構

```
99_Maple-ORM/
├── Orm/                  # ORM 核心原始碼
│   ├── Model.php         # 基礎 Model 類別（Orm\Model）
│   ├── Helper.php        # 工具函式（Orm\Helper）
│   └── Core/             # 核心基礎設施（Orm\Core\*）
│       ├── Builder.php   # Query Builder（流式介面）
│       ├── Column.php    # 欄位元資料與驗證
│       ├── Config.php    # 資料庫連線設定
│       ├── Connection.php # PDO 連線管理
│       ├── Table.php     # 資料表元資料快取
│       ├── Plugin.php    # Plugin 基礎類別
│       ├── Plugin/       # 內建 Plugin
│       │   ├── DateTime.php    # 日期時間
│       │   ├── Binary.php      # 二進位資料
│       │   └── Uploader/       # 檔案上傳
│       │       ├── File.php    # 檔案上傳
│       │       ├── Image.php   # 圖片上傳（含版本）
│       │       └── Driver/     # 儲存驅動（Local、S3）
│       ├── Thumbnail.php       # 縮圖抽象類別
│       │   ├── Gd.php          # GD 實作
│       │   └── Imagick.php     # ImageMagick 實作
│       ├── Hashids.php   # ID 編碼
│       ├── Inflect.php   # 單複數轉換
│       └── S3.php        # S3 操作
├── Model/                # 測試用 Model（按功能分類）
│   ├── Create/、Select/、Update/、Delete/
│   ├── Relation/、Transaction/
│   ├── Image/、File/、ReadWrite/
├── Test/                 # 測試檔案
│   ├── 0.php             # 共用設定
│   ├── 1-create.php ~ 11-driver-s3.php
├── Guide/                # 使用文件（Markdown）
│   ├── 00_config.md ~ 08_uploader.md
├── Sample/               # 測試用範例檔案（圖片、字型）
├── Storage/              # 本地檔案儲存（測試用）
├── Tmp/                  # 暫存目錄（測試用）
├── Config.php            # 資料庫設定（含密碼，勿提交）
├── Autoload.php          # PSR-4 自動載入
├── index.php             # 測試入口
├── Test.php              # 測試執行器
└── readme.md             # 專案 README
```

---

## 核心架構

### 命名空間

| 命名空間 | 說明 |
|----------|------|
| `Orm\Model` | 基礎 Model（所有 Model 繼承此類別） |
| `Orm\Helper` | 工具函式（WHERE 建構、transaction、版本） |
| `Orm\Core\Builder` | Query Builder（流式查詢介面） |
| `Orm\Core\Config` | 資料庫連線設定物件 |
| `Orm\Core\Connection` | PDO 連線管理與連線池 |
| `Orm\Core\Table` | 資料表結構快取 |
| `Orm\Core\Column` | 欄位定義與型別偵測 |
| `Orm\Core\Plugin` | Plugin 基礎類別 |
| `Orm\Core\Plugin\DateTime` | 日期時間 Plugin |
| `Orm\Core\Plugin\Binary` | 二進位 Plugin |
| `Orm\Core\Plugin\Uploader\File` | 檔案上傳 Plugin |
| `Orm\Core\Plugin\Uploader\Image` | 圖片上傳 Plugin |

### Model 定義範例

```php
namespace Model\Select;

class User extends \Orm\Model {
  // 可選：覆寫預設值
  public static ?string $tableName = null;    // 自動推斷資料表名
  public static ?array  $primaries = null;    // 自動偵測主鍵
  public static array   $hides     = [];      // toArray 隱藏欄位
}

// 綁定 Uploader（類別定義之後）
User::bindImage('avatar', function($image) {
  $image->addVersion('thumb')->setMethod('resize')->setArgs(100, 100);
});
```

---

## API 速查表

### Model 靜態方法

| 分類 | 方法 |
|------|------|
| **建立** | `create($attrs, $allow)`、`creates($rows, $limit)` |
| **查詢** | `one(...)`、`first(...)`、`last(...)`、`all()`、`count()` |
| **條件** | `where(...)`、`whereIn()`、`in()`、`whereNotIn()`、`notIn()`、`whereBetween()`、`between()`、`whereGroup()` |
| **排序** | `order(...)`、`byKey(...)` |
| **分頁** | `limit()`、`offset()` |
| **選取** | `select(...)`、`group()`、`having()` |
| **關聯** | `hasMany()`、`hasOne()`、`belongsTo()`、`belongsToMany()`、`relation(...)` |
| **批次** | `updates($sets)`、`deletes()` |
| **連線** | `db()`、`close()`、`truncate()` |

### Model 實例方法

| 方法 | 回傳 | 說明 |
|------|------|------|
| `save(&$count)` | `?self` | 儲存（失敗回傳 null） |
| `delete(&$count)` | `?self` | 刪除（失敗回傳 null） |
| `attrs()` | `array` | 取得所有屬性 |
| `hasMany()`、`hasOne()`、`belongsTo()` | `Builder` | 延遲載入關聯 |

### Builder 鏈式方法

| 分類 | 方法 |
|------|------|
| **條件** | `where()`、`orWhere()`、`whereIn()`、`orWhereIn()`、`whereNotIn()`、`orWhereNotIn()`、`whereBetween()`、`orWhereBetween()`、`whereGroup()`、`orWhereGroup()` |
| **排序** | `order()`、`byKey()` |
| **分頁** | `limit()`、`offset()` |
| **選取** | `select()`、`group()`、`having()`、`lockForUpdate()` |
| **關聯** | `relation(...)` |
| **終端** | `all()`、`one()`、`first()`、`last()`、`count()`、`update($attrs)`、`delete()` |

### Helper 常用方法

| 方法 | 說明 |
|------|------|
| `Helper::version()` | 取得 ORM 版本 |
| `Helper::transaction($db, $closure, &$errors)` | 交易（自動 commit/rollback） |
| `Helper::rollback($message)` | 手動 rollback |
| `Helper::toArray($model, $isRaw)` | Model 轉陣列 |
| `Helper::where($table, ...)` | 建構 WHERE 子句 |

---

## 測試

### 執行方式

```bash
# 透過 Docker 執行（本機無 PHP）
docker exec php zsh -c "cd ~/Workspace/99_Maple-ORM && php index.php"
```

### 測試清單

| # | 檔案 | 測試內容 |
|---|------|----------|
| 01 | 1-create.php | CREATE 單筆/批次、驗證、預設值、Plugin |
| 02 | 2-select.php | SELECT 查詢、WHERE 變體、排序、分組 |
| 03 | 3-update.php | UPDATE 整筆/部分、dirty tracking |
| 04 | 4-delete.php | DELETE 驗證、硬刪除 |
| 05 | 5-relation.php | 延遲/預先載入關聯 |
| 06 | 6-transaction.php | 交易原子性、rollback |
| 07 | 7-put-image.php | 圖片上傳、版本、Storage |
| 08 | 8-put-file.php | 檔案上傳、大小/類型驗證 |
| 09 | 9-db-read-write.php | 讀寫分離 |
| 10 | 10-Thumbnail.php | GD/ImageMagick 縮圖 |
| 11 | 11-driver-s3.php | S3 驅動 |

### 測試結構慣例

```php
// 1. DDL 建表
$sql = "CREATE TABLE `TestTable` (...)";
Connection::instance()->runQuery($sql);

// 2. 測試邏輯（失敗 throw Exception）
if ($result !== $expected) {
  throw new Exception('斷言失敗');
}

// 3. 清理（Test.php 結尾統一清除 Tmp/ 和 Storage/）
```

---

## 開發注意事項

### PHP 7.4 限制

禁止使用 PHP 8.0+ 語法：
- named arguments
- union types（`string|int`）
- `match` 表達式
- nullsafe operator（`?->`）
- `str_contains()`、`str_starts_with()` 等 PHP 8 函式

### 回傳值約定

ORM 的寫入操作**失敗回傳 null**，不會 throw：

| 方法 | 成功 | 失敗 |
|------|------|------|
| `create()` | `?static`（Model 實例） | `null` |
| `save()` | `?self`（Model 實例） | `null` |
| `delete()` | `?self`（Model 實例） | `null` |
| `creates()` | `?int`（影響列數） | `null` |
| `updates()` | `?int`（影響列數） | `null` |
| `deletes()` | `?int`（影響列數） | `null` |

### Plugin 注意事項

- DateTime/Binary 等 Plugin 欄位永遠回傳 Plugin 物件（即使 DB 值為 NULL）
- 使用 `getValue()` 取得原始值，不能直接 `!== null` 判斷
- Plugin 透過 `Column::getType()` 匹配 `allowTypes()` 自動實例化

### 私有變數命名

ORM 核心使用 `$_` 前綴表示 private/protected 變數：

```php
private static array $_instances = [];
private string $_hostname;
protected $_value;
```

---

## 文件導讀

| 文件 | 內容 |
|------|------|
| readme.md | 專案簡介、測試方式 |
| Guide/00_config.md | 初始設定、命名慣例、錯誤處理、日誌 |
| Guide/01_create.md | 新增資料 |
| Guide/02_select.md | 查詢資料 |
| Guide/03_update.md | 更新資料 |
| Guide/04_delete.md | 刪除資料 |
| Guide/05_transaction.md | 交易事件 |
| Guide/06_relation.md | 關聯資料 |
| Guide/07_relation.md | 預先關聯（Eager Loading） |
| Guide/08_uploader.md | 綁定檔案（Image/File Uploader） |
| .claude/rules/ | 編碼規範（自動載入） |
| .claude/commands/ | 自訂指令（commit、review） |
