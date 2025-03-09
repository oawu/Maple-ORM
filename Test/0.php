<?php

$stmt = null;

foreach (\M\Core\Connection::getDbs() as $db) {
  if (\M\Core\Connection::instance($db)->runQuery('show tables;', [], $stmt)) {
    var_dump('show tables Error');
    exit(1);
  }

  $tables = [];
  foreach ($stmt->fetchAll() as $row) {
    array_push($tables, array_shift($row));
  }

  foreach ($tables as $table) {
    if (\M\Core\Connection::instance($db)->runQuery('DROP TABLE IF EXISTS `' . $table . '`;')) {
      var_dump('drop table `' . $table . '` Error');
      exit(1);
    }
  }
}

@exec('rm -rf ' . PATH_TMP . '*');
@exec('rm -rf ' . PATH_STORAGE . '*');

\M\Model::setCaseTable(\M\Model::CASE_CAMEL);
\M\Model::setCaseColumn(\M\Model::CASE_CAMEL);
