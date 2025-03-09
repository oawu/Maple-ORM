<?php

date_default_timezone_set('Asia/Taipei');

// 定義基礎路徑
define('PATH', dirname(__FILE__, 1) . DIRECTORY_SEPARATOR);
define('PATH_MODEL',  PATH . 'Model' . DIRECTORY_SEPARATOR);
define('PATH_SAMPLE', PATH . 'Sample' . DIRECTORY_SEPARATOR);
define('PATH_STORAGE', PATH . 'Storage' . DIRECTORY_SEPARATOR);
define('PATH_TMP', PATH . 'Tmp' . DIRECTORY_SEPARATOR);
define('PATH_FONT', PATH . 'Hack-Regular.ttf');
define('BASE_URL', 'http://dev.orm.ioa.tw/');

define('S3_BUCKET', '');
define('S3_REGION', 'ap-northeast-1');
define('S3_ACCESSKEY', '');
define('S3_SECRETKEY', '');

// 載入 Model
include PATH . 'Model.php';

// 設定 Model Config
include PATH . 'Config.php';

// 測試
include PATH . 'Test.php';
