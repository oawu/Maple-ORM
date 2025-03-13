<?php

namespace Orm;

use \Orm\Helper;
use \Orm\Core\Builder;
use \Orm\Core\Config;
use \Orm\Core\Connection;
use \Orm\Core\Table;
use \Orm\Core\Hashids;
use \Orm\Core\Inflect;

use \Orm\Core\Plugin;
use \Orm\Core\Plugin\Uploader;
use \Orm\Core\Plugin\DateTime;
use \Orm\Core\Plugin\Uploader\File;
use \Orm\Core\Plugin\Uploader\Image;

abstract class Model {
  public const PRIMARY_ID = 'id';
  public const CASE_CAMEL = 'Camel';
  public const CASE_SNAKE = 'Snake';

  public static string $createAt = 'createAt';
  public static string $updateAt = 'updateAt';

  private static array $_baseNameSpaces = [];
  private static $_errorFunc = null; // php8 -> ?callable
  private static $_queryLogFunc = null; // php8 -> ?callable
  private static $_logFunc = null; // php8 -> ?callable
  private static array $_cacheFuncs = [];

  private static ?string $_lastLog = null;
  private static ?array $_lastQuery = null;
  private static string $_caseTable = Model::CASE_CAMEL;
  private static string $_caseColumn = Model::CASE_CAMEL;
  private static ?Hashids $_hashids = null;
  private static $_binds = [];

  public static function setErrorFunc(?callable $func = null): void {
    self::$_errorFunc = $func;
  }
  public static function executeError(...$args): void {
    $func = self::$_errorFunc;
    if (is_callable($func)) {
      $func(...$args);
    } else {
      var_dump($args);
      exit(1);
    }
  }
  public static function setQueryLogFunc(?callable $func = null): void {
    self::$_queryLogFunc = $func;
  }
  public static function getLastQueryLog(): ?array {
    return self::$_lastQuery;
  }
  public static function executeQueryLog(string $db, string $sql, array $vals, bool $status, float $during, bool $log): void {
    self::$_lastQuery = [
      'db' => $db,
      'sql' => $sql,
      'vals' => $vals,
      'status' => $status,
      'during' => $during,
      'log' => $log
    ];

    $func = self::$_queryLogFunc;
    if (is_callable($func)) {
      $func($db, $sql, $vals, $status, $during, $log);
    } else {
      var_dump($db, $sql, $vals, $status, $during, $log);
    }
  }
  public static function setLogFunc(?callable $func = null): void {
    self::$_logFunc = $func;
  }
  public static function getLastLog(): ?string {
    return self::$_lastLog;
  }
  public static function executeLog(string $message): void {
    self::$_lastLog = $message;

    $func = self::$_logFunc;
    if (is_callable($func)) {
      $func($message);
    } else {
      // var_dump($message);
    }
  }
  public static function setCacheFunc(string $type, callable $func): void {
    self::$_cacheFuncs[$type] = $func;
  }
  public static function executeCache(string $type, string $key, callable $closure) { // php8 -> return mixed
    if (array_key_exists($type, self::$_cacheFuncs)) {
      $func = self::$_cacheFuncs[$type];
      if (is_callable($func)) {
        return $func($key, $closure);
      }
    }
    return $closure();
  }
  public static function setNamespace(string ...$names): void {
    $_names = [];

    foreach ($names as $name) {
      $dirs = Helper::explode($name, '\\', ['\\']);
      foreach ($dirs as $dir) {
        $_names[] = $dir;
      }
    }

    self::$_baseNameSpaces = $_names;
  }
  public static function getBaseNamespaces(): array {
    return self::$_baseNameSpaces;
  }
  public static function setCaseTable(string $caseTable): void {
    if (in_array($caseTable, [Model::CASE_CAMEL, Model::CASE_SNAKE])) {
      self::$_caseTable = $caseTable;
    }
  }
  public static function getCaseTable(): string {
    return self::$_caseTable;
  }
  public static function setCaseColumn(string $caseColumn): void {
    if (in_array($caseColumn, [Model::CASE_CAMEL, Model::CASE_SNAKE])) {
      self::$_caseColumn = $caseColumn;
    }
  }
  public static function getCaseColumn(): string {
    return self::$_caseColumn;
  }
  public static function setHashids(int $minLength = 0, string $salt = '', ?string $alphabet = null): void {
    if ($alphabet === null) {
      $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    }
    self::$_hashids = new Hashids($minLength, $salt, $alphabet);
  }
  public static function getHashids(): ?Hashids {
    return self::$_hashids;
  }
  public static function setConfig(string $db, Config $config): Config {
    Connection::setConfig($db, $config);
    return $config;
  }
  public static function getTable(?string $db): Table {
    return Table::instance($db, static::class);
  }
  public static function setImageThumbnail(callable $func): void {
    Image::setThumbnail($func);
  }
  public static function setUploader(callable $func): void {
    Uploader::func($func);
  }
  public static function bindFile(string $column, ?callable $func = null): void {
    static::$_binds[$column] = [
      'class' => File::class,
      'func' => $func,
    ];
  }
  public static function bindImage(string $column, ?callable $func = null): void {
    static::$_binds[$column] = [
      'class' => Image::class,
      'func' => $func,
    ];
  }
  public static function getPlugins(): array {
    return static::$_binds;
  }
  public static function db(?string $db = null) {
    return Builder::create($db, static::class);
  }
  public static function create(array $attrs = [], array $allow = [], ?string $db = null): ?self { // php8 -> return ?static
    if ($allow) { // 只允許指定的欄位
      $attrs = array_intersect_key($attrs, array_flip($allow));
    }

    $class = static::class;

    $model = null;
    try {
      $attrs = static::getTable($db)->getDefaultColumns($attrs, true);
      $model = new $class($attrs, true);
    } catch (\Exception $e) {
      Model::executeLog('實體 Model 時發生錯誤，錯誤原因：' . $e->getMessage());
      $model = null;
    }

    return $model;
  }
  public static function truncate(?string $db = null): bool {
    $tableName = static::getTable($db)->getName(true);

    $stmt = null;
    $sql = 'TRUNCATE TABLE ' . $tableName . ';';
    $error = Connection::instance($db)->runQuery($sql, [], $stmt);
    if ($error instanceof \Exception) {
      Model::executeLog('資料庫執行語法錯誤，錯誤原因：' . $error->getMessage());
      return false;
    }

    return true;
  }
  public static function creates(array $_rows = [], int $limit = 50, ?string $db = null): ?int {
    $table = static::getTable($db);
    $columns = $table->getColumns();
    $plugins = static::getPlugins();
    $tableName = $table->getName(true);
    $rows = [];

    try {
      foreach ($_rows as $row) {
        $tmps = [];
        $attrs = $table->getDefaultColumns($row, true);
        foreach ($attrs as $name => $value) {
          $column = $columns[$name] ?? null;

          if ($column) {
            $tmps[$name] = $column->initWith(null, $value, $plugins[$name] ?? null);
            if ($tmps[$name] instanceof Plugin) {
              $tmps[$name] = $tmps[$name]->getValue();
            }
          }
        }
        $rows[] = $tmps;
      }
    } catch (\Exception $error) {
      Model::executeLog('新增資料錯誤，錯誤原因：' . $error->getMessage());
      return null;
    }

    if (!$rows) {
      return 0;
    }

    $j = 0;
    $page = [
      'pits' => [],
      'vals' => []
    ];
    $pages = [];
    $cols = array_flip(array_keys($rows[0]));
    $len  = count($cols);

    foreach ($rows as $i => $row) {
      if ($len != count(array_intersect_key($row, $cols))) {
        Model::executeLog('結構錯誤，第 ' . ($i + 1) . '筆資料 key 結構與第一筆不同');
        return null;
      }

      if (count($page['pits']) > $limit) {
        $pages[] = $page;
        $page = ['pits' => [], 'vals' => []];
      }

      $page['pits'][] = '(' . Helper::whereQuestion($len) . ')';

      $tmps = [];
      foreach ($row as $key => $val) {
        $column = $columns[$key] ?? null;
        if ($column) {
          $tmps[] = Helper::attrsToStrings($column->getType(), $val);
        }
      }
      $page['vals'] = [...$page['vals'], ...$tmps];
    }

    if ($page['pits']) {
      $pages[] = $page;
    }

    $sum = 0;
    foreach ($pages as $page) {
      $sql = 'INSERT INTO ' . $tableName . ' (' . implode(', ', array_map(fn($key) => $tableName . '.' . Helper::quoteName($key), array_keys($rows[0]))) . ') VALUES ' . implode(', ', $page['pits']) . ';';
      $vals = $page['vals'];

      $stmt = null;
      $error = Connection::instance($db)->runQuery($sql, $vals, $stmt);
      if ($error instanceof \Exception) {
        Model::executeLog('新增資料錯誤，錯誤原因：' . $error->getMessage());
        return null;
      }

      $sum += $stmt->rowCount();
    }

    if ($sum != count($rows)) {
      Model::executeLog('新增資料錯誤，錯誤原因：影響筆數為 ' . $sum . ' 筆，但應該為 ' . count($rows) . ' 筆');
      return null;
    }

    return $sum;
  }
  public static function one(...$args): ?self { // php8 -> return ?static
    return Builder::create(null, static::class)->one(...$args);
  }
  public static function first(...$args): ?self { // php8 -> return ?static
    return Builder::create(null, static::class)->first(...$args);
  }
  public static function last(...$args): ?self { // php8 -> return ?static
    return Builder::create(null, static::class)->last(...$args);
  }
  public static function all(): array {
    return Builder::create(null, static::class)->all();
  }
  public static function builder(?string $db = null): Builder {
    return Builder::create($db, static::class);
  }
  public static function select(string ...$args): Builder {
    return Builder::create(null, static::class)->select(...$args);
  }
  public static function order(string ...$args): Builder {
    return Builder::create(null, static::class)->order(...$args);
  }
  public static function byKey(string ...$keys): Builder {
    return Builder::create(null, static::class)->byKey(...$keys);
  }
  public static function limit(int $limit): Builder {
    return Builder::create(null, static::class)->limit($limit);
  }
  public static function offset(int $offset): builder {
    return Builder::create(null, static::class)->offset($offset);
  }
  public static function group(?string $group): Builder {
    return Builder::create(null, static::class)->group($group);
  }
  public static function having(?string $having): Builder {
    return Builder::create(null, static::class)->having($having);
  }
  public static function count(): ?int {
    return Builder::create(null, static::class)->count();
  }
  public static function updates(array $sets = []): ?int {
    return Builder::create(null, static::class)->update($sets);
  }
  public static function deletes(): ?int {
    return Builder::create(null, static::class)->delete();
  }
  public static function close(): bool {
    return Connection::close();
  }
  public static function where(...$args): Builder {
    return Builder::create(null, static::class)->where(...$args);
  }
  public static function whereIn(string $key, array $vals): Builder {
    return Builder::create(null, static::class)->whereIn($key, $vals);
  }
  public static function in(string $key, array $vals): Builder {
    return Builder::create(null, static::class)->whereIn($key, $vals);
  }
  public static function whereNotIn(string $key, array $vals): Builder {
    return Builder::create(null, static::class)->whereNotIn($key, $vals);
  }
  public static function notIn(string $key, array $vals): Builder {
    return Builder::create(null, static::class)->whereNotIn($key, $vals);
  }
  public static function whereBetween(string $key, $val1, $val2): Builder {
    return Builder::create(null, static::class)->whereBetween($key, $val1, $val2);
  }
  public static function between(string $key, $val1, $val2): Builder {
    return Builder::create(null, static::class)->whereBetween($key, $val1, $val2);
  }
  public function hasMany(string $class, ?string $fk = null, ?string $pk = Model::PRIMARY_ID): Builder {
    return static::_has('all', $this, $class, $fk, $pk);
  }
  public function hasOne(string $class, ?string $fk = null, ?string $pk = Model::PRIMARY_ID): Builder {
    return static::_has('one', $this, $class, $fk, $pk);
  }
  public function belongsTo(string $class, ?string $fk = null, ?string $pk = Model::PRIMARY_ID): Builder {
    return static::_belongs('one', $this, $class, $fk, $pk);
  }
  public function belongsToMany(string $class, ?string $fk = null, ?string $pk = Model::PRIMARY_ID): Builder {
    return static::_belongs('all', $this, $class, $fk, $pk);
  }
  public static function relation(string ...$args): Builder {
    return Builder::create(null, static::class)->relation(...$args);
  }

  private static function _has(string $type, Model $model, string $class, ?string $fk = null, ?string $pk = Model::PRIMARY_ID): Builder {
    $db = $model->getDb();
    $_class = get_class($model);
    $fk = $fk ?? static::_foreign($_class::getTable($db)->getName(false));
    return Builder::create($db, $class, $type)->has($fk, $pk, $model->$pk);
  }
  private static function _belongs(string $type, Model $model, string $class, ?string $fk = null, ?string $pk = Model::PRIMARY_ID): Builder {
    $db = $model->getDb();
    $fk = $fk ?? static::_foreign($class::getTable($db)->getName(false));
    return Builder::create($db, $class, $type)->belongs($pk, $fk, $model->$fk);
  }
  private static function _foreign(string $tableName): string {
    if (Model::getCaseColumn() == Model::CASE_SNAKE) {
      return Inflect::singularize($tableName) . '_' . Model::PRIMARY_ID;
    }
    return lcfirst($tableName) . ucfirst(Model::PRIMARY_ID);
  }

  private ?string $_db = null;
  private array $_attrs = [];
  private array $_dirties = [];
  private array $_relations = [];
  private array $_vars = [];
  private bool $_deleted = false;

  public function __construct($attrs = [], $isNew = true, ?string $db = null) {
    $this->_db = $db;

    $table = static::getTable($db);
    $columns = $table->getColumns();
    $plugins = static::getPlugins();
    $tableName = $table->getName(true);

    foreach ($attrs as $name => $value) {
      $colum = $columns[$name] ?? null;
      if ($colum) {
        $this->_attrs[$name] = $colum->initWith($this, $value, $plugins[$name] ?? null);
      } else {
        $this->_vars[$name] = $value;
      }
    }

    if (!$isNew) {
      return;
    }

    $cols = [];
    $pits = [];
    $vals = [];

    foreach ($this->_attrs as $key => $val) {
      $column = $columns[$key] ?? null;
      if ($column) {
        array_push($cols, Helper::quoteName($key));
        array_push($pits, '?');
        array_push($vals, Helper::attrsToStrings($column->getType(), $val));
      }
    }

    $sql = 'INSERT INTO ' . $tableName . ' (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $pits) . ');';

    $stmt = null;
    $error = Connection::instance($db)->runQuery($sql, $vals, $stmt);
    if ($error instanceof \Exception) {
      throw new \Exception('新增資料錯誤，錯誤原因：' . $error->getMessage());
      return;
    }

    if ($stmt->rowCount() != 1) {
      throw new \Exception('新增資料錯誤，錯誤原因：影響筆數不為 1 筆');
    }

    $id = (int)Connection::instance($db)->lastInsertId();

    foreach ($table->getPrimaries() as $primary) {
      $column = $columns[$primary] ?? null;
      if ($column && $column->getIsAutoIncrement()) {
        $this->_attrs[$primary] = $id;
      }
    }

    $this->_cleanFlagDirty($isNew = false);

    // afterCreates 不保證成功全跑完，失敗結束不影響新增
    $afterCreates = static::$afterCreates ?? [];
    if (!is_array($afterCreates)) {
      $afterCreates = [$afterCreates];
    }

    $return = $this;
    foreach ($afterCreates as $afterCreate) {
      if (method_exists($this, $afterCreate)) {
        try {
          $return = $this->$afterCreate($return);
        } catch (\Throwable $e) {
          Model::executeLog('Model「' . static::class . '」執行 after create「' . $afterCreate . '」失敗，錯誤原因：' . $e->getMessage());
          break;
        }
      }
    }
  }

  public function __isset(string $name): bool {
    return array_key_exists($name, $this->_attrs);
  }
  public function &__get(string $name) {
    if (array_key_exists($name, $this->_attrs)) {
      return $this->_attrs[$name];
    }

    if (array_key_exists($name, $this->_relations)) {
      return $this->_relations[$name];
    }

    if (method_exists($this, $name)) {
      $result = $this->$name();
      if ($result instanceof Builder) {
        $this->_relations[$name] = $result->runWill();
      } else {
        $this->_relations[$name] = $result;
      }
      return $this->_relations[$name];
    }

    if (!array_key_exists($name, $this->_vars)) {
      $this->_vars[$name] = null;
    }

    return $this->_vars[$name];
  }
  public function __set(string $name, $value): void {
    if (array_key_exists($name, $this->_attrs)) {
      $table = static::getTable($this->getDb());
      $column = $table->getColumns();
      $column = $column[$name] ?? null;

      if ($column) {
        $this->_attrs[$name] = $column->updateWith($this->_attrs[$name], $value);
        $this->_dirties[$name] = true;
        return;
      }
    }

    if (array_key_exists($name, $this->_relations) || method_exists($this, $name)) {
      $this->_relations[$name] = $value;
      return;
    }

    $this->_vars[$name] = $value;
  }
  public function save(?int &$count = 0): ?self { // php8 -> return static
    if ($this->_deleted) {
      return $this;
    }

    $table = static::getTable($this->getDb());
    $columns = $table->getColumns();
    $primaries = $this->_getPrimaries();
    $tableName = $table->getName(true);

    if (!$primaries) {
      Model::executeLog('更新資料失敗，錯誤原因：找不到 Primary Key');
      return null;
    }

    if (!array_intersect_key($this->_attrs, $this->_dirties)) {
      $count = 1;
      return $this;
    }

    $updateAt = $columns[static::$updateAt] ?? null;

    if ($updateAt && array_key_exists(static::$updateAt, $this->_attrs) && !array_key_exists(static::$updateAt, $this->_dirties)) {
      try {
        $this->_attrs[static::$updateAt] = $updateAt->updateWith($this->_attrs[static::$updateAt], \date(DateTime::formatByType($updateAt->getType())));
        $this->_dirties[static::$updateAt] = true;
      } catch (\Exception $e) {
        Model::executeLog('更新資料失敗，錯誤原因：' . $e->getMessage());
        return null;
      }
    }

    $sets = [];
    $vals = [];
    $where = [];

    $dirtieColums = array_intersect_key($this->_attrs, $this->_dirties);
    foreach ($dirtieColums as $key => $value) {
      $column = $columns[$key] ?? null;
      if ($column) {
        array_push($sets, $tableName . '.' . Helper::quoteName($key) . ' = ?');
        array_push($vals, Helper::attrsToStrings($column->getType(), $value));
      }
    }

    foreach ($primaries as $key => $value) {
      $column = $columns[$key] ?? null;
      if ($column) {
        array_push($where, $tableName . '.' . Helper::quoteName($key) . ' = ?');
        array_push($vals, Helper::attrsToStrings($column->getType(), $value));
      }
    }

    $sql = 'UPDATE ' . $tableName . ' SET ' . implode(', ', $sets) . ' WHERE ' . implode(' AND ', $where) . ';';

    $stmt = null;
    $error = Connection::instance($this->getDb())->runQuery($sql, $vals, $stmt);
    if ($error instanceof \Exception) {
      throw new \Exception('新增資料錯誤，錯誤原因：' . $error->getMessage());
      return null;
    }

    $count = $stmt->rowCount();
    return $this->_cleanFlagDirty();
  }
  public function delete(?int &$count = 0): ?self { // php8 -> return static
    if ($this->_deleted) {
      return $this;
    }

    $table = static::getTable($this->getDb());
    $columns = $table->getColumns();
    $primaries = $this->_getPrimaries();
    $tableName = $table->getName(true);

    if (!$primaries) {
      Model::executeLog('刪除資料失敗，錯誤原因：找不到 Primary Key');
      return null;
    }

    $tmps = [];
    $vals = [];

    foreach ($primaries as $key => $val) {
      array_push($tmps, $tableName . '.' . Helper::quoteName($key) . ' = ?');
      array_push($vals, $val);
    }

    $sql = 'DELETE FROM ' . $tableName . ' WHERE ' . implode(' AND ', $tmps) . ';';

    $stmt = null;
    $error = Connection::instance($this->getDb())->runQuery($sql, $vals, $stmt);
    if ($error instanceof \Exception) {
      throw new \Exception('移除資料庫錯誤，錯誤原因：' . $error->getMessage());
      return null;
    }

    $count = $stmt->rowCount();

    // afterDeletes 不保證成功全跑完，失敗結束不影響新增
    $afterDeletes = static::$afterDeletes ?? [];
    if (!is_array($afterDeletes)) {
      $afterDeletes = [$afterDeletes];
    }

    $return = $this;
    foreach ($afterDeletes as $afterDelete) {
      if (method_exists($this, $afterDelete)) {
        try {
          $return = $this->$afterDelete($return);
        } catch (\Throwable $e) {
          Model::executeLog('Model「' . static::class . '」執行 after create「' . $afterDelete . '」失敗，錯誤原因：' . $e->getMessage());
          break;
        }
      }
    }

    return $this;
  }
  public function set(array $attrs = [], array $allow = [], bool $save = false): ?self { // php8 -> return static
    if ($allow) {
      $attrs = array_intersect_key($attrs, array_flip($allow));
    }

    foreach ($attrs as $key => $val) {
      $this->$key = $val;
    }

    return $save ? $this->save() : $this;
  }
  public function attrs(?string $key = null, $default = null) {
    if ($key === null) {
      return $this->_attrs;
    }

    if (array_key_exists($key, $this->attrs)) {
      return $this->attrs[$key];
    }

    return $default;
  }
  public function toArray(bool $isRaw = false): array {
    return Helper::toArray($this, $isRaw);
  }
  public function getDb(): ?string {
    return $this->_db;
  }

  private function _cleanFlagDirty(): self {
    $this->_dirties = [];
    return $this;
  }
  private function _getPrimaries(): array {
    $table = static::getTable($this->getDb());
    $primaries = $table->getPrimaries();
    $tmp = [];

    foreach ($primaries as $primary) {
      if (array_key_exists($primary, $this->_attrs)) {
        $tmp[$primary] = $this->_attrs[$primary];
      }
    }

    return $tmp;
  }
}
