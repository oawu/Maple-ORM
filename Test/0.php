<?php

use \Orm\Core\Connection;
use \Orm\Model;

$stmt = null;

foreach (Connection::getDbs() as $db) {
  if (Connection::instance($db)->runQuery('show tables;', [], $stmt)) {
    var_dump('show tables Error');
    exit(1);
  }

  $tables = [];
  foreach ($stmt->fetchAll() as $row) {
    array_push($tables, array_shift($row));
  }

  foreach ($tables as $table) {
    if (Connection::instance($db)->runQuery('DROP TABLE IF EXISTS `' . $table . '`;')) {
      var_dump('drop table `' . $table . '` Error');
      exit(1);
    }
  }
}

@exec('rm -rf ' . PATH_TMP . '*');
@exec('rm -rf ' . PATH_STORAGE . '*');

Model::setCaseTable(Model::CASE_CAMEL);
Model::setCaseColumn(Model::CASE_CAMEL);
