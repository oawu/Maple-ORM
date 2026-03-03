# 上傳器（Uploader）

## 情境設定

以下範例使用 `Task` 和 `User` 兩個 Model：

**Task 資料表**

| 欄位 | 型別 | 說明 |
|------|------|------|
| id | int | 主鍵 |
| title | varchar(255) | 標題 |
| zip | varchar(255) | 檔案上傳器欄位 |

**User 資料表**

| 欄位 | 型別 | 說明 |
|------|------|------|
| id | int | 主鍵 |
| name | varchar(255) | 名稱 |
| avatar | varchar(255) | 圖片上傳器欄位 |

```php
namespace Model;

class Task extends \Orm\Model {}

class User extends \Orm\Model {}
```

---

## 綁定上傳器

上傳器是一項特殊的 Plugin 功能，將資料表的 `varchar` 欄位綁定為**檔案上傳器**或**圖片上傳器**。

在 Model 定義之後，呼叫 `bindFile()` 或 `bindImage()`：

```php
// 檔案上傳器
Task::bindFile('zip');

// 圖片上傳器
User::bindImage('avatar');
```

**方法簽名**：

- `static bindFile(string $column, ?callable $func = null): void`
- `static bindImage(string $column, ?callable $func = null): void`

第二參數 `$func` 可針對個別欄位自訂設定（覆蓋全域 `setUploader` 的設定）。

---

## 檔案上傳器（File）

### 賦值方式

綁定後，支援三種來源格式：

```php
$task = Task::one(1);

// 1. 本機檔案路徑
$task->zip = '/path/to/demo.zip';

// 2. $_FILES（表單上傳）
$task->zip = $_FILES['file'];

// 3. 網址（自動下載）
$task->zip = 'https://example.com/demo.zip';

// 存檔
$task->save();
```

### 清除檔案

賦值 `null` 清除檔案（從 Driver 刪除已儲存的檔案）：

```php
$task->zip = null;
$task->save();
```

### 取得網址

```php
$task = Task::one(1);
echo $task->zip->getUrl();
// http://example.com/Storage/Task/zip/0000/0001/fe01ce2a7fbac8fafaed7c982a04e229.zip
```

當欄位無檔案時，回傳 `setDefaultUrl()` 設定的預設網址。

### 下載檔案

```php
$task->zip->saveAs('/path/to/save.zip');
// 回傳 true（成功）/ false（失敗）
```

**方法簽名**：`saveAs(string $dest, bool $throwException = false): bool`

### toArray()

```php
$task->zip->toArray();       // 回傳 getUrl() 結果
$task->zip->toArray(true);   // 回傳原始值（檔名字串或 null）
```

---

## 圖片上傳器（Image）

圖片上傳器除了基本的檔案功能外，還支援**縮圖版本**。

### 綁定與設定版本

```php
User::bindImage('avatar', static function (\Orm\Core\Plugin\Uploader\Image $image) {
  // 自訂命名規則（圖片預設為 md5 → random → origin）
  $image->setNamingSort('md5', 'random', 'origin');

  // 設定縮圖版本
  $image->addVersion('w100')->setMethod('resize')->setArgs(100, 100, 'width');
  $image->addVersion('thumb')->setMethod('adaptiveResize')->setArgs(200, 200);
});
```

### 賦值方式

與檔案上傳器相同（本機路徑、`$_FILES`、網址）。賦值時會自動：

1. 讀取 EXIF 方向資訊，自動旋轉校正
2. 依據每個 version 設定產生對應尺寸的縮圖
3. 上傳所有版本（原圖 + 各縮圖）到 Driver

```php
$user = User::one(1);
$user->avatar = $_FILES['avatar'];
$user->save();
```

### 取得網址

```php
// 原圖
echo $user->avatar->getUrl();
// http://example.com/Storage/User/avatar/0000/0001/fe01ce2a7fbac8fafaed7c982a04e229.png

// 指定版本（版本 key 前綴 + 底線 + 檔名）
echo $user->avatar->getUrl('w100');
// http://example.com/Storage/User/avatar/0000/0001/w100_fe01ce2a7fbac8fafaed7c982a04e229.png

echo $user->avatar->getUrl('thumb');
// http://example.com/Storage/User/avatar/0000/0001/thumb_fe01ce2a7fbac8fafaed7c982a04e229.png
```

**方法簽名**：`getUrl(string $key = ''): string`

- `$key` 為空字串或不傳 → 原圖
- `$key` 為版本名稱 → 對應版本的縮圖

### 下載指定版本

```php
$user->avatar->saveAs('/path/to/save.png');           // 原圖
$user->avatar->saveAs('/path/to/save.png', 'w100');   // 指定版本
```

**方法簽名**：`saveAs(string $dest, string $key = '', bool $throwException = false): bool`

### toArray()

```php
$user->avatar->toArray();
// ['w100' => 'http://...w100_xxx.png', 'thumb' => 'http://...thumb_xxx.png', '' => 'http://...xxx.png']

$user->avatar->toArray(true);
// 原始值（檔名字串或 null）
```

### 取得所有版本

```php
$versions = $user->avatar->getVersions();
// ['w100' => Version, 'thumb' => Version]
```

---

## 縮圖版本（Version）

每個版本由 `addVersion()` 建立，透過 `setMethod()` 和 `setArgs()` 設定處理方式：

```php
$image->addVersion('w100')
  ->setMethod('resize')       // 縮圖方法名稱
  ->setArgs(100, 100, 'width');  // 傳給方法的參數
```

### 可用的縮圖方法（Gd）

| 方法 | 參數 | 說明 |
|------|------|------|
| `resize` | `width, height, method` | 等比縮放，method: `'w'`/`'width'`、`'h'`/`'height'`、`'a'`/`'auto'` |
| `resizeByWidth` | `width` | 按寬度等比縮放 |
| `resizeByHeight` | `height` | 按高度等比縮放 |
| `scale` | `percent` | 按比例縮放（0.5 = 50%） |
| `crop` | `startX, startY, width, height` | 裁切 |
| `cropCenter` | `width, height` | 中心裁切 |
| `adaptiveResize` | `width, height` | 等比縮放 + 中心裁切 |
| `adaptiveResizePercent` | `width, height, percent` | 等比縮放 + 自訂位置裁切 |
| `adaptiveResizeQuadrant` | `width, height, quadrant` | 等比縮放 + 象限裁切（`'c'`/`'l'`/`'r'`/`'t'`/`'b'`） |
| `pad` | `width, height, color` | 填補背景色至指定尺寸 |
| `rotate` | `degree, color` | 旋轉 |

---

## 儲存路徑

檔案在 Driver 中的儲存路徑結構：

```
/{baseDir}/{tableName}/{columnName}/{hashPath}/{fileName}
```

- **baseDir**：`setBaseDir()` 設定（如 `Storage`）
- **tableName**：資料表名稱（如 `Task`）
- **columnName**：欄位名稱（如 `zip`）
- **hashPath**：若有設定 Hashids，使用 Hashids 編碼並拆分目錄；否則使用 id 的 36 進位表示（每 4 碼一層）
- **fileName**：依命名規則決定（md5 / origin / random）+ 副檔名

範例（id=1, baseDir=Storage, 無 Hashids）：

```
/Storage/Task/zip/0000/0001/fe01ce2a7fbac8fafaed7c982a04e229.zip
```

Image 的版本檔案，在檔名前加上 `{versionKey}_`：

```
/Storage/User/avatar/0000/0001/w100_fe01ce2a7fbac8fafaed7c982a04e229.png
```

---

## 命名規則

透過 `setNamingSort()` 設定檔名的優先命名方式：

| 規則 | 常數 | 說明 |
|------|------|------|
| `origin` | `Uploader::NAME_SORT_ORIGIN` | 保留原始檔名 |
| `md5` | `Uploader::NAME_SORT_MD5` | 檔案內容的 MD5 雜湊 |
| `random` | `Uploader::NAME_SORT_RANDOM` | 32 字元隨機十六進位字串 |

按順序取第一個非空的名稱。

- **File 預設**：`origin → md5 → random`
- **Image 預設**：`md5 → random → origin`

---

## 個別設定 Driver

除了全域 `setUploader()` 外，可在 `bindFile()` / `bindImage()` 的 callback 中設定個別欄位的 Driver：

```php
Task::bindFile('zip', static function (\Orm\Core\Plugin\Uploader $uploader) {
  $uploader->setDriver('S3', [
    'bucket' => 'my-bucket',
    'access' => 'ACCESS_KEY',
    'secret' => 'SECRET_KEY',
    'region' => 'ap-northeast-1',
    'acl'    => 'private',
    'ttl'    => 0,
    'isUseSSL' => false,
  ]);
});
```

---

## hides 靜態屬性

Model 可定義 `$hides` 靜態屬性，控制 `toArray()` 時隱藏特定欄位：

```php
class User extends \Orm\Model {
  static $hides = ['password'];
}

$user = User::one(1);
$array = $user->toArray();
// $array 中不包含 'password' 欄位
```

上傳器欄位在 `toArray()` 中：

- **File**：回傳 `getUrl()` 字串
- **Image**：回傳各版本 URL 的關聯式陣列

使用 `toArray(true)` 取得原始值（資料庫中儲存的檔名字串）。
