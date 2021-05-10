# config

引用此 ORM 須先做的基礎設定，可參考 `Config.php` 內的寫法，以下為設定項目：

* 必須設定
  * 資料庫連線方式
  * 指定 Model 放置的目錄
  * 專案 Model 的命名規則

* 非必須
  * 指定 query logger 函式
  * 指定 model logger 函式
  * 指定 errorFunc 函式
  * 指定 cacheFunc 函式
  * 設定所有上傳器預設值
  * 指定縮圖處理工具
  * 施加鹽巴
  * 更新 extensions 參考值

## 資料庫連線方式
目前不支援跨資料庫功能，其設定方式如下，依序填入其值即可：

```php
\M\Model::config()
  ->hostname('127.0.0.1')
  ->username('root')
  ->password('password')
  ->database('database');
```

## 指定 Model 放置的目錄
放置對應資料庫的 Model 目錄，方式如下

```php
\M\Model::dir('/path/.../models/');
```

## 專案 Model 的命名規則
針對此專案的資料庫命名規則做設定，預設是 **駝峰命名** 方式，但亦可選擇 **蛇形命名** 模式。

* 駝峰命名 - Table 名稱為**單數大駝峰**，欄位名稱為**單數小駝峰**
* 蛇形命名 - Table 名稱為**複數蛇行命名**，欄位名稱為**單數蛇行命名**

```php
// 駝峰命名

// 資料庫格式
// User       Article
// ---------  ---------
// id         id
// name       userId
// createAt   title
//            createAt
\M\Model::case(\M\Model::CASE_CAMEL);

// 蛇形命名

// 資料庫格式
// users       articles
// ---------   ---------
// id          id
// name        user_id
// created_at  title
//             created_at
\M\Model::case(\M\Model::CASE_SNAKE);
```

## 指定 query logger 函式
紀錄程式執行過程中對資料庫所下的 sql query，參數分別：

1. SQL 字串
2. 帶入參數
3. 結果
4. 耗時，單位為 1/1000 秒

```php
\M\Model::queryLogger(function($sql, $vals, $status, $during) {
});
```

## 指定 model logger 函式
發生**可允許**的錯誤時，紀錄其原因，例如新增失敗時的原因。參數為字串。

```php
\M\Model::logger(function($log) {
});
```

## 指定 errorFunc 函式
發生**不可允許**的錯誤時，紀錄其原因，例如尚未設定連線方式時的錯誤原因。參數為字串。

```php
\M\Model::errorFunc(function($message) {
});
```

## 指定 cacheFunc 函式
針對 ORM 特定功能做 Cache，目前主要是針對 `MetaData` 可做 Cache 功能。

如下範例，因為功能需求在首次撈取資料表時，都會下 meta 語法以取得所有表格欄位資訊，可以針對此功能做 cache 已減少對資料庫的 query 次數。

```php
\M\Model::cacheFunc('MetaData', function($key, $closure) {
  return $closure();
});
```

## 設定所有上傳器預設值
設定所有上傳器預設值，上傳器被初始化時，同時會呼叫此 function，故可以預先設定。

```php
\M\Model::setUploader(function($uploader) {
});
```

設定上傳器採用哪些的 Driver，目前支援 `S3` 與 `Local` 兩種上傳方式。

採用 Local 方式的上傳器，需設定參數 `dir`，指定儲存的目錄路徑。

```php
$uploader->driver('Local', ['dir' => '/path/.../']);
```

採用 S3 方式的上傳器，帶入參數必需要有 **bucket**、**access**、**secret**，非必需有 **acl**、**ttl**、**isUseSSL**、**isVerifyPeer** 可以設定。

* acl - 權限，共有 `private`、`public-read`、`public-read-write`、`authenticated-read` 四種權限設定，預設為 `public-read`
* ttl - cache 時間，以秒為單位，預設為 0，不做 cache
* isUseSSL - 是否採用 SSL 方式，預設為 false
* isVerifyPeer - 預設為 false，需要 isUseSSL 為 true 才會生效

```php
$uploader->driver('S3', [
  'bucket' => '',
  'access' => '',
  'secret' => '',
]);
```

其他設定如 **暫存目錄**、**根目錄**、**網址**以及**預設網址**。

```php
// 指定系統暫存目錄當暫存目錄
$uploader->tmpDir(sys_get_temp_dir() . DIRECTORY_SEPARATOR);

// 儲存的基礎名稱
$uploader->baseDirs('dir1', 'dir2');

// 基礎網址
$uploader->baseURL('http://demo.ioa.tw/');

// 預設的網址
$uploader->default('http://demo.ioa.tw/404.png');
```

可針對 Image Uploader 設定統一的縮圖版本，參數分別如下：

* key - 用來識別版本的關鍵字串
* thumbnail - 縮圖工具的方法
* params - 縮圖時需帶入的參數

```php
$uploader->version('w100', 'resize', [100, 100, 'width'])
```


## 指定縮圖處理工具
搭配其他縮圖工具的功能，主要是提供給 Image Uploader 使用。

```php
\M\Model::thumbnail(function($file) {
  return new ImageThumbnailTool($file);
});
```

## 施加鹽巴
上傳器網址路徑的加密關鍵字串，若沒設定的話，則用 id 流水號為主。

```php
\M\Model::salt('123...');
```

## 更新 extensions 參考值
因上傳器與圖片處理會使用到副檔名與 mime 資訊，故可以更新或指定新的規則。

```php
\M\Model::extensions(['jpg' => ['image/jpeg', 'image/pjpeg'], ...]);
```
