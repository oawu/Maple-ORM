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
1. 修改 `Config.php` 內容
2. 專案目錄下執行 `php index.php` 即可測試

> 測試項目有 Model 應用、關聯功能、底線命名的關聯功能、Models merge 功能、Uploader 功能

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
