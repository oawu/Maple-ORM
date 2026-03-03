<?php

$_passed = 0;
$_total  = 12;

function test(string $number, string $name, string $file) {
  global $_passed;
  echo "\n  {$number}. {$name}";
  include PATH . $file;
  finish();
  $_passed++;
}

echo '開始測試' . "\n";

test('01', 'Create',         'Test/1-create.php');
test('02', 'Select',         'Test/2-select.php');
test('03', 'Update',         'Test/3-update.php');
test('04', 'Delete',         'Test/4-delete.php');
test('05', 'Relation',       'Test/5-relation.php');
test('06', 'Transaction',    'Test/6-transaction.php');
test('07', 'Put Image',      'Test/7-put-image.php');
test('08', 'Put File',       'Test/8-put-file.php');
test('09', 'DB Read Write',  'Test/9-db-read-write.php');
test('10', 'Thumbnail',      'Test/10-Thumbnail.php');
test('11', 'S3 Driver',      'Test/11-driver-s3.php');
test('12', 'Advanced Query', 'Test/12-advanced-query.php');

@exec('rm -rf ' . PATH_TMP . '*');
@exec('rm -rf ' . PATH_STORAGE . '*');

echo "\n\n" . '完成測試（' . $_passed . '/' . $_total . ' 通過）' . "\n";
