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
include PATH . 'Tests/0.php';

// 測試 關聯
include PATH . 'Tests/1.php';

// 測試 底線命名 關聯
include PATH . 'Tests/2.php';

// 測試 merge
include PATH . 'Tests/3.php';

// 測試 uploader
include PATH . 'Tests/4.php';
