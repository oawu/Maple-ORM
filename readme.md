# Maple ORM
歡迎來到 Model ORM Model

## 說明
* 這是 [Maple 8](https://github.com/comdan66/Maple) 所採用的 active record ORM
* 支援 PHP 7.0+
* 支援 MySQL 5.6+
* 支援 [駝峰命名法](https://en.wikipedia.org/wiki/Camel_case) 的資料庫
* 支援 [蛇形命名法](https://en.wikipedia.org/wiki/Snake_case) 的資料庫
* 支援欄位綁定 Uploader，分別有 Image、File 兩種 Uploader
* 支援 Local Uploader 與 S3 Uploader 上傳方式

## 測試
1. 建立 `Storage` 目錄，將權限改為 `777`
2. 修改 `Config.php` 內容
3. 專案目錄下執行 `php index.php` 即可測試

> 測試項目有 Model 應用、關聯功能、底線命名的關聯功能、Models merge 功能、Uploader 功能