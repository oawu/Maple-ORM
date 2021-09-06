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

// Model::
//   [where, whereIn, whereNotIn, whereBetween, select, order, group, having, limit, offset, keyBy, relation]
//   [where, whereIn, whereNotIn, whereBetween, select, order, group, having, limit, offset, keyBy, relation, orWhere, orWhereIn, orWhereNotIn, orWhereBetween]*
//   [one, first, last, all, count, update, delete]()

// Model::
//   [create, creates, truncate, one, first, last, all, count, update, delete]()

// include PATH . 'Test/1-create.php';
// include PATH . 'Test/2-select.php';
// include PATH . 'Test/3-update.php';
// include PATH . 'Test/4-delete.php';
include PATH . 'Test/5-relation.php';
// include PATH . 'Test/6-transaction.php';

// include PATH . 'Test/7-put-image.php';
// include PATH . 'Test/8-put-file.php';
