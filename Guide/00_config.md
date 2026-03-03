# 初始設定

引用此 ORM 須先做基礎設定，可參考專案根目錄的 `Config.php` 範例。

---

## 資料庫連線

支援多組資料庫連線（讀寫分離），以字串 key 區分。空字串 `''` 為預設資料庫：

```php
use \Orm\Core\Config;

// 預設資料庫
\Orm\Model::setConfig('', Config::create()
  ->setHostname('127.0.0.1')
  ->setUsername('root')
  ->setPassword('password')
  ->setDatabase('my_db'));

// 第二組資料庫（例如唯讀）
\Orm\Model::setConfig('R', Config::create()
  ->setHostname('127.0.0.1')
  ->setUsername('root')
  ->setPassword('password')
  ->setDatabase('my_db_read'));
```

`Config` 可用方法：

| 方法 | 預設值 | 說明 |
|------|--------|------|
| `setHostname(string)` | `''` | 主機位址 |
| `setUsername(string)` | `''` | 使用者名稱 |
| `setPassword(string)` | `''` | 密碼 |
| `setDatabase(string)` | `''` | 資料庫名稱 |
| `setEncoding(string)` | `'utf8mb4'` | 字元編碼 |

---

## 指定 Model Namespace

ORM 會根據 Model class 的名稱自動推導資料表名稱。若 Model 有多餘的 namespace 前綴，可設定要去除的部分：

```php
// Model 完整名稱為 Model\User，去除 'Model' 後得到 'User' → 對應資料表 User
\Orm\Model::setNamespace('Model');

// 也可一次設定多個
\Orm\Model::setNamespace('App', 'Model');
```

**方法簽名**：`setNamespace(string ...$names): void`

---

## 命名規則（Table / Column）

設定資料庫的命名風格，預設為**駝峰命名**。

```php
// 駝峰命名（預設）
\Orm\Model::setCaseTable(\Orm\Model::CASE_CAMEL);
\Orm\Model::setCaseColumn(\Orm\Model::CASE_CAMEL);

// 蛇形命名
\Orm\Model::setCaseTable(\Orm\Model::CASE_SNAKE);
\Orm\Model::setCaseColumn(\Orm\Model::CASE_SNAKE);
```

兩種命名的資料庫對照：

| 項目 | 駝峰（Camel） | 蛇形（Snake） |
|------|---------------|---------------|
| Table | `User`（單數大駝峰） | `users`（複數蛇形） |
| Column | `userId`（小駝峰） | `user_id`（蛇形） |
| createAt | `createAt` | `created_at` |
| updateAt | `updateAt` | `updated_at` |

---

## Error Func

設定**嚴重錯誤**的處理函式，例如未設定連線方式、欄位不可為 null 等。

```php
\Orm\Model::setErrorFunc(static function (...$args) {
  var_dump($args);
  exit();
});
```

**方法簽名**：`setErrorFunc(?callable $func = null): void`

---

## Query Log Func

紀錄程式執行過程中所有的 SQL Query。

```php
\Orm\Model::setQueryLogFunc(static function (string $db, string $sql, array $vals, bool $status, float $during) {
  // $db     — 資料庫 key（'' 為預設）
  // $sql    — SQL 字串
  // $vals   — 綁定參數
  // $status — 執行結果（true 成功 / false 失敗）
  // $during — 耗時（秒，浮點數）
});
```

**方法簽名**：`setQueryLogFunc(?callable $func = null): void`

取得最後一筆 Query Log：

```php
$log = \Orm\Model::getLastQueryLog();
// 回傳 array: ['db', 'sql', 'vals', 'status', 'during', 'log']
// 或 null（尚無紀錄時）
```

---

## Log Func

紀錄**可預期的錯誤**，例如新增 / 更新 / 刪除失敗時的原因。

```php
\Orm\Model::setLogFunc(static function (string $message) {
  // $message — 錯誤訊息
});
```

**方法簽名**：`setLogFunc(?callable $func = null): void`

取得最後一筆 Log：

```php
$message = \Orm\Model::getLastLog();
// 回傳 string 或 null
```

---

## Cache Func

針對特定功能啟用快取。目前支援 `MetaData`（資料表欄位結構），ORM 首次查詢資料表時會執行 `SHOW COLUMNS` 取得欄位資訊，透過 cache 可減少重複查詢。

```php
\Orm\Model::setCacheFunc('MetaData', static function (string $key, callable $closure) {
  // 自行實作快取邏輯，例如 Redis、File cache 等
  // $key     — 快取鍵值
  // $closure — 原始取得資料的 closure，呼叫 $closure() 取得結果
  return $closure();
});
```

**方法簽名**：`setCacheFunc(string $type, callable $func): void`

---

## Hashids

設定 Uploader 儲存路徑的 Hashids 加密。若未設定，路徑以 id 流水號為主。

```php
\Orm\Model::setHashids(8, 'my-salt', 'abcdefghijklmnopqrstuvwxyz1234567890');
```

**方法簽名**：`setHashids(int $minLength = 0, string $salt = '', ?string $alphabet = null): void`

| 參數 | 說明 |
|------|------|
| `$minLength` | 最小長度 |
| `$salt` | 加密鹽值 |
| `$alphabet` | 自訂字母表，`null` 使用預設 |

---

## 縮圖方式

指定 Image Uploader 使用的縮圖工具。目前內建 `Gd` 和 `Imagick`：

```php
// 使用 GD
\Orm\Model::setImageThumbnail(fn ($file) => \Orm\Core\Thumbnail\Gd::create($file));

// 使用 Imagick
\Orm\Model::setImageThumbnail(fn ($file) => \Orm\Core\Thumbnail\Imagick::create($file));
```

**方法簽名**：`setImageThumbnail(callable $func): void`

---

## Uploader 預設值

設定所有 Uploader（File / Image）的共用預設值。每個 Uploader 被初始化時，都會呼叫此 callback：

```php
\Orm\Model::setUploader(static function (\Orm\Core\Plugin\Uploader $uploader): void {
  // ===== 儲存驅動 =====

  // Local 驅動
  $uploader->setDriver('Local', ['storage' => '/path/to/storage/']);

  // S3 驅動
  // $uploader->setDriver('S3', [
  //   'bucket' => '',       // 儲存空間名稱（必填）
  //   'access' => '',       // 存取金鑰（必填）
  //   'secret' => '',       // 密鑰（必填）
  //   'region' => 'ap-northeast-1',  // 區域
  //   'acl'    => 'public-read',     // 權限：private / public-read / public-read-write / authenticated-read
  //   'ttl'    => 0,                 // Cache 秒數
  //   'isUseSSL' => false,           // 是否使用 SSL
  // ]);

  // ===== 命名規則 =====
  // 依優先順序決定檔名：origin（原始檔名）、md5（內容雜湊）、random（隨機）
  $uploader->setNamingSort('origin', 'md5', 'random');

  // ===== 目錄與網址 =====
  $uploader->setTmpDir('/path/to/tmp/');            // 暫存目錄
  $uploader->setBaseDir('Storage');                  // 儲存基礎目錄
  $uploader->setBaseUrl('http://example.com/');      // 基礎網址
  $uploader->setDefaultUrl('http://example.com/404.png');  // 預設網址（無檔案時）

  // ===== Image 專用：縮圖版本 =====
  if ($uploader instanceof \Orm\Core\Plugin\Uploader\Image) {
    $uploader->addVersion('w100')->setMethod('resize')->setArgs(100, 100, 'width');
  }
});
```

**方法簽名**：`setUploader(callable $func): void`

---

## 完整設定範例

```php
<?php

use \Orm\Core\Config;

// 資料庫連線
\Orm\Model::setConfig('', Config::create()
  ->setHostname('db-mysql')
  ->setUsername('root')
  ->setPassword('1234')
  ->setDatabase('orm'));

// Model namespace
\Orm\Model::setNamespace('Model');

// 命名規則
\Orm\Model::setCaseTable(\Orm\Model::CASE_CAMEL);
\Orm\Model::setCaseColumn(\Orm\Model::CASE_CAMEL);

// 錯誤處理
\Orm\Model::setErrorFunc(static function (...$args) {
  var_dump($args);
  exit();
});

// Query Log
\Orm\Model::setQueryLogFunc(static function (string $db, string $sql, array $vals, bool $status, float $during) {
});

// Log
\Orm\Model::setLogFunc(static function (string $message) {
});

// Cache
\Orm\Model::setCacheFunc('MetaData', fn (string $key, callable $closure) => $closure());

// Hashids
\Orm\Model::setHashids(8, 'key', 'abcdefghijklmnopqrstuvwxyz1234567890');

// 縮圖
\Orm\Model::setImageThumbnail(fn ($file) => \Orm\Core\Thumbnail\Gd::create($file));

// Uploader
\Orm\Model::setUploader(static function (\Orm\Core\Plugin\Uploader $uploader): void {
  $uploader->setDriver('Local', ['storage' => PATH]);
  $uploader->setNamingSort('origin', 'md5', 'random');
  $uploader->setTmpDir(PATH_TMP);
  $uploader->setBaseDir('Storage');
  $uploader->setBaseUrl(BASE_URL);
  $uploader->setDefaultUrl(BASE_URL . '404.png');

  if ($uploader instanceof \Orm\Core\Plugin\Uploader\Image) {
    $uploader->addVersion('w100')->setMethod('resize')->setArgs(100, 100, 'width');
  }
});
```
