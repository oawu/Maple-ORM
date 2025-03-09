<?php

namespace Orm\Core;

use \Orm\Model;
use \Orm\Helper;

final class Builder {
  private const _TYPE_SELECT = "SELECT";
  private const _TYPE_UPDATE = "UPDATE";
  private const _TYPE_DELETE = "DELETE";

  public static function create(?string $db, string $class, ?string $will = null): Builder { // php8 -> return static
    return new static($db, $class, $will);
  }

  private ?string $_type = null;
  private string $_class = '';
  private ?string $_will = null;
  private ?string $_order = null;
  private ?int $_limit = null;
  private ?int $_offset = null;
  private ?string $_select = null;
  private ?string $_where = null;
  private ?string $_group = null;
  private ?string $_having = null;
  private array $_values = [];
  private array $_byKeys = [];
  private array $_attrs = [];
  private array $_relation = [];
  private array $_preBuilds = [];
  private ?string $_db = null;

  private function __construct(?string $db, string $class, ?string $will = null) {
    $this->_db = $db;
    $this->_class = $class;
    $this->_setWill($will);
  }

  public function getDb(): ?string {
    return $this->_db;
  }
  public function getClass(): string {
    return $this->_class;
  }
  public function getTable(): Table {
    return Table::instance($this->getDb(), $this->getClass());
  }
  public function all(): array {
    return $this->_runSelects();
  }
  public function one(...$args): ?Model {
    return $this->where(...$args)->limit(1)->_runSelect();
  }
  public function first(...$args): ?Model {
    return $this->where(...$args)->limit(1)->_runSelect();
  }
  public function last(...$args): ?Model {
    return $this->where(...$args)->_reverseOrder()->limit(1)->_runSelect();
  }
  public function count(): ?int {
    $this->select('COUNT(*) as c')->group(null)->_setType(self::_TYPE_SELECT);

    $stmt = null;
    $error = Connection::instance($this->getDb())->runQuery($this->getSql(), $this->_getValues(), $stmt);
    if ($error instanceof \Exception) {
      Model::executeLog('查詢資料庫錯誤，錯誤原因：' . $error->getMessage());
      return null;
    }

    if (!$objs = array_map(fn($row) => $row, $stmt->fetchAll())) {
      return 0;
    }

    $obj = array_shift($objs);
    if (!$obj) {
      return 0;
    }

    $count = $obj['c'] ?? 0;
    return (int) $count;
  }
  public function getSql(): string {
    if ($this->_type == self::_TYPE_SELECT) {
      return $this->_getSelectSql();
    }
    if ($this->_type == self::_TYPE_UPDATE) {
      return $this->_getUpdateSQL();
    }
    if ($this->_type == self::_TYPE_DELETE) {
      return $this->_getDeleteSQL();
    }

    return ';';
  }
  public function select(string ...$_args): self {
    $args = [];
    foreach ($_args as $arg) {
      $tokens = explode(',', $arg);
      foreach ($tokens as $token) {
        $token = trim($token);
        if ($token !== '') {
          $args[] = $token;
        }
      }
    }

    $args = implode(', ', $args);

    return $args === '' || $args === '*'
      ? $this->_setSelect($this->getTable()->getName(true) . '.*')
      : $this->_setSelect($args);
  }
  public function order(string ...$_args): self {
    $args = [];
    foreach ($_args as $arg) {
      $tokens = explode(',', $arg);
      foreach ($tokens as $token) {
        $token = trim($token);
        if ($token !== '') {
          $args[] = $token;
        }
      }
    }

    $args = implode(', ', $args);
    return $this->_setOrder($args !== '' ? $args : null);
  }
  public function having(?string $having): self {
    $this->_having = $having;
    return $this;
  }
  public function group(?string $group): self {
    $this->_group = $group;
    return $this;
  }
  public function byKey(string ...$keys): self {
    $this->_byKeys = $keys;
    return $this;
  }
  public function limit(?int $limit): self {
    if ($limit >= 0) {
      $this->_limit = $limit;
    } else {
      $this->_limit = null;
    }
    return $this;
  }
  public function offset(?int $offset): self {
    if ($offset >= 0) {
      $this->_offset = $offset;
    } else {
      $this->_offset = null;
    }
    return $this;
  }
  public function update($attrs = []): ?int {
    $this->_setType(self::_TYPE_UPDATE);

    $table = $this->getTable();
    $columns = $table->getColumns();

    $class = $this->getClass();
    $plugins = $class::getPlugins();
    $tableName = $table->getName(true);

    $defaults = [];
    try {
      $defaults = $table->getDefaultColumns($attrs, false);
    } catch (\Exception $e) {
      Model::executeLog($e->getMessage());
      return null;
    }

    $sets = [];
    try {
      foreach ($defaults as $name => $value) {
        $column = $columns[$name] ?? null;
        if ($column) {
          $sets[$name] = $column->initWith(null, $value, $plugins[$name] ?? null);

          if ($sets[$name] instanceof Plugin) {
            $sets[$name] = $sets[$name]->getValue();
          }
        }
      }
    } catch (\Exception $e) {
      Model::executeLog($e->getMessage());
      return null;
    }

    $createAtKey = $class::$createAt;

    if (array_key_exists($createAtKey, $sets)) {
      unset($sets[$createAtKey]);
    }

    $attrs = [];
    $vals = [];

    foreach ($sets as $key => $val) {
      array_push($attrs, $tableName . '.' . Helper::quoteName($key) . ' = ?');
      $column = $columns[$key] ?? null;
      if ($column) {
        array_push($vals, Helper::attrsToStrings($column->getType(), $val));
      }
    }

    if (!$attrs) {
      return 0;
    }

    $_vals = $this->_getValues();
    return $this->_setAttrs($attrs)->_setValues([...$vals, ...$_vals])->_execute();
  }
  public function delete(): ?int {
    return $this->_setType(self::_TYPE_DELETE)->_execute();
  }
  public function where(...$args): self {
    $where = Helper::where($this->getTable()->getName(true), ...$args);
    if ($where === null) {
      return $this;
    }

    ['str' => $str, 'vals' => $vals] = $where;

    $where = $this->_getWhere();
    if ($where !== null) {
      $this->_setWhere('(' . $where . ') AND (' . $str . ')');
    } else {
      $this->_setWhere($str);
    }

    $_vals = $this->_getValues();
    return $this->_setValues([...$_vals, ...$vals]);
  }
  public function whereIn(string $key, array $vals): self {
    return $this->where($key, 'IN', $vals);
  }
  public function whereNotIn(string $key, array $vals): self {
    return $this->where($key, 'NOT IN', $vals);
  }
  public function whereBetween(string $key, $val1, $val2): self {
    return $this->where($key, 'BETWEEN', [$val1, $val2]);
  }
  public function orWhere(...$args): self {
    $where = Helper::where($this->getTable()->getName(true), ...$args);
    if ($where === null) {
      return $this;
    }

    ['str' => $str, 'vals' => $vals] = $where;

    $where = $this->_getWhere();
    if ($where !== null) {
      $this->_setWhere('(' . $where . ') OR (' . $str . ')');
    } else {
      $this->_setWhere($str);
    }

    $_vals = $this->_getValues();
    return $this->_setValues([...$_vals, ...$vals]);
  }
  public function or(...$args): self {
    return $this->orWhere(...$args);
  }
  public function orWhereIn(string $key, array $vals): self {
    return $this->orWhere($key, 'IN', $vals);
  }
  public function orIn(string $key, array $vals): self {
    return $this->orWhereIn($key, $vals);
  }
  public function orWhereNotIn(string $key, array $vals): self {
    return $this->orWhere($key, 'NOT IN', $vals);
  }
  public function orNotIn(string $key, array $vals): self {
    return $this->orWhereNotIn($key, $vals);
  }
  public function orWhereBetween(string $key, $val1, $val2): self {
    return $this->orWhere($key, 'BETWEEN', [$val1, $val2]);
  }
  public function orBetween(string $key, $val1, $val2): self {
    return $this->orWhereBetween($key, $val1, $val2);
  }
  public function has(string $fk, string $pk, ?int $val): self {
    return $this->where($fk, $val)->_setRelation(['key1' => $fk, 'key2' => $pk, 'val' => $val]);
  }
  public function belongs(string $pk, string $fk, ?int $val): self {
    return $this->where($pk, $val)->_setRelation(['key1' => $pk, 'key2' => $fk, 'val' => $val]);
  }
  public function runWill() {
    $will = $this->_getWill();
    return $this->$will();
  }
  public function relation(string ...$args): self {
    $preBuilds = [];
    foreach ($args as $arg) {
      foreach (explode(',', $arg) as $a) {
        $a = trim($a);
        if ($a !== '') {
          $preBuilds[] = $a;
        }
      }
    }

    return $this->_setPreBuilds($preBuilds);
  }

  private function _setType(string $type) {
    $this->_type = $type;
    return $this;
  }
  private function _getSelectSql(): string {
    $tableName = $this->getTable()->getName(true);

    $strs = ['SELECT'];

    $select = $this->_getSelect();
    $where = $this->_getWhere();
    $group = $this->_getGroup();
    $having = $this->_getHaving();
    $order = $this->_getOrder();
    $limit = $this->_getLimit();
    $offset = $this->_getOffset();

    if ($select !== null) {
      $strs[] = $select;
    } else {
      $strs[] = $tableName . '.*';
    }

    $strs[] = 'FROM';
    $strs[] = $tableName;

    if ($where !== null) {
      $strs[] = 'WHERE';
      $strs[] = $where;
    }
    if ($group !== null) {
      $strs[] = 'GROUP BY';
      $strs[] = $group;
    }
    if ($having !== null) {
      $strs[] = 'HAVING';
      $strs[] = $having;
    }
    if ($order !== null) {
      $strs[] = 'ORDER BY';
      $strs[] = $order;
    }
    if ($limit !== null) {
      $strs[] = 'LIMIT';
      if ($offset !== null) {
        $strs[] = $offset;
        $strs[] = ',';
        $strs[] = $limit;
      } else {
        $strs[] = $limit;
      }
    }

    return implode(' ', $strs) . ';';
  }
  private function _getUpdateSQL(): string {
    $tableName = $this->getTable()->getName(true);

    $attrs = $this->_getAttrs();

    $strs = ['UPDATE', $tableName, 'SET', implode(', ', $attrs)];

    $where = $this->_getWhere();
    $order = $this->_getOrder();
    $limit = $this->_getLimit();
    $offset = $this->_getOffset();

    if ($where !== null) {
      $strs[] = 'WHERE';
      $strs[] = $where;
    }
    if ($order !== null) {
      $strs[] = 'ORDER BY';
      $strs[] = $order;
    }
    if ($limit !== null) {
      $strs[] = 'LIMIT';
      if ($offset !== null) {
        $strs[] = $offset;
        $strs[] = ',';
        $strs[] = $limit;
      } else {
        $strs[] = $limit;
      }
    }
    return implode(' ', array_filter($strs)) . ';';
  }
  private function _getDeleteSQL(): string {
    $tableName = $this->getTable()->getName(true);
    $strs = ['DELETE', 'FROM', $tableName];

    $where = $this->_getWhere();
    $order = $this->_getOrder();
    $limit = $this->_getLimit();
    $offset = $this->_getOffset();

    if ($where !== null) {
      $strs[] = 'WHERE';
      $strs[] = $where;
    }
    if ($order !== null) {
      $strs[] = 'ORDER BY';
      $strs[] = $order;
    }

    if ($limit !== null) {
      $strs[] = 'LIMIT';
      if ($offset !== null) {
        $strs[] = $offset;
        $strs[] = ',';
        $strs[] = $limit;
      } else {
        $strs[] = $limit;
      }
    }
    return implode(' ', array_filter($strs)) . ';';
  }
  private function _runSelect(): ?Model { // php8 -> return null|object
    $this->_setType(self::_TYPE_SELECT);
    $db = $this->getDb();

    $stmt = null;
    $error = Connection::instance($db)->runQuery($this->getSql(), $this->_getValues(), $stmt);
    if ($error instanceof \Exception) {
      Model::executeLog('查詢資料庫錯誤，錯誤原因：' . $error->getMessage());
      return null;
    }

    $objs = [];
    try {
      $class = $this->getClass();
      $objs = array_map(fn($row): Model => new $class($row, false, $db), $stmt->fetchAll());
    } catch (\Exception $e) {
      Model::executeLog('實體 Model 時發生錯誤，錯誤原因：' . $e);
      $objs = [];
    }

    return array_shift($objs);
  }
  private function _runSelects(): array {
    $this->_setType(self::_TYPE_SELECT);
    $db = $this->getDb();
    $stmt = null;
    $error = Connection::instance($db)->runQuery($this->getSql(), $this->_getValues(), $stmt);
    if ($error instanceof \Exception) {
      Model::executeLog('查詢資料庫錯誤，錯誤原因：' . $error->getMessage());
      return [];
    }

    $objs = [];
    try {
      $class = $this->getClass();
      $objs = array_map(fn($row): Model => new $class($row, false, $db), $stmt->fetchAll());
    } catch (\Exception $e) {
      Model::executeLog('實體 Model 時發生錯誤，錯誤原因：' . $e);
      $objs = [];
    }

    if (!$objs) {
      return [];
    }

    $preBuilds = $this->_getPreBuilds();
    foreach ($preBuilds as $relation) {
      $builders = array_filter(array_map(fn(Model $obj): ?Builder => method_exists($obj, $relation) ? $obj->$relation() : null, $objs), fn($obj) => $obj !== null);

      $vals = array_filter(array_map(fn($builders) => $builders->_getRelation()['val'] ?? null, $builders), fn($obj) => $obj !== null);

      if (!$vals) {
        continue;
      }

      if (!$builder = array_shift($builders)) {
        continue;
      }

      if (!$where = $builder->_getRelation()) {
        continue;
      }

      $will = $builder->_getWill();
      $key1 = $where['key1'];
      $key2 = $where['key2'];

      $relations = $builder->_setWhere(null)->_setValues([])->whereIn($key1, $vals)->byKey($key1)->all();

      foreach ($objs as $obj) {
        if (method_exists($obj, $relation) && isset($obj->$key2)) {
          $_tmps = $relations[$obj->$key2] ?? [];
          if ($will == 'all') {
            $obj->$relation = $_tmps;
          } else {
            $obj->$relation = $_tmps[0] ?? null;
          }
        }
      }
    }

    $byKeys = $this->_getByKeys();
    if (!$byKeys) {
      return $objs;
    }

    $groups = [];
    foreach ($objs as $obj) {

      $key = '';

      foreach ($byKeys as $_key) {
        if (isset($obj->$_key)) {
          $key .= $obj->$_key;
        }
      }

      if (!isset($groups[$key])) {
        $groups[$key] = [];
      }

      $groups[$key][] = $obj;
    }

    return $groups;
  }
  private function _reverseOrder(): self {
    $order = $this->_getOrder();

    if ($order !== null) {
      $tmps = [];
      $parts = explode(',', $order);
      foreach ($parts as $part) {
        $v = trim(strtolower($part));

        if (strpos($v, ' asc') !== false) {
          $tmps[] = preg_replace('/asc/i', 'DESC', $v);
        } else if (strpos($v, ' desc') !== false) {
          $tmps[] = preg_replace('/desc/i', 'ASC', $v);
        } else {
          $tmps[] = $v . ' DESC';
        }
      }

      return $this->_setOrder(implode(', ', $tmps));
    }

    $primaries = $this->getTable()->getPrimaries();
    return $primaries
      ? $this->_setOrder(implode(' DESC, ', $primaries) . ' DESC')
      : $this->_setOrder('id DESC');
  }
  private function _execute(): ?int {
    $stmt = null;
    $error = Connection::instance($this->getDb())->runQuery($this->getSql(), $this->_getValues(), $stmt);
    if ($error instanceof \Exception) {
      Model::executeLog('資料庫執行語法錯誤，錯誤原因：' . $error->getMessage());
      return null;
    }
    return $stmt->rowCount();
  }
  private function _setRelation(array $relation): self {
    $this->_relation = $relation;
    return $this;
  }
  private function _getRelation(): array {
    return $this->_relation;
  }
  private function _setPreBuilds(array $preBuilds): self {
    $this->_preBuilds = $preBuilds;
    return $this;
  }
  private function _getPreBuilds(): array {
    return $this->_preBuilds;
  }
  private function _setWill(?string $will): self {
    if ($will === null) {
      $this->_will = null;
    } else if (in_array($will, ['all', 'one', 'last', 'first'])) {
      $this->_will = $will;
    } else {
      $this->_will = 'all';
    }
    return $this;
  }
  private function _getWill(): string {
    return $this->_will ?? 'all';
  }
  private function _setOrder(?string $order): self {
    $this->_order = $order;
    return $this;
  }
  private function _getOrder(): ?string {
    return $this->_order;
  }
  private function _getLimit(): ?int {
    return $this->_limit;
  }
  private function _getOffset(): ?int {
    return $this->_offset;
  }
  private function _getSelect(): ?string {
    return $this->_select;
  }
  private function _setSelect(?string $select): self {
    $this->_select = $select;
    return $this;
  }
  private function _getWhere(): ?string {
    return $this->_where;
  }
  private function _getValues(): array {
    return $this->_values;
  }
  private function _setValues(array $values): self {
    $this->_values = $values;
    return $this;
  }
  private function _setWhere(?string $where): self {
    $this->_where = $where;
    return $this;
  }
  private function _getGroup(): ?string {
    return $this->_group;
  }
  private function _getHaving(): ?string {
    return $this->_having;
  }
  private function _getByKeys(): array {
    return $this->_byKeys;
  }
  private function _setAttrs(array $attrs): self {
    $this->_attrs = $attrs;
    return $this;
  }
  private function _getAttrs(): array {
    return $this->_attrs;
  }
}
