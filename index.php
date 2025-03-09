<?php

date_default_timezone_set('Asia/Taipei');

// 定義基礎路徑
define('PATH', dirname(__FILE__, 1) . DIRECTORY_SEPARATOR);
define('PATH_SAMPLE', PATH . 'Sample' . DIRECTORY_SEPARATOR);
define('PATH_STORAGE', PATH . 'Storage' . DIRECTORY_SEPARATOR);
define('PATH_TMP', PATH . 'Tmp' . DIRECTORY_SEPARATOR);
define('PATH_FONT', PATH_SAMPLE . 'Hack-Regular.ttf');
define('BASE_URL', 'http://dev.orm.ioa.tw/');

define('S3_BUCKET', '');
define('S3_REGION', 'ap-northeast-1');
define('S3_ACCESSKEY', '');
define('S3_SECRETKEY', '');

include PATH . 'Autoload.php';
include PATH . 'Model.php';
include PATH . 'Config.php';

// 顯示版本
echo \Orm\Helper::version();
echo "\n";

// 測試
include PATH . 'Test.php';
