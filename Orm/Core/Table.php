<?php

namespace Orm\Core;

use \Orm\Model;
use \Orm\Helper;
use \Orm\Core\Plugin\DateTime;

final class Table {
  private static array $_instances = [];

  public static function instance(?string $db, string $class): Table {
    $key = $db !== null ? ($db . '.' . $class) : $class;

    if (isset(self::$_instances[$key])) {
      return self::$_instances[$key];
    }

    $table = new Table($db, $class);
    self::$_instances[$key] = $table;
    return $table;
  }

  private ?string $_db = null;
  private string $_class = '';
  private array $_columns = [];
  private array $_primaries = [];
  private ?array $_cacheName = null;

  private function __construct(?string $db, string $class) {
    $this->_db = $db;
    $this->_class = $class;
    $this->_setMetaData()->_setPrimaries();
  }

  public function getPrimaries(): array {
    return $this->_primaries;
  }
  public function getName(bool $isQuoteName): string {
    if ($this->_cacheName !== null) {
      return $this->_cacheName[$isQuoteName ? 1 : 0];
    }

    $class = $this->_class;
    $tableName = $class::$tableName ?? null;

    if (!(is_string($tableName) && $tableName !== '')) {
      $parts = $this->_classToTableName(Model::getBaseNamespaces(), $class);

      if (Model::getCaseTable() == Model::CASE_SNAKE) {
        $tableName = '';
        foreach ($parts as $part) {
          $part = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $part));
          $tableName = ($tableName !== '' ? ($tableName . '_') : '') . $part;
        }
      } else {
        $tableName = implode('', $parts);
      }
    }

    $this->_cacheName = [
      0 => $tableName,
      1 => Helper::quoteName($tableName),
    ];
    return $this->_cacheName[$isQuoteName ? 1 : 0];
  }
  public function getDefaultColumns(array $attrs, bool $full): array {
    $columns = $this->getColumns();
    $attrs = array_intersect_key($attrs, $columns);

    $class = $this->getClass();

    $updateAtKey = $class::$updateAt;
    $createAtKey = $class::$createAt;

    $updateAt = $columns[$updateAtKey] ?? null;
    $createAt = $columns[$createAtKey] ?? null;

    if ($updateAt && !array_key_exists($updateAtKey, $attrs)) {
      $attrs[$updateAtKey] = \date(DateTime::formatByType($updateAt->getType()));
    }
    if ($createAt && !array_key_exists($createAtKey, $attrs)) {
      $attrs[$createAtKey] = \date(DateTime::formatByType($createAt->getType()));
    }

    $_attrs = [];
    foreach ($columns as $key => $column) {
      if (!($full || array_key_exists($key, $attrs))) {
        continue;
      }
      if (!array_key_exists($key, $attrs)) {
        $attrs[$key] = $column->getDefaultValue();
      }
      if (!($column->getIsNullable() || isset($attrs[$key]) || $column->getIsAutoIncrement())) {
        throw new \Exception('欄位「' . $key . '」不可以為 NULL');
      }

      if ($column->getType() != 'json') {
        $_attrs[$key] = $attrs[$key] ?? null;
      } else {
        $_attrs[$key] = @json_encode($attrs[$key]);
      }
    }

    return $_attrs;
  }
  public function getColumns(): array {
    return $this->_columns;
  }
  public function getClass(): string {
    return $this->_class;
  }

  private function _setMetaData(): self {
    $db = $this->_getDb();
    $name = $this->getName(false);

    $this->_columns = Model::executeCache('MetaData', $db !== null ? ($db . '.' . $name) : $name, static function () use ($db, $name): array {
      $stmt = null;


      $error = Connection::instance($db)->runQuery("SHOW COLUMNS FROM " . Helper::quoteName($name) . ';', [], $stmt);
      if ($error instanceof \Exception) {
        Model::executeError('取得「' . $name . '」Table 的 Meta Data 失敗，錯誤原因：' . $error->getMessage());
        return [];
      }

      $columns = [];
      foreach ($stmt->fetchAll() as $row) {
        $column = Column::create($row);
        if ($column !== null) {
          $columns[$column->getName()] = $column;
        }
      }
      return $columns;
    });

    return $this;
  }
  private function _setPrimaries(): self {
    $class = $this->_class;
    $primaries = $class::$primaries ?? null;

    if ($primaries === null) {
      $primaries = array_values(array_filter(array_map(fn(Column $c): ?string => $c->getIsPrimary() ? $c->getName() : null, $this->_columns), fn($c) => $c !== null));
    }

    if (is_string($primaries)) {
      $primaries = [$primaries];
    }

    if (is_array($primaries)) {
      $primaries = array_filter($primaries, 'is_string');
    }

    $this->_primaries = $primaries;
    return $this;
  }
  private function _getDb(): ?string {
    return $this->_db;
  }
  private function _classToTableName(array $bases, string $class): array {
    $classes = Helper::explode($class, '\\', ['\\']);
    $c1 = count($bases);
    $c2 = count($classes);

    if ($c1 >= $c2) {
      return $classes;
    }

    $names = [];
    $same = true;
    for ($i = 0; $i < $c2; $i++) {
      if ($same && $i < $c1 && $bases[$i] == $classes[$i]) {
        continue;
      }
      $same = false;
      $names[] = $classes[$i];
    }

    return $names;
  }
}
