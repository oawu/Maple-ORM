# 設定

引用此 ORM 須先做的基礎設定，可參考 `Config.php` 內的寫法，以下為設定項目：

* 必須設定
  * 資料庫連線方式，可設定多組
  * 指定 Model 放置的目錄

* 非必須
  * 專案 Model 的命名規則
  * 指定 error func 函式
  * 指定 query log 函式
  * 指定 model log 函式
  * 指定 cacheFunc 函式
  * 施加鹽巴
  * 指定縮圖方式
  * 設定所有上傳器預設值

## 資料庫連線方式
目前支援跨資料庫功能，其設定方式如下，依序填入其值即可：

```php
\Orm\Model::setConfig('', \Orm\Core\Config::create()
  ->setHostname('127.0.0.1')
  ->setUsername('root')
  ->setPassword('password')
  ->setDatabase('database'));
```

## 指定 Model 需要去除的 namespace
應用的 Model 有可能會有多餘的 namespace，可以透過此設定來去除。

```php
\Orm\Model::setNamespace('App\Model\...');
```

## 專案 Model 的命名規則
針對此專案的資料庫命名規則做設定，預設是 **駝峰命名** 方式，但亦可選擇 **蛇形命名** 模式。
欄位亦可設定選擇哪種方式。

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
\Orm\Model::setCaseTable(\Orm\Model::CASE_CAMEL);
\Orm\Model::setCaseColumn(\Orm\Model::CASE_CAMEL);

// 蛇形命名

// 資料庫格式
// users       articles
// ---------   ---------
// id          id
// name        user_id
// created_at  title
//             created_at
\Orm\Model::setCaseTable(\Orm\Model::CASE_SNAKE);
\Orm\Model::setCaseColumn(\Orm\Model::CASE_SNAKE);
```

## 指定 Error Func 函式
發生**不可允許**的錯誤時，紀錄其原因，例如尚未設定連線方式時的錯誤原因。參數為字串。

```php
\Orm\Model::setErrorFunc(static function(...$args) {
  // ...
  // var_dump($args);
  // exit();
});
```

## 指定 Query Log 函式
紀錄程式執行過程中對資料庫所下的 SQL Query，參數分別：

1. 哪一個資料庫
2. SQL 字串
3. 帶入參數
4. 結果
5. 耗時，單位為 1/1000 秒

```php
\Orm\Model::setQueryLogFunc(static function(string $db, string $sql, array $vals, string $status, float $during) {
});
```

若要取得上次的 Log，可以使用 `\Orm\Model::getLastQueryLog()` 方法。

## 指定 Log 函式
發生**可允許**的錯誤時，紀錄其原因，例如新增失敗時的原因。參數為字串。

```php
\Orm\Model::setLogFunc(static function(string $message) {
});
```

若要取得上次的 Log，可以使用 `\Orm\Model::getLastLog()` 方法。


## 指定 Cache Func 函式
針對 ORM 特定功能做 Cache，目前主要是針對 `MetaData` 可做 Cache 功能。

如下範例，因為功能需求在首次撈取資料表時，都會下 meta 語法以取得所有表格欄位資訊，可以針對此功能做 cache 已減少對資料庫的 query 次數。

```php
\Orm\Model::setCacheFunc('MetaData', fn (string $key, callable $closure) {
  return $closure();
});
```

## 施加鹽巴
上傳器網址路徑的加密關鍵字串，若沒設定的話，則用 id 流水號為主。

```php
\Orm\Model::setHashids(8, 'key', 'abcdefghijklmnopqrstuvwxyz1234567890');
```

## 指定縮圖方式
搭配其他縮圖工具的功能，主要是提供給 Image Uploader 使用。

```php
\Orm\Model::setImageThumbnail(fn ($file) => \Orm\Core\Thumbnail\Imagick::create($file));
```

## 設定所有上傳器預設值
設定所有上傳器預設值，上傳器被初始化時，同時會呼叫此 function，故可以預先設定。

```php
\Orm\Model::setUploader(static function(M\Core\Plugin\Uploader $uploader): void {
});
```

設定上傳器採用哪些的 Driver，目前支援 `S3` 與 `Local` 兩種上傳方式。

採用 Local 方式的上傳器，需設定參數 `dir`，指定儲存的目錄路徑。

```php
$uploader->setDriver('Local', ['storage' => '/path/.../']);
```

採用 S3 方式的上傳器，帶入參數必需要有 **bucket**、**access**、**secret**，非必需有 **acl**、**ttl**、**isUseSSL**、**isVerifyPeer** 可以設定。

* bucket - 儲存空間名稱
* access - 存取金鑰
* secret - 密鑰
* region - 區域
* acl - 權限，共有 `private`、`public-read`、`public-read-write`、`authenticated-read` 四種權限設定，預設為 `public-read`
* ttl - cache 時間，以秒為單位，預設為 0，不做 cache
* isUseSSL - 是否採用 SSL 方式，預設為 false

```php
$uploader->setDriver('S3', [
  'bucket' => '',
  'access' => '',
  'secret' => '',
  'region' => 'ap-northeast-1',
  'acl' => 'private',
  'ttl' => 0,
  'isUseSSL' => false,
]);
```

其他設定如下。

```php
// 指定命名規則
$uploader->setNamingSort('origin', 'md5', 'random');

// 指定系統暫存目錄當暫存目錄
$uploader->setTmpDir(sys_get_temp_dir() . DIRECTORY_SEPARATOR);

// 儲存的基礎名稱
$uploader->setBaseDir('dir1', 'dir2');

// 基礎網址
$uploader->setBaseUrl('http://demo.ioa.tw/');

// 預設的網址
$uploader->setDefaultUrl('http://demo.ioa.tw/404.png');
```

可針對 Image Uploader 設定統一的縮圖版本：

```php
$uploader->addVersion('w100')->setMethod('resize')->setArgs(100, 100, 'width');
```
