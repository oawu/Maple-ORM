<?php

if (\M\Core\Connection::instance()->query('show tables;', [], $sth)) {
  var_dump('show tables Error');
  exit(1);
}

$tables = [];
foreach ($sth->fetchAll() as $row)
  array_push($tables, array_shift($row));

foreach ($tables as $table) {
  if (\M\Core\Connection::instance()->query('DROP TABLE IF EXISTS `' . $table . '`;')) {
    var_dump('drop table `' . $table . '` Error');
    exit(1);
  }
}
