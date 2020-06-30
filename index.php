<?php

date_default_timezone_set('Asia/Taipei');

// 定義基礎路徑
define('PATH', dirname(__FILE__, 1) . DIRECTORY_SEPARATOR);
define('PATH_MODEL',  PATH . 'Model' . DIRECTORY_SEPARATOR);
define('PATH_SAMPLE', PATH . 'Sample' . DIRECTORY_SEPARATOR);

// 載入 Model
include PATH . 'Model.php';

// 設定 Model Config
include PATH . 'Config.php';

// 載入 縮圖軟體
include PATH . 'Thumbnail.php';

// 測試 應用
include PATH . 'Tests/0-methods-where.php';

// 測試 關聯
include PATH . 'Tests/1-relation1.php';

// 測試 底線命名 關聯
include PATH . 'Tests/2-relation2.php';

// 測試 pre-relation
include PATH . 'Tests/3-pre-relation.php';

// 測試 uploader
include PATH . 'Tests/4-uploader.php';

// 測試 transaction
include PATH . 'Tests/5-transaction.php';

// // 測試 文件
// include PATH . 'Tests/6-guide-crud.php';

// // 測試 文件
// include PATH . 'Tests/7-guide-relation.php';

// // 測試 文件
// include PATH . 'Tests/8-guide-pre-relation.php';
