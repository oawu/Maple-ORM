# Maple ORM
歡迎來到 Model ORM Model

## 說明
* 這是 [Maple 9](https://github.com/oawu/Maple) 所採用的 ORM
* 支援 PHP 7.4+
* 支援 MySQL 5.7+
* 支援 [駝峰命名法](https://en.wikipedia.org/wiki/Camel_case) 的資料庫
* 支援 [蛇形命名法](https://en.wikipedia.org/wiki/Snake_case) 的資料庫
* 支援欄位綁定 Uploader，分別有 Image、File 兩種 Uploader
* 支援 Local Uploader 與 S3 Uploader 上傳方式
* 支援多組資料庫，可以讀寫分離

## 測試
1. 修改 `Config.php` 內容
1. 修改 `index.php` 的 `S3` 相關 `define` 內容
2. 專案目錄下執行 `php index.php` 即可測試

## 文件
文件可以參考 [Gitbook 文件](https://oawu.gitbook.io/maple-orm/)，或者以下 Guide

* [初始設定](Guide/00_config.md)
* [新增資料](Guide/01_create.md)
* [查詢資料](Guide/02_select.md)
* [更新資料](Guide/03_update.md)
* [刪除資料](Guide/04_delete.md)
* [交易事件](Guide/05_transaction.md)
* [關聯資料](Guide/06_relation.md)
* [預先關聯](Guide/07_relation.md)
* [綁定檔案](Guide/08_uploader.md)
