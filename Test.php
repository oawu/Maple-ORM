<?php

// 測試
echo '開始測試' . "\n";

echo '  01. 測試 Create'; include PATH . 'Test/1-create.php'; echo ' - ok' . "\n";
echo '  02. 測試 Select'; include PATH . 'Test/2-select.php'; echo ' - ok' . "\n";
echo '  03. 測試 Update'; include PATH . 'Test/3-update.php'; echo ' - ok' . "\n";
echo '  04. 測試 Delete'; include PATH . 'Test/4-delete.php'; echo ' - ok' . "\n";
echo '  05. 測試 Relation'; include PATH . 'Test/5-relation.php'; echo ' - ok' . "\n";
echo '  06. 測試 Transaction'; include PATH . 'Test/6-transaction.php'; echo ' - ok' . "\n";
echo '  07. 測試 Put Image'; include PATH . 'Test/7-put-image.php'; echo ' - ok' . "\n";
echo '  08. 測試 Put File'; include PATH . 'Test/8-put-file.php'; echo ' - ok' . "\n";
echo '  09. 測試 DB Read Write'; include PATH . 'Test/9-db-read-write.php'; echo ' - ok' . "\n";
echo '  10. 測試 Thumbnail'; include PATH . 'Test/10-Thumbnail.php'; echo ' - ok' . "\n";
echo '  11. 測試 S3 Driver'; include PATH . 'Test/11-driver-s3.php'; echo ' - ok' . "\n";

@exec('rm -rf ' . PATH_TMP . '*');
@exec('rm -rf ' . PATH_STORAGE . '*');

echo '完成測試' . "\n";
