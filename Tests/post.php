<?php

date_default_timezone_set('Asia/Taipei');

// 定義基礎路徑
define('PATH', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);
define('PATH_MODEL',  PATH . 'Model' . DIRECTORY_SEPARATOR);
define('PATH_SAMPLE', PATH . 'Sample' . DIRECTORY_SEPARATOR);

// 載入 Model
include PATH . 'Model.php';

// 設定 Model Config
include PATH . 'Config.php';

// 載入 縮圖軟體
include PATH . 'Thumbnail.php';

$user = \M\User::one(1);

$result = $user->avatar->put($_FILES['avatar']);


echo json_encode([
  'result' => $result,
  'url' => $user->avatar->url()
]);
