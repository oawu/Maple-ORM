# Maple ORM
歡迎來到 Maple ORM

## 說明
* 這是 [Maple 9](https://github.com/oawu/Maple) 所採用的 ORM
* 支援 PHP 7.4+
* 支援 MySQL 5.5+
* 支援 [駝峰命名法](https://en.wikipedia.org/wiki/Camel_case) 的資料庫
* 支援 [蛇形命名法](https://en.wikipedia.org/wiki/Snake_case) 的資料庫
* 支援欄位綁定 Uploader，分別有 Image、File 兩種 Uploader
* 支援 Local Uploader 與 S3 Uploader 上傳方式
* 支援多組資料庫，可以讀寫分離

## 測試

### 設定
1. 複製設定範本：`cp Config.local.sample.php Config.local.php`
2. 編輯 `Config.local.php`，填入 DB 連線資訊與 S3 金鑰

### 執行
```bash
# 透過 Docker 執行
docker exec php zsh -c "cd ~/Workspace/99_Maple-ORM && php index.php"

# 或本機有 PHP 環境時直接執行
php index.php
```

### 測試項目
| # | 測試內容 |
|---|----------|
| 01 | CREATE 單筆/批次 |
| 02 | SELECT 查詢、WHERE 變體、排序 |
| 03 | UPDATE 整筆/部分 |
| 04 | DELETE |
| 05 | 關聯（延遲/預先載入） |
| 06 | Transaction |
| 07 | 圖片上傳（Local） |
| 08 | 檔案上傳（Local） |
| 09 | 讀寫分離 |
| 10 | 縮圖（GD / ImageMagick） |
| 11 | S3 驅動（需填入 S3 金鑰） |
| 12 | 進階查詢（whereGroup、group/having、WHERE NULL、多欄排序、set、toArray） |

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
