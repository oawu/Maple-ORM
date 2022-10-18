<?php

namespace M {
  use \M\Core\Table;
  use \M\Core\Connection;
  use \M\Core\Config;
  use \M\Core\Builder;
  use \M\Core\Error;
  use \M\Core\Inflect;
  use \M\Core\Plugin;
  use \M\Core\Plugin\DateTime;
  use \M\Core\Plugin\Uploader;
  use \M\Core\Plugin\Uploader\File;
  use \M\Core\Plugin\Uploader\Image;
  use \M\Core\Plugin\Uploader\TinyKey;
  use \M\Core\Plugin\Uploader\Driver\S3;

  function attrsToStrings($type, $val) {
    return $type != 'json'
      ? $val instanceof Plugin
        ? $val->SQL()
        : $val
      : @json_encode($val);
  }

  function foreign($tableName, $foreign = null) {
    return $foreign === null
      ? Model::caseColumn() == Model::CASE_SNAKE
        ? (Inflect::singularize($tableName) . '_id')
        : (lcfirst($tableName) . 'Id')
      : $foreign;
  }

  function quoteName($string) {
    return !($string[0] === '`' || $string[strlen($string) - 1] === '`')
      ? '`' . $string . '`'
      : $string;
  }

  function columnFormat($row) {
    $row = array_change_key_case($row, CASE_LOWER);

    preg_match_all('/^(?P<type>[A-Za-z0-9_]+)(\((?P<length>.*)\))?( (?P<infos>.*))?/', $row['type'], $matches);
    $type   = $matches['type'] ? array_shift($matches['type']) : $row['type'];
    $length = $matches['length'] ? array_shift($matches['length']) : null;
    $infos  = $matches['infos'] ? array_values(array_filter(array_map('trim', explode(' ', array_shift($matches['infos']))), function($info) { return $info !== ''; })) : [];

    $type == 'timestamp' && $type = 'datetime';
    $type == 'integer'   && $type = 'int';

    $return = [
      'type'     => $type,
      'field'    => $row['field'],
      'nullable' => $row['null']  === 'YES',
      'primary'  => $row['key']   === 'PRI',
      'auto'     => $row['extra'] === 'auto_increment',
      'default'  => $row['default'],
      'infos'    => $infos
    ];

    if ($type == 'enum') {
      preg_match_all('/(\(?)\'(?P<vals>[^,]*)\'\1/', $length, $matches);
      $return['vals'] = $matches['vals'] ?: [];
    }
    if ($type == 'varchar' || $type == 'int') {
      $return['length'] = (int)$length;
    }
    if ($type == 'decimal') {
      $return['length'] = array_map(function($item) { return 0 + $item; }, array_filter(array_map('trim', explode(',', $length)), function($item) { return $item !== ''; }));
    }

    return $return;
  }

  function columnInit($format, $val, $plugin, &$model = null, $column = null) {
    columnNull($format, $val);

    if ($plugin) {
      try { return (clone $plugin)->setModel($model)->setColumn($column)->setValue($val); }
      catch (Error $e) { throw new Error('「' . $format['field'] . '」欄位值' . $e->getMessage()); }
    }

    switch ($format['type']) {
      case 'tinyint': case 'smallint': case 'mediumint': case 'int': case 'bigint': return columnInt($val); 
      case 'float': case 'double': case 'numeric': case 'decimal': case 'dec': return columnFloat($val); 
      case 'datetime': case 'timestamp': case 'date': case 'time': return columnDateTime($format, $val); 
      case 'json': return $val !== null ? @json_decode($val, true) : null;
      case 'enum': return columnEnum($val, $format);
      default: return columnString($val);
    }

    return $val;
  }

  function columnUpdate($format, $oldVal, $newVal) {
    columnNull($format, $newVal);
      
    if ($oldVal instanceof Plugin) {
      try { return $oldVal->setValue($newVal); }
      catch (Error $e) { throw new Error('「' . $format['field'] . '」欄位值' . $e->getMessage()); }
    }

    switch ($format['type']) {
      case 'tinyint': case 'smallint': case 'mediumint': case 'int': case 'bigint': return columnInt($newVal); 
      case 'float': case 'double': case 'numeric': case 'decimal': case 'dec': return columnFloat($newVal); 
      case 'datetime': case 'timestamp': case 'date': case 'time': return columnDateTime($format, $newVal); 
      case 'json': return $newVal;
      case 'enum': return columnEnum($newVal, $format);
      default: return columnString($newVal);
    }

    return $newVal;
  }

  function columnNull($format, $val) {
    if ($val === null && !$format['nullable'] && !$format['auto'])
      throw new Error('「' . $format['field'] . '」欄位不可以為 NULL');
  }

  function columnInt($val) {
    if ($val === null) return null;
    if (is_int($val)) return $val;
    if (is_numeric($val) && floor($val) != $val) return (int)$val;
    if (is_string($val) && is_float($val + 0)) return (string)$val;
    if (is_float($val) && $val >= PHP_INTM\CoreAX) return number_format($val, 0, '', '');
    return (int)$val;
  }

  function columnFloat($val) {
    return $val !== null ? (double)$val : null;
  }

  function columnDateTime($format, $val) {
    try { return DateTime::create($format['type'])->setValue($val); }
    catch (Error $e) { throw new Error('「' . $format['field'] . '」欄位值' . $e->getMessage()); }
  }

  function columnString($val) {
    return $val !== null ? (string)$val : null;
  }

  function columnEnum($val, $format) {
    if ($val === null) return null;
    if (!in_array((string)$val, $format['vals']))
      throw new Error('「' . $format['field'] . '」欄位格式為「' . $format['type'] . '」，選項有「' . implode('、', $format['vals']) . '」，您給予的值為：' . $val . '，不在選項內');
    return (string)$val;
  }

  function defaults($table, $attrs, $full = true) {
    $columns = $table->columns;

    isset($columns[$table->class::$createAt])
      && !array_key_exists($table->class::$createAt, $attrs)
      && $attrs[$table->class::$createAt] = \date(DateTime::formatByType($columns[$table->class::$createAt]['type']));
    
    isset($columns[$table->class::$updateAt])
      && !array_key_exists($table->class::$updateAt, $attrs)
      && $attrs[$table->class::$updateAt] = \date(DateTime::formatByType($columns[$table->class::$updateAt]['type']));

    $newAttrs = [];
    foreach ($columns as $key => $column) {

      if (!($full || array_key_exists($key, $attrs)))
        continue;

      array_key_exists($key, $attrs) || $attrs[$key] = $column['default'];

      if (!($column['nullable'] || isset($attrs[$key]) || $column['auto']))
        throw new Error('「' . $key . '」欄位不可以為 NULL');

      $newAttrs[$key] = $column['type'] != 'json'
        ? $attrs[$key] ?? null
        : @json_encode($attrs[$key]);
    }
    return $newAttrs;
  }

  function toArray($obj) {
    $attrs = [];
    $hides = array_fill_keys($obj->table()->class::$hides ?? [], '1');
    
    foreach ($obj->attrs() as $key => $attr) {
      if (isset($hides[$key]))
        continue;

      if ($attr instanceof Image)
        $attrs[$key] = array_combine($keys = array_keys($attr->versions()), array_map(function($key) use ($attr) { return $attr->url($key); }, $keys));
      else if ($attr instanceof File)
        $attrs[$key] = $attr->url();
      else if ($attr instanceof DateTime)
        $attrs[$key] = (string)$attr;
      else if (isset($obj->table()->columns[$key]) && in_array($obj->table()->columns[$key]['type'], ['tinyint', 'smallint', 'mediumint', 'int', 'bigint']))
        $attrs[$key] = is_int($attr) ? $attr : (is_numeric($attr) && floor($attr) != $attr ? (int)$attr : (is_string($attr) && is_float($attr + 0) ? (string)$attr : (is_float($attr) && $attr >= PHP_INTM\CoreAX ? number_format($attr, 0, '', '') : (int)$attr)));
      else if (isset($obj->table()->columns[$key]) && in_array($obj->table()->columns[$key]['type'], ['float', 'double', 'numeric', 'decimal', 'dec']))
        $attrs[$key] = (double)$attr;
      else if (isset($obj->table()->columns[$key]) && in_array($obj->table()->columns[$key]['type'], ['json']))
        $attrs[$key] = $attr;
      else 
        $attrs[$key] = (string)$attr;
    }

    return $attrs;
  }
  
  function getNamespaces($className) {
    return array_slice(explode('\\', $className), 0, -1);
  }

  function deNamespace($className) {
    $className = array_slice(explode('\\', $className), -1);
    return array_shift($className);
  }

  function toTabelSnake($str) {
    return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $str));
  }

  function rollback($message = null) {
    throw new \Error($message ?? Model::$lastLog);
  }

  function transaction($closure, &$errors = []) {
    $instance = Connection::instance();

    try {

      if (!$instance->beginTransaction())
        throw new \Error('Transaction 失敗');

      if (!$result = $closure())
        throw new \Error('transaction 回傳 false，故 rollback');

      if (!$instance->commit())
        throw new \Error('Commit 失敗');

      return $result;
    } catch (\Error $e) {
      $errors = $instance->rollback()
        ? [$e->getMessage()]
        : ['Rollback 失敗', $e->getMessage()];

      return null;
    } catch (\Exception $e) {
      $errors = $instance->rollback()
        ? [$e->getMessage()]
        : ['Rollback 失敗', $e->getMessage()];

      return null;
    }

    return ['不明原因錯誤！'];
  }

  function _relation($class) {
    class_exists($class);
    $traces = array_filter(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT), function($trace) { return isset($trace['class'], $trace['object']) && $trace['object'] instanceof Model; });
    if (!$trace = array_shift($traces)) throw new Error('Relation Many 綁定錯誤');
    return $trace;
  }
  function _has($class, $type, $fk = null, $pk = 'id') {
    $trace = _relation($class);
    $fk = $fk ?? foreign($trace['class']::table()->name);
    return Builder::create($class, $type)->has($fk, $pk, $trace['object']->$pk);
  }
  function _belongs($class, $type, $fk = null, $pk = 'id') {
    $trace = _relation($class);
    $fk = $fk ?? foreign($class::table()->name);
    return Builder::create($class, $type)->belongs($pk, $fk, $trace['object']->$fk);
  }
  function hasMany($class, $fk = null, $pk = 'id') {
    return _has($class, 'all', $fk, $pk);
  }
  function hasOne($class, $fk = null, $pk = 'id') {
    return _has($class, 'one', $fk, $pk);
  }
  function belongsToMany($class, $fk = null, $pk = 'id') {
    return _belongs($class, 'all', $fk, $pk);
  }
  function belongsTo($class, $fk = null, $pk = 'id') {
    return _belongs($class, 'one', $fk, $pk);
  }

  abstract class Model {
    const CASE_CAMEL = 'Camel';
    const CASE_SNAKE = 'Snake';

    static $createAt = 'createAt';
    static $updateAt = 'updateAt';

    private static $logFunc = null;
    public  static $lastLog = null;
    public  static $query = null;
    private static $errorFunc = null;
    private static $caches = [];
    private static $dirs   = null;
    private static $caseTable = Model::CASE_CAMEL;
    private static $caseColumn = Model::CASE_CAMEL;

    public static function config($key = '', $config = []) {
      return Connection::$configs[$key] = new Config($config);
    }
    
    public static function dir($dir) {
      return is_dir($dir)
        ? !!self::dirs(array_merge([$dir], array_filter(array_map(function($t) use ($dir) {
          return !in_array($t, ['.', '..'])
            ? $dir . $t . DIRECTORY_SEPARATOR
            : null; }, scandir($dir)), 'is_dir')))
        : false;
    }

    public static function dirs($dirs) {
      self::$dirs !== null || spl_autoload_register(function($className) {
        if (!$namespaces = getNamespaces($className))
          return false;

        if (!in_array($namespace = array_shift($namespaces), ['M']))
          return false;

        if (!$modelName = deNamespace($className))
          return false;

        foreach (self::$dirs as $dir)
          if (is_file($tmp = $dir . $modelName . '.php') && is_readable($tmp))
            if ($path = $tmp)
              break;

        if (!isset($path))
          return false;

        include_once $path;

        return class_exists($className);
      });        

      return $dirs !== null ? self::$dirs = $dirs : self::$dirs;
    }

    public static function caseTable($caseTable = null) {
      return $caseTable !== null
        ? self::$caseTable = in_array($caseTable, [Model::CASE_CAMEL, Model::CASE_SNAKE])
          ? $caseTable
          : self::$caseTable
        : self::$caseTable;
    }
    
    public static function caseColumn($caseColumn = null) {
      return $caseColumn !== null
        ? self::$caseColumn = in_array($caseColumn, [Model::CASE_CAMEL, Model::CASE_SNAKE])
          ? $caseColumn
          : self::$caseColumn
        : self::$caseColumn;
    }
    
    public static function queryLogFunc($func) {
      return Connection::logFunc($func);
    }

    public static function logFunc($func) {
      return self::$logFunc = $func;
    }

    public static function errorFunc($func) {
      return self::$errorFunc = $func;
    }

    public static function cacheFunc($type, $func) {
      return self::$caches[$type] = $func;
    }

    public static function salt($key = null) {
      return TinyKey::key($key);
    }

    public static function extensions($extensions) {
      return S3::extensions($extensions);
    }

    public static function uploader($column, $class) {
      $class = '\\M\\Core\\Plugin\\Uploader\\' . $class;
      return self::table()->plugins[$column] = $class::create();
    }
    
    public static function thumbnail($func) {
      return Image::thumbnail($func);
    }

    public static function setUploader($func) {
      return Uploader::func($func);
    }

    public static function writeLog($message) {
      self::$lastLog = $message;
      $logFunc = self::$logFunc; $logFunc && $logFunc(self::$lastLog);
      return false;
    }

    public static function cache($type, $key, $closure) {
      $cacheFunc = self::$caches[$type] ?? null;
      return $cacheFunc ? $cacheFunc(...[$key, $closure]) : $closure();
    }
    
    public static function error(...$args)      { $errorFunc = self::$errorFunc; $errorFunc ? $errorFunc(...$args) : var_dump($args); exit(1); }
    public static function table()              { return Table::instance(static::class); }
    
    public static function builder()            { return Builder::create(static::class); }

    public static function where(...$args)      { return Builder::create(static::class)->where(...$args); }
    public static function whereIn($key, $vals) { return Builder::create(static::class)->whereIn($key, $vals); }
    public static function in($key, $vals)      { return Builder::create(static::class)->whereIn($key, $vals); }

    public static function whereNotIn($key, $vals) { return Builder::create(static::class)->whereNotIn($key, $vals); }
    public static function notIn($key, $vals)      { return Builder::create(static::class)->whereNotIn($key, $vals); }

    public static function whereBetween($key, $val1, $val2) { return Builder::create(static::class)->whereBetween($key, $val1, $val2); }
    public static function between($key, $val1, $val2)      { return Builder::create(static::class)->whereBetween($key, $val1, $val2); }

    public static function select(...$args)     { return Builder::create(static::class)->select(...$args); }
    public static function order(...$args)      { return Builder::create(static::class)->order(...$args); }
    public static function group($group)        { return Builder::create(static::class)->group($group); }
    public static function having($having)      { return Builder::create(static::class)->having($having); }
    public static function limit($limit)        { return Builder::create(static::class)->limit($limit); }
    public static function offset($offset)      { return Builder::create(static::class)->offset($offset); }
    public static function keyBy($key)          { return Builder::create(static::class)->keyBy($key); }
    public static function relation(...$args)   { return Builder::create(static::class)->relation(...$args); }
    public static function one($id = null)      { return Builder::create(static::class)->one($id); }
    public static function first($id = null)    { return Builder::create(static::class)->first($id); }
    public static function last($id = null)     { return Builder::create(static::class)->last($id); }
    public static function all()                { return Builder::create(static::class)->all(); }
    public static function count()              { return Builder::create(static::class)->count(); }
    public static function update($sets = [])   { return Builder::create(static::class)->update($sets); }
    public static function closeDB()            { return Connection::close(); }
    public static function truncate()           {
      $e = Connection::instance()->query(implode(' ', ['TRUNCATE', 'TABLE', quoteName(static::table()->name)]) . ';');
      return $e ? Model::writeLog('資料庫執行語法錯誤，錯誤原因：' . $e) ?: false : true;
    }
    public static function create($attrs = [], $allow = []) {
      $allow && $attrs = array_intersect_key($attrs, array_flip($allow));
      $attrs = array_intersect_key($attrs, static::table()->columns);
      $class = static::class;

      try { $model = new $class(defaults(static::table(), $attrs), true); }
      catch (Error $e) { return Model::writeLog($e->getMessage()) ?: null; }

      return $model;
    }
    public static function creates($rows = [], $limit = 100) { // null, count
      $table = static::table();
      try {
        $rows = array_values(array_map(function($attrs) use ($table) {
          $tmps = [];
          foreach (defaults($table, $attrs, true) as $name => $value)
            isset($table->columns[$name]) && $tmps[$name] = columnInit($table->columns[$name], $value, $table->plugins[$name] ?? null);
          return $tmps;
        }, $rows));
      } catch (Error $e) {
        return Model::writeLog($e->getMessage()) ?: null;
      }

      if (!$rows)
        return 0;

      $j = 0;
      $page = ['pits' => [], 'vals' => []];
      $pages = [];
      $len  = count($cols = array_flip(array_keys($rows[0])));

      foreach ($rows as $i => $row) {
        if ($len != count(array_intersect_key($row, $cols)))
          return Model::writeLog('結構錯誤，第 ' . ($i + 1) . '筆資料 key 結構與第一筆不同') ?: null;

        if (count($page['pits']) > $limit) {
          array_push($pages, $page);
          $page = ['pits' => [], 'vals' => []];
        }

        array_push($page['pits'], '(' . implode(', ', array_fill(0, $len, '?')) . ')');
        
        $tmps = [];
        foreach ($row as $key => $val)
          array_push($tmps, attrsToStrings($table->columns[$key]['type'], $val));
        $page['vals'] = array_merge($page['vals'], $tmps);
      }

      $page['pits'] && array_push($pages, $page);

      foreach ($pages as $page) {
        $sth = null;
        if ($e = Connection::instance()->query('INSERT INTO ' . quoteName(static::table()->name) . ' (' . implode(', ', array_map(function($key) { return quoteName(static::table()->name) . '.' . quoteName($key); }, array_keys($rows[0]))) . ') VALUES ' . implode(', ', $page['pits']) . ';', $page['vals'], $sth))
          return Model::writeLog('新增資料庫錯誤，錯誤原因：' . $e) ?: null;
      }

      // $sth->rowCount() == count($rows) || Model::writeLog('新增資料庫錯誤，錯誤原因：影響筆數為 1 筆，但應該為 ' . count($rows) . ' 筆');
      // $sth->rowCount()
      return true;
    }

    public static function hasMany($class, $fk = null, $pk = 'id') { return _has($class, 'all', $fk, $pk); }
    public static function hasOne($class, $fk = null, $pk = 'id') { return _has($class, 'one', $fk, $pk); }
    public static function belongsTo($class, $fk = null, $pk = 'id') { return _belongs($class, 'one', $fk, $pk); }
    public static function belongsToMany($class, $fk = null, $pk = 'id') { return _belongs($class, 'all', $fk, $pk); }
    
    private $attrs     = [];
    private $vars      = [];
    private $dirties   = [];
    private $relations = [];
    private $isNew     = true;

    public function __construct($attrs = [], $isNew = true) {
      $this->isNew = $isNew;

      foreach ($attrs as $name => $value)
        $this->attrs[$name] = isset(static::table()->columns[$name])
          ? columnInit(static::table()->columns[$name], $value, static::table()->plugins[$name] ?? null, $this, $name)
          : $value;

      if (!$isNew)
        return $this;

      $table = static::table();

      $cols = $pits = $vals = [];

      foreach ($this->attrs as $key => $val) {
        array_push($cols, quoteName($key));
        array_push($pits, '?');
        array_push($vals, attrsToStrings($table->columns[$key]['type'], $val));
      }

      $sth = null;

      if ($e = Connection::instance()->query('INSERT INTO ' . quoteName($table->name) . ' (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $pits) . ');', $vals, $sth))
        throw new Error('新增資料庫錯誤，錯誤原因：' . $e);

      // if ($sth->rowCount() != 1)
      //   throw new Error('新增資料庫錯誤，錯誤原因：影響筆數不為 1 筆');
      
      $id = (int)Connection::instance()->lastInsertId();

      foreach ($table->primaries as $primary)
        if (isset($table->columns[$primary]) && $table->columns[$primary]['auto'])
          $this->attrs[$primary] = $id;

      $this->_cleanFlagDirty($this->isNew = false);

      $afterCreates = static::$afterCreates ?? [];
      is_array($afterCreates) || $afterCreates = [$afterCreates];

      $return = $this;
      foreach ($afterCreates as $afterCreate) {
        if (!method_exists($this, $afterCreate))
          throw new Error('Model「' . static::class . '」內沒有「' . $afterCreate . '」的 method');

        if (!$return = $this->$afterCreate($return))
          throw new Error('Model「' . static::class . '」執行「' . $afterCreate . '」after create 失敗');
      }
    }

    public function __set($name, $value) {
      if (array_key_exists($name, $this->attrs) && isset(static::table()->columns[$name]))
        return $this->_updateAttr($name, $value);

      if (array_key_exists($name, $this->relations) || method_exists($this, $name))
        return $this->relations[$name] = $value;

      return $this->vars[$name] = $value;
    }

    public function &__get($name) {
      if (array_key_exists($name, $this->attrs))
        return $this->attrs[$name];

      if (array_key_exists($name, $this->relations))
        return $this->relations[$name];

      if (method_exists($this, $name)) {
        $this->relations[$name] = $this->$name()->will();
        return $this->relations[$name];
      }

      if (!array_key_exists($name, $this->vars))
        $this->vars[$name] = null;

      return $this->vars[$name];
    }

    public function __isset($name) {
      return array_key_exists($name, $this->attrs);
    }

    public function save(&$count = 0) {
      $table = static::table();

      if (!$primaries = $this->_primaries())
        return Model::writeLog('更新資料失敗，錯誤原因：找不到 Primary Key') ?: null;

      if (!array_intersect_key($this->attrs, $this->dirties))
        return $this;
      
      if (isset($table->columns[static::$updateAt]) && array_key_exists(static::$updateAt, $this->attrs) && !array_key_exists(static::$updateAt, $this->dirties)) {
        try { $this->_updateAttr(static::$updateAt, \date(DateTime::formatByType($table->columns[static::$updateAt]['type']))); }
        catch (Error $e) { return Model::writeLog('更新資料失敗，錯誤原因：' . $e) ?: null; }
      }

      $sets = $vals = $tmps = [];

      foreach (array_intersect_key($this->attrs, $this->dirties) as $key => $val) {
        array_push($sets, quoteName($table->name) . '.' . quoteName($key) . ' = ?');
        array_push($vals, attrsToStrings($table->columns[$key]['type'], $val));
      }

      foreach ($primaries as $key => $val) {
        array_push($tmps, quoteName($table->name) . '.' . quoteName($key) . ' = ?');
        array_push($vals, $val);
      }

      $sth = null;

      if ($e = Connection::instance()->query('UPDATE ' . quoteName($table->name) . ' SET ' . implode(', ', $sets) . ' WHERE ' . implode(' AND ', $tmps) . ';', $vals, $sth))
        return Model::writeLog('更新資料庫錯誤，錯誤原因：' . $e) ?: null;

      $count = $sth->rowCount();

      return $this->_cleanFlagDirty();
    }

    public static function __callStatic($name, $arguments) {
      if ($name == 'delete') return Builder::create(static::class)->delete();
      throw new Error(static::class . ' 沒有「' . $name . '」這個 static method');
    }

    public function __call($name, $arguments) {
      if ($name == 'delete') return $this->remove(...$arguments);
      throw new Error(static::class . ' 沒有「' . $name . '」這個 method');
    }
    
    public function remove(&$count = 0) {
      if (!$primaries = $this->_primaries())
        return Model::writeLog('刪除資料失敗，錯誤原因：找不到 Primary Key');

      $table = static::table();
      
      $tmps = $vals = [];
      foreach ($primaries as $key => $val) {
        array_push($tmps, quoteName($table->name) . '.' . quoteName($key) . ' = ?');
        array_push($vals, $val);
      }

      $sth = null;

      if ($e = Connection::instance()->query('DELETE FROM ' . quoteName($table->name) . ' WHERE ' . implode(' AND ', $tmps) . ';', $vals, $sth))
        return Model::writeLog('移除資料庫錯誤，錯誤原因：' . $e);

      $count = $sth->rowCount();

      $afterDeletes = static::$afterDeletes ?? [];
      is_array($afterDeletes) || $afterDeletes = [$afterDeletes];

      $return = $this;
      foreach ($afterDeletes as $afterDelete) {
        if (!method_exists($this, $afterDelete))
          return Model::writeLog('Model「' . static::class . '」內沒有「' . $afterDelete . '」的 method');

        if (!$return = $this->$afterDelete($return))
          return Model::writeLog('Model「' . static::class . '」執行「' . $afterDelete . '」after create 失敗');
      }

      return true;
    }
    
    public function attrs($key = null, $default = null) {
      return $key !== null
        ? array_key_exists($key, $this->attrs)
          ? $this->attrs[$key]
          : $default
        : $this->attrs;
    }

    public function toArray() {
      return toArray($this);
    }

    public function set($attrs = [], $allow = [], $save = false) {
      $allow === true  && ($save = $allow) && $allow = [];
      $allow === false && !($save = $allow) && $allow = [];
      $allow && $attrs = array_intersect_key($attrs, array_flip($allow));

      foreach ($attrs as $key => $val)
        $this->$key = $val;

      return $save ? $this->save() : $this;
    }

    private function _updateAttr($name, $value) {
      $this->attrs[$name] = columnUpdate(static::table()->columns[$name], $this->attrs[$name], $value);
      $this->dirties[$name] = true;
      return $this->attrs[$name];
    }

    private function _cleanFlagDirty() {
      $this->dirties = [];
      return $this;
    }
    
    private function _primaries() {
      $tmp = [];

      foreach (static::table()->primaries as $primary)
        if (array_key_exists($primary, $this->attrs))
          $tmp[$primary] = $this->attrs[$primary];

      return $tmp;
    }
  }
};

namespace M\Core {
  use function \M\attrsToStrings;
  use function \M\quoteName;
  use function \M\deNamespace;
  use function \M\toTabelSnake;
  use function \M\columnFormat;
  use function \M\defaults;
  use function \M\columnInit;

  use \M\Model;
  use \M\Core\Plugin;

  function umaskChmod($path, $mode = 0777) {
    $oldmask = umask(0);
    @chmod($path, $mode);
    umask($oldmask);
  }

  function umaskMkdir($path, $mode = 0777, $recursive = false) {
    $oldmask = umask(0);
    $return = @mkdir($path, $mode, $recursive);
    umask($oldmask);
    return $return;
  }

  final class Error extends \Exception {
    public function __construct($message) { parent::__construct($message); }
    public function __toString() { return $this->getMessage(); }
  }

  final class Builder {
    const TYPE_SELECT = "SELECT";
    const TYPE_UPDATE = "UPDATE";
    const TYPE_DELETE = "DELETE";

    private $type    = null;
    private $class   = null;
    private $options = [];
    
    private function __construct($class, $will = null) {
      $this->class = $class;
      $will === null || $this->options['will'] = in_array($will, ['all', 'one', 'last', 'first']) ? $will : 'all';
    }

    public function __get($name) {
      if ($name == 'sql')      return $this->type != self::TYPE_SELECT ? $this->type != self::TYPE_UPDATE ? $this->type != self::TYPE_DELETE ? null : $this->_deleteSQL() : $this->_updateSQL() : $this->_searchSQL();
      if ($name == 'will')     return $this->options['will'] ?? 'all';
      if ($name == 'table')    return Table::instance($this->class);
      if ($name == 'values')   return $this->options['values'] ?? [];
      if ($name == 'relation') return $this->options['relation'] ?? null;
      return null;
    }

    public function __set($name, $val) {}

    public static function create($class, $will = null) { return new static($class, $will); }

    public function will() { $will = $this->will; return $will === null ? $this->all() : $this->$will(); }

    private function _where(...$args) {
      $length = count($args);
      if (!$length)
        return null;

      if ($length == 1) { // where(null), where(1), where([1, 2]), where('id=1')
        $value = array_shift($args);
        if (is_numeric($value)) return [quoteName($this->table->name) . '.' . quoteName('id') . ' = ?', 0 + $value];
        if (is_array($value))
          if ($value = array_unique($value))
            return $value ? array_merge([quoteName($this->table->name) . '.' . quoteName('id') . ' IN (' . implode(', ', array_fill(0, count($value), '?')) . ')'], $value) : null;
          else
            return null;
        if (is_string($value)) return [$value];
        return null;
      }

      if ($length == 2) { // where('id', 1), where('id', [1])
        $column = array_shift($args);
        $value  = array_shift($args);

        if ($value === null)
          return [quoteName($this->table->name) . '.' . quoteName($column) . ' IS NULL'];
        
        if (is_array($value))
          if ($value = array_unique($value))
            return array_merge([quoteName($this->table->name) . '.' . quoteName($column) . ' IN (' . implode(', ', array_fill(0, count($value), '?')) . ')'], $value);
          else
            return null;

        return [quoteName($this->table->name) . '.' . quoteName($column) . ' = ?', $value];
      }

      if ($length == 3) { // where('id', '=', 1), where('id', 'IN', [1]), where('id', 'NOT IN', [1]), where('id', 'BETWEEN', [1, 2])
        $column = array_shift($args);
        $key    = array_shift($args);
        $value  = array_shift($args);

        if (strtolower(trim($key)) == 'between')
          return count($value) >= 2 ? array_merge([quoteName($this->table->name) . '.' . quoteName($column) . ' BETWEEN ? AND ?'], $value) : null;

        if ($value === null)
          return [quoteName($this->table->name) . '.' . quoteName($column) . ' ' . $key . ' NULL'];

        if (is_array($value))
          if ($value = array_unique($value))
            return array_merge([quoteName($this->table->name) . '.' . quoteName($column) . ' ' . $key . ' (' . implode(', ', array_fill(0, count($value), '?')) . ')'], $value);
          else
            return null;

        return [quoteName($this->table->name) . '.' . quoteName($column) . ' ' . $key . ' ?', $value];
      }
    }

    private function _type($type = null) { $this->type = $type; return $this; }
    
    private function _reverseOrder() {
      $this->options['order'] = !isset($this->options['order'])
        ? $this->table->primaries
          ? implode(' DESC, ', $this->table->primaries) . ' DESC'
          : 'id DESC'
        : implode(', ', array_map(function($part) {
            $v = trim(strtolower($part));
            return strpos($v,' asc') === false
              ? strpos($v,' desc') === false
                ? $v . ' DESC'
                : preg_replace('/desc/i', 'ASC', $v)
              : preg_replace('/asc/i', 'DESC', $v);
          }, explode(',', $this->options['order'])));
      return $this;
    }

    private function _searchSQL() {
      $strs = ['SELECT', $this->options['select'] ?? quoteName($this->table->name) . '.*', 'FROM', quoteName($this->table->name)];

      isset($this->options['where'])  && array_push($strs, 'WHERE', $this->options['where']);
      isset($this->options['group'])  && array_push($strs, 'GROUP BY', $this->options['group']);
      isset($this->options['having']) && array_push($strs, 'HAVING', $this->options['having']);
      isset($this->options['order'])  && array_push($strs, 'ORDER BY', $this->options['order']);

      $limit  = isset($this->options['limit']) ? (int)$this->options['limit'] : 0;
      $offset = isset($this->options['offset']) ? (int)$this->options['offset'] : 0;

      if ($limit || $offset)
        array_push($strs, 'LIMIT', ($offset ? $offset . ', ' : '') . $limit);

      return implode(' ', array_filter($strs)) . ';';
    }

    private function _updateSQL() {
      $strs = ['UPDATE', quoteName($this->table->name), 'SET', implode(', ', $this->options['attrs'])];

      isset($this->options['where']) && array_push($strs, 'WHERE', $this->options['where']);
      isset($this->options['order']) && array_push($strs, 'ORDER BY', $this->options['order']);

      $limit  = isset($this->options['limit']) ? (int)$this->options['limit'] : 0;
      $offset = isset($this->options['offset']) ? (int)$this->options['offset'] : 0;

      if ($limit || $offset)
        array_push($strs, 'LIMIT', ($offset ? $offset . ', ' : '') . $limit);

      return implode(' ', array_filter($strs)) . ';';
    }

    private function _deleteSQL() {
      $strs = ['DELETE', 'FROM', quoteName($this->table->name)];

      isset($this->options['where']) && array_push($strs, 'WHERE', $this->options['where']);
      isset($this->options['order']) && array_push($strs, 'ORDER BY', $this->options['order']);

      $limit  = isset($this->options['limit']) ? (int)$this->options['limit'] : 0;
      $offset = isset($this->options['offset']) ? (int)$this->options['offset'] : 0;

      if ($limit || $offset)
        array_push($strs, 'LIMIT', ($offset ? $offset . ', ' : '') . $limit);

      return implode(' ', array_filter($strs)) . ';';
    }

    private function _search($isSingular) {
      $this->_type(self::TYPE_SELECT);

      $sth = null;
      $objs = [];

      if ($e = Connection::instance()->query($this->sql, $this->values, $sth)) return Model::writeLog('查詢資料庫錯誤，錯誤原因：' . $e) ?: ($isSingular ? null : []);
      
      try { $objs = array_map(function($row) { return new $this->class($row, false); }, $sth->fetchAll()); }
      catch (Error $e) { return Model::writeLog('實體 Model 時發生錯誤，錯誤原因：' . $e) ?: ($isSingular ? null : []); }

      if (!$objs) return $isSingular ? null : [];

      if (!empty($this->options['preBuilds'])) {
        foreach ($this->options['preBuilds'] as $relation) {
          $builds = array_filter(array_map(function($obj) use ($relation) { return method_exists($obj, $relation) ? $obj->$relation() : null; }, $objs));

          if (!$vals = array_filter(array_map(function($build) { return $build->relation['val'] ?? null; }, $builds), function($build) { return $build !== null; }))
            continue;

          if (!$build = array_shift($builds))
            continue;

          if(!$where = $build->relation)
            continue;

          $will = $build->will ?? null;
          $key1 = $where['key1'];
          $key2 = $where['key2'];

          // $relations = $build->resetWhere()->whereIn($key1, $vals)->keyBy($key1)->all();
          $relations = $build->whereIn($key1, $vals)->keyBy($key1)->all();

          foreach ($objs as $obj)
            if (method_exists($obj, $relation) && isset($obj->$key2)) {
              $obj->$relation = $relations[$obj->$key2] ?? [];
              $will == 'all' || $obj->$relation = array_shift($obj->$relation);
            }
        }
      }

      if ($isSingular) return array_shift($objs);

      if (isset($this->options['keyBy'])) {
        $keyBy = $this->options['keyBy'];
        $tmps = [];

        foreach ($objs as $obj) {
          if (!isset($obj->$keyBy)) return $objs;
          $tmps[$obj->$keyBy] ?? $tmps[$obj->$keyBy] = [];
          array_push($tmps[$obj->$keyBy], $obj);
        }

        return $tmps;
      }

      return $objs;
    }

    private function _execute() {
      $sth = null;
      $e = Connection::instance()->query($this->sql, $this->values, $sth);
      return $e ? Model::writeLog('資料庫執行語法錯誤，錯誤原因：' . $e) ?: null : $sth->rowCount();
    }

    public function resetWhere() {
      $this->options['where'] = null;
      $this->options['values'] = [];
      return $this;
    }

    public function has($fk, $pk, $val) { $this->options['relation'] = ['key1' => $fk, 'key2' => $pk, 'val' => $val]; return $this->where($fk, $val); }

    public function belongs($pk, $fk, $val) { $this->options['relation'] = ['key1' => $pk, 'key2' => $fk, 'val' => $val]; return $this->where($pk, $val); }

    public function where(...$args) {
      $where = $this->_where(...$args);
      if ($where === null) return $this;
      if (isset($this->options['where'])) {
        $this->options['where']  = '(' . $this->options['where'] . ') AND (' . array_shift($where) . ')';
        $this->options['values'] = array_merge($this->options['values'], $where);
      } else {
        $this->options['where']  = array_shift($where);
        $this->options['values'] = $where;
      }
      return $this;
    }
    public function whereIn($key, $vals) { return $this->where($key, 'IN', $vals); }
    public function whereNotIn($key, $vals) { return $this->where($key, 'NOT IN', $vals); }
    public function whereBetween($key, $val1, $val2) { return $this->where($key, 'between', [$val1, $val2]); }
    
    public function orWhere(...$args) {
      $where = $this->_where(...$args);

      if ($where === null) return $this;

      if (isset($this->options['where'])) {
        $this->options['where']  = '(' . $this->options['where'] . ') OR (' . array_shift($where) . ')';
        $this->options['values'] = array_merge($this->options['values'], $where);
      } else {
        $this->options['where']  = array_shift($where);
        $this->options['values'] = $where;
      }

      return $this;
    }
    public function or(...$args) { return $this->orWhere(...$args); }
    public function orWhereIn($key, $vals) { return $this->orWhere($key, 'IN', $vals); }
    public function orIn($key, $vals) { return $this->orWhereIn($key, $vals); }
    public function orWhereNotIn($key, $vals) { return $this->orWhere($key, 'NOT IN', $vals); }
    public function orNotIn($key, $vals) { return $this->orWhereNotIn($key, $vals); }
    public function orWhereBetween($key, $val1, $val2) { return $this->orWhere($key, 'between', [$val1, $val2]); }
    public function orBetween($key, $val1, $val2) { return $this->orWhereBetween($key, $val1, $val2); }
    
    public function select(...$args) { // 'id, name'   ['id' => 'i', 'name' => 'n']   'id', 'name'
      $args = implode(', ', array_reduce(array_map(function($arg) {
        if (is_string($arg)) return array_map('trim', explode(',', $arg));
        
        if (is_array($arg)) {
          $tmp = [];
          foreach ($arg as $key => $val) {
            $key = trim($key);
            $val = trim($val);
            $key !== '' && $val !== '' && array_push($tmp, $key . ' AS ' . $val);
          }
          return $tmp;
        }
        return is_array($arg) ? $arg : [$arg];
      }, $args), function($a, $b) { return array_merge($a, $b); }, []));

      $this->options['select'] = ($args === '' || $args === '*') ? quoteName($this->table->name) . '.*' : $args;
      
      return $this;
    }
    public function order(...$args) { // 'id ASC', 'name DESC'     ['id' => 'ASC', 'name' => 'DESC']
      $args = implode(', ', array_reduce(array_map(function($arg) {
        if (is_string($arg)) return [trim($arg)];

        if (is_array($arg)) {
          $tmp = [];
          foreach ($arg as $key => $val) {
            $key = trim($key);
            $val = trim($val);
            $key !== '' && $val !== '' && array_push($tmp, $key . ' ' . $val);
          }
          return $tmp;
        }

        return is_array($arg) ? $arg : [$arg];
      }, $args), function($a, $b) { return array_merge($a, $b); }, []));

      $args === '' || $this->options['order'] = $args;
      
      return $this;
    }
    public function group($group) { $this->options['group'] = $group; return $this; }
    public function having($having) { $this->options['having'] = $having; return $this; }
    public function limit($limit) { is_numeric($limit) && $limit >= 0 && $this->options['limit'] = $limit; return $this; }
    public function keyBy($key) { $this->options['keyBy'] = $key; return $this; }
    public function offset($offset) { is_numeric($offset) && $offset >= 0 && $this->options['offset'] = $offset; return $this; }
    public function relation(...$args) { // 'id, name'     'id', 'name'
      $this->options['preBuilds'] = array_reduce(array_map(function($arg) { return array_map('trim', explode(',', $arg)); }, $args), function($a, $b) { return array_merge($a, $b); }, []); return $this;
    }

    public function one($id = null)   { return $this->where($id)->limit(1)->_search(true); }
    public function first($id = null) { return $this->where($id)->limit(1)->_search(true); }
    public function last($id = null)  { return $this->where($id)->_reverseOrder()->limit(1)->_search(true); }
    public function all()             { return $this->_search(false); }
    public function count() { // null, int
      $this->select(['COUNT(*)' => 'count'])->group(null)->_type(self::TYPE_SELECT);

      $sth = null;

      if ($e = Connection::instance()->query($this->sql, $this->values, $sth))
        return Model::writeLog('查詢資料庫錯誤，錯誤原因：' . $e) ?: 0;

      if (!$objs = array_map(function($row) { return $row; }, $sth->fetchAll()))
        return 0;

      $objs = array_shift($objs);
      return $objs ? (int)$objs['count'] ?? 0 : 0;
    }
    public function update($attrs = []) { // null, int
      $this->_type(self::TYPE_UPDATE);

      $sets = [];
      try { $defaults = defaults($this->table, $attrs, false); }
      catch (Error $e) { return Model::writeLog($e->getMessage()) ?: null; }
      
      try {
        foreach ($defaults as $name => $value)
          $sets[$name] = columnInit($this->table->columns[$name], $value, $this->table->plugins[$name] ?? null);
      } catch (Error $e) { return Model::writeLog($e->getMessage()) ?: null; }

      if (array_key_exists($this->table->class::$createAt, $sets))
        unset($sets[$this->table->class::$createAt]);

      $attrs = [];
      $vals = [];

      foreach ($sets as $key => $val) {
        array_push($attrs, quoteName($this->table->name) . '.' . quoteName($key) . ' = ?');
        array_push($vals, attrsToStrings($this->table->columns[$key]['type'], $val));
      }

      if (!$attrs) return 0;

      $this->options['attrs'] = $attrs;
      $this->options['values'] = array_merge($vals, $this->options['values'] ?? []);

      return $this->_execute();
    }
    public function delete() { return $this->_type(self::TYPE_DELETE)->_execute(); }
  }

  final class Table {
    private static $instances = [];

    public static function instance($className) { return self::$instances[$className] ?? self::$instances[$className] = new Table($className); }

    public $columns   = [];
    public $primaries = [];
    public $plugins   = [];
    private $className = '';
    private $cacheName = null;

    private function __construct($className) {
      $this->className = $className;
      $this->_getMetaData()->_setPrimaries();
    }

    public function __get($name) {
      if ($name == 'name') {
        if (isset($this->cacheName))
          return $this->cacheName;

        $className = $this->className;
        
        if (isset($className::$tableName))
          return $this->cacheName = $className::$tableName;

        return $this->cacheName = Model::caseTable() == Model::CASE_SNAKE
          ? toTabelSnake(deNamespace($className))
          : deNamespace($className);
      }
      if ($name == 'class') return $this->className;
      return null;
    }

    public function __set($name, $val) {}

    private function _getMetaData() {
      $sth = null;
      
      $closure = function() {
        $e = Connection::instance()->query("SHOW COLUMNS FROM " . quoteName($this->name) . ';', [], $sth);
        $e && Model::error('取得「' . $this->name . '」Table 的 Meta Data 失敗，錯誤原因：' . $e);

        $columns = [];
        foreach ($sth->fetchAll() as $row)
          if ($column = columnFormat($row))
            $columns[$column['field']] = $column;

        return $columns;
      };

      $this->columns = Model::cache('MetaData', $this->name, $closure);

      return $this;
    }

    private function _setPrimaries() {
      $className = $this->className;
      $this->primaries = isset($className::$primaries)
        ? is_array($className::$primaries)
          ? $className::$primaries
          : [$className::$primaries]
        : array_values(array_column(array_filter($this->columns, function($column) {
          return $column['primary'];
        }), 'field'));
      return $this;
    }
  }

  final class Connection extends \PDO {
    public  static $configs   = [];
    private static $logFunc    = null;
    private static $instances = [];
    private static $options   = [\PDO::ATTR_CASE => \PDO::CASE_NATURAL, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL, \PDO::ATTR_STRINGIFY_FETCHES => false];

    public static function logFunc($func) { return self::$logFunc = $func; }

    public static function create($config) {
      try { return new static($config); }
      catch (\PDOException $e) { Model::error('PDO 連線錯誤，請檢查 Database Config 設定值，錯誤原因：' . $e->getMessage()); return null; }
    }

    public static function instance($key = '') { return self::$instances[$key] ?? self::$instances[$key] = static::create(self::$configs[$key] ?? Model::error('尚未設定連線方式')); }

    public static function close() {
      foreach (self::$instances as &$instance) $instance = null;
      self::$instances = [];
      return true;
    }

    public function __construct($config) {
      parent::__construct('mysql:host=' . $config->hostname() . ';dbname=' . $config->database(), $config->username(), $config->password(), self::$options);
      $this->setEncoding($config->encoding());
    }

    public function setEncoding($encoding) {
      $e = $this->query('SET NAMES ?;', [$encoding]);
      $e && Model::error('設定編碼格式「' . $encoding . '」失敗，錯誤原因：' . $e);
      return $this;
    }

    public function query($sql, $vals = [], &$sth = null, $fetchModel = \PDO::FETCH_ASSOC, $log = true) {
      try {
        Model::$query = [$sql, $vals];

        if (!$sth = $this->prepare((string)$sql))
          return '執行 Connection prepare 失敗';

        $sth->setFetchMode($fetchModel);
        
        $start = \microtime(true);
        $status = $sth->execute($vals);

        $logFunc = self::$logFunc ?? null;
        $logFunc && $logFunc($sql, $vals, $status, \number_format((\microtime(true) - $start) * 1000, 1), $log);

        if (!$status) return '執行 Connection execute 失敗';
        return null;
      } catch (\PDOException $e) {
        return $e->getMessage();
      }
    }
  }

  final class Config {
    private $hostname = null;
    private $username = null;
    private $password = null;
    private $database = null;
    private $encoding = 'utf8mb4';

    public function __construct($options = []) {
      foreach (array_intersect_key($options, array_flip(['hostname', 'username', 'password', 'database', 'encoding'])) as $key => $value)
        $this->$key($value);
    }

    public function hostname($hostname = null) { if (!isset($hostname)) return $this->hostname; $this->hostname = $hostname; return $this; }
    public function username($username = null) { if (!isset($username)) return $this->username; $this->username = $username; return $this; }
    public function password($password = null) { if (!isset($password)) return $this->password; $this->password = $password; return $this; }
    public function database($database = null) { if (!isset($database)) return $this->database; $this->database = $database; return $this; }
    public function encoding($encoding = null) { if (!isset($encoding)) return $this->encoding; $this->encoding = $encoding; return $this; }
  }

  abstract class Plugin {
    abstract public function SQL();
    abstract public function validate();
    abstract public function __toString();

    static public function create(...$args) {
      return new static(...$args);
    }

    protected $value;
    protected $model;
    protected $column;

    public function setModel(&$model = null) {
      $this->model = &$model;
      return $this;
    }
    
    public function setColumn($column = null) {
      $this->column = $column;
      return $this;
    }
    
    public function setValue($value = null) {
      $this->value = $value;
      
      if ($this->value !== null && !$this->validate()) {
        throw new Error('「' . $this->value . '」無法轉為 ' . static::class . ' 格式');
      }

      return $this;
    }

    public function __get($name) {
      if ($name === 'value') return $this->value;
      return null;
    }
    public function __set($name, $value) {
      return null;
    }
    
    public function isNull() {
      return $this->value === null;
    }
    
    public function isEmpty() {
      return $this->isNull() || $this->value === '';
    }
  }

  abstract class Inflect {
    private static $plural = [
      '/(quiz)$/i'               => "$1zes",
      '/^(ox)$/i'                => "$1en",
      '/([m|l])ouse$/i'          => "$1ice",
      '/(matr|vert|ind)ix|ex$/i' => "$1ices",
      '/(x|ch|ss|sh)$/i'         => "$1es",
      '/([^aeiouy]|qu)y$/i'      => "$1ies",
      '/(hive)$/i'               => "$1s",
      '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
      '/(shea|lea|loa|thie)f$/i' => "$1ves",
      '/sis$/i'                  => "ses",
      '/([ti])um$/i'             => "$1a",
      '/(tomat|potat|ech|her|vet)o$/i'=> "$1oes",
      '/(bu)s$/i'                => "$1ses",
      '/(alias)$/i'              => "$1es",
      '/(octop)us$/i'            => "$1i",
      '/(ax|test)is$/i'          => "$1es",
      '/(us)$/i'                 => "$1es",
      '/s$/i'                    => "s",
      '/$/'                      => "s"
    ];
    
    private static $singular = [
      '/(quiz)zes$/i'             => "$1",
      '/(matr)ices$/i'            => "$1ix",
      '/(vert|ind)ices$/i'        => "$1ex",
      '/^(ox)en$/i'               => "$1",
      '/(alias)es$/i'             => "$1",
      '/(octop|vir)i$/i'          => "$1us",
      '/(cris|ax|test)es$/i'      => "$1is",
      '/(shoe)s$/i'               => "$1",
      '/(o)es$/i'                 => "$1",
      '/(bus)es$/i'               => "$1",
      '/([m|l])ice$/i'            => "$1ouse",
      '/(x|ch|ss|sh)es$/i'        => "$1",
      '/(m)ovies$/i'              => "$1ovie",
      '/(s)eries$/i'              => "$1eries",
      '/([^aeiouy]|qu)ies$/i'     => "$1y",
      '/([lr])ves$/i'             => "$1f",
      '/(tive)s$/i'               => "$1",
      '/(hive)s$/i'               => "$1",
      '/(li|wi|kni)ves$/i'        => "$1fe",
      '/(shea|loa|lea|thie)ves$/i'=> "$1f",
      '/(^analy)ses$/i'           => "$1sis",
      '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",        
      '/([ti])a$/i'               => "$1um",
      '/(n)ews$/i'                => "$1ews",
      '/(h|bl)ouses$/i'           => "$1ouse",
      '/(corpse)s$/i'             => "$1",
      '/(us)es$/i'                => "$1",
      '/s$/i'                     => ""
    ];
    
    private static $irregular = [
      'move'   => 'moves',
      'foot'   => 'feet',
      'goose'  => 'geese',
      'sex'    => 'sexes',
      'child'  => 'children',
      'man'    => 'men',
      'tooth'  => 'teeth',
      'person' => 'people'
    ];

    private static $uncountable = ['sheep', 'fish', 'deer', 'series', 'species', 'money', 'rice', 'information', 'equipment'];

    public static function pluralize($string) {
      if (in_array(strtolower($string), self::$uncountable))
        return $string;

      foreach (self::$irregular as $pattern => $result) {
        $pattern = '/' . $pattern . '$/i';
        
        if (preg_match($pattern, $string))
          return preg_replace($pattern, $result, $string);
      }
      
      foreach (self::$plural as $pattern => $result) {
        if (preg_match($pattern, $string))
          return preg_replace($pattern, $result, $string);
      }

      return $string;
    }
    
    public static function singularize($string) {
      if (in_array(strtolower($string), self::$uncountable))
        return $string;

      foreach (self::$irregular as $result => $pattern) {
        $pattern = '/' . $pattern . '$/i';
        
        if (preg_match($pattern, $string))
          return preg_replace($pattern, $result, $string);
      }

      foreach (self::$singular as $pattern => $result) {
        if (preg_match($pattern, $string))
          return preg_replace($pattern, $result, $string);
      }

      return $string;
    }
    
    public static function pluralize_if($count, $string) {
      if ($count == 1)
        return '1 ' . $string;
      else
        return $count . ' ' . self::pluralize($string);
    }
  }
}

namespace M\Core\Plugin {
  use function \M\Core\umaskChmod;
  
  use \M\Model;
  use \M\Core\Plugin;
  use \M\Core\Plugin\Uploader\Driver;
  use \M\Core\Plugin\Uploader\TinyKey;

  final class DateTime extends Plugin {
    const FORMAT_DATE = 'Y-m-d';
    const FORMAT_TIME = 'H:i:s';
    const FORMAT_DATETIME = 'Y-m-d H:i:s';

    static public function formatByType($type) {
      return $type != 'time'
        ? $type != 'date'
          ? self::FORMAT_DATETIME
          : self::FORMAT_DATE
        : self::FORMAT_TIME;
    }

    private $type;
    private $format;
    private $datetime;

    public function __construct($type) {
      $this->type = $type;

      in_array($this->type, ['time', 'date', 'datetime']) || $this->type = 'datetime';

      $this->format = self::formatByType($this->type);
    }

    public function validate() {
      return $this->value instanceof \DateTime || \DateTime::createFromFormat($this->format, $this->value) !== false;
    }

    public function timestamp() {
      return $this->datetime ? $this->datetime->getTimestamp() : null;
    }

    public function type() {
      return $this->type;
    }

    public function getFormat() {
      return $this->format;
    }

    // U -> timestamp, 'c' -> ISO 8601 date(2004-02-12T15:19:21+00:00)
    // http://php.net/manual/en/function.date.php
    public function format($format = null, $default = null) {
      return $this->datetime
        ? $this->datetime->format($format === null ? $this->format : $format)
        : $default;
    }

    public function unix($default = null) {
      return $this->datetime
        ? 0 + $this->datetime->format('U')
        : $default;
    }

    public function SQL() {
      return $this->format();
    }

    public function __toString() {
      return $this->format(null, '');
    }

    public function setValue($value = null) {
      parent::setValue($value === 'CURRENT_TIMESTAMP' ? \date(DateTime::FORMAT_DATETIME) : $value);
      $this->datetime = $this->value !== null
        ? $this->value instanceof \DateTime
          ? $this->value
          : \DateTime::createFromFormat($this->format, $this->value)
        : null;
      return $this;
    }
  }

  abstract class Uploader extends Plugin {
    abstract public function clean($save);
    
    private static $func = null;
    
    public static function func($func) {
      return self::$func = $func;
    }

    public static function randomName() {
      return md5(uniqid(mt_rand(), true));
    }

    protected $defaultURL = '';
    protected $baseURL    = '';
    protected $driver     = null;
    protected $tmpDir     = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
    protected $baseDirs   = [];

    public function __construct() {
      $func = self::$func ?? null;
      $func && $func($this);
    }

    public function SQL() {
      return $this->value;
    }

    public function __toString() {
      return (string)$this->value;
    }

    public function validate() {
      return true;
    }

    public function tmpDir($dir) {
      $this->tmpDir = rtrim($dir, DIRECTORY_SEPARATOR); $this->tmpDir && $this->tmpDir .= DIRECTORY_SEPARATOR;
      return $this;
    }

    public function baseDirs(...$dirs) {
      $this->baseDirs = $dirs;
      return $this;
    }

    public function baseURL($url) {
      $this->baseURL = rtrim($url, '/'); $this->baseURL && $this->baseURL .= '/';
      return $this;
    }

    public function driver($driver = null, $config = null) {
      $this->driver = $driver == 'S3' ? Driver::s3($config) : Driver::local($config);
      return $this;
    }

    public function default($defaultURL) {
      $this->defaultURL = $defaultURL;
      return $this;
    }

    public function defaultURL() {
      return $this->defaultURL;
    }

    public function paths() {
      return array_merge([get_class($this->model)::table()->name, $this->column],
        isset($this->model->id)
          ? TinyKey::key()
            ? str_split(TinyKey::encode($this->model->id, 6), 2)
            : str_split(sprintf('%08s', base_convert($this->model->id, 10, 36)), 4)
          : []);
    }

    public function dirs() {
      return array_merge($this->baseDirs, $this->paths());
    }

    public function put($file, $save = false) {
      if (!extract($this->putFileCheck($file)))
        return false;

      if (!$this->driver->put($tmpPath, $path = implode(DIRECTORY_SEPARATOR, array_merge($this->dirs(), [$name]))))
        return Model::writeLog('搬移至指定目錄時發生錯誤，tmpPath：' . $tmpPath . '，path：' . $path);

      @unlink($tmpPath) || Model::writeLog('移除舊資料錯誤');

      $this->model->{$this->column} = $name;

      return $save ? $this->model->save() : true;
    }

    public function url($key = '') {
      return $this->value
        ? $this->baseURL . implode('/', array_merge($this->dirs(), [$key . $this->value]))
        : $this->defaultURL();
    }

    public function saveAs($source, $key = '') {
      if (!$this->checkSetting())
        return false;

      if (!$this->driver->saveAs($path = implode(DIRECTORY_SEPARATOR, array_merge($this->dirs(), [$key . $this->value])), $source))
        return Model::writeLog('下載時發生錯誤，path：' . $path);

      return true;
    }

    protected function moveOriFile($source, $format, &$dest) {
      $dest = $this->tmpDir . 'uploader_' . static::randomName() . $format;

      if (is_uploaded_file($source['tmp_name']))
        @move_uploaded_file($source['tmp_name'], $dest);
      else {
        @rename($source['tmp_name'], $dest);
      }

      @umaskChmod($dest, 0777);

      return file_exists($dest);
    }

    protected function checkSetting() {
      if (!($this->column && $this->model))
        return Model::writeLog('未設定 Model 與 Column');

      if (!$this->driver)
        return Model::writeLog('取得 Save Driver 物件失敗');
      
      return true;
    }

    protected function download($url) {
      if (!preg_match('/^https?:\/\/.*/', $url))
        return $url;

      $format = strtolower(pathinfo($url, PATHINFO_EXTENSION));

      $curl = curl_init($url);
      curl_setopt_array($curl, [CURLOPT_URL => $url, CURLOPT_TIMEOUT => 120, CURLOPT_HEADER => false, CURLOPT_MAXREDIRS => 10, CURLOPT_AUTOREFERER => true, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_USERAGENT => 'Maple 8.0']);

      $data = curl_exec($curl);
      $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      
      $error = curl_errno($curl);
      $message = curl_error($curl);

      curl_close($curl);

      if ($error || $message)
        return Model::writeLog('無法取得圖片，網址：' . $url) ?: '';

      $write = fopen($file = $this->tmpDir . static::randomName() . ($format ? '.' . $format : ''), 'w');
      fwrite($write, $data);
      fclose($write);
      @umaskChmod($file, 0777);

      return $file;
    }

    protected function putFileCheck(&$file) {
      if (is_string($file)) {
        if (!(($file = $this->download($file)) && file_exists($file)))
          return Model::writeLog('檔案格式有誤(1)') ?: [];
        
        $file = ['name' => basename($file), 'tmp_name' => $file, 'type' => '', 'error' => '', 'size' => filesize($file)];
      }

      if (!is_array($file))
        return Model::writeLog('檔案格式有誤(3)，缺少 key：' . $key) ?: [];

      foreach (['name', 'tmp_name', 'type', 'error', 'size'] as $key)
        if (!array_key_exists($key, $file))
          return Model::writeLog('檔案格式有誤(2)，缺少 key：' . $key) ?: [];

      $pathinfo     = pathinfo($file['name']);
      $file['name'] = preg_replace("/[^a-zA-Z0-9\\._-]/", "", $file['name']);
      $format       = !empty($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';
      $file['name'] = ($pathinfo['filename'] ? $pathinfo['filename'] : static::randomName()) . $format;

      if (!$this->checkSetting())
        return [];

      if (!$this->moveOriFile($file, $format, $tmp))
        return Model::writeLog('搬移至暫存目錄時發生錯誤') ?: [];

      return [
        'name' => $file['name'],
        'format' => $format === '' ? null : $format,
        'tmpPath' => $tmp,
      ];
    }

    protected function clear($key = '') {
      return $this->driver->delete($path = implode(DIRECTORY_SEPARATOR, array_merge($this->dirs(), [$key . $this->value])))
        ? true
        : Model::writeLog('移除時發生錯誤，path：' . $path);
    }
  }
}

namespace M\Core\Plugin\Uploader {
  use \M\Model;
  use \M\Core\Plugin\Uploader;
  use \M\Core\Plugin\Uploader\Driver\S3;
  use \M\Core\Plugin\Uploader\Driver\Local;

  abstract class Driver {
    abstract public function put($source, $dest);
    abstract public function delete($path);
    abstract public function saveAs($source, $dest);

    public static function local($dir) {
      return new Local($dir);
    }

    public static function s3($config) {
      return new S3($config);
    }
  }

  final class File extends Uploader {
    public function save($file, $key = null) {
      return parent::save($file, '');
    }

    public function url($key = '') {
      return parent::url('');
    }

    public function saveAs($source, $key = '') {
      return parent::saveAs($source, '');
    }

    public function clean($save = false) {
      if (!$this->checkSetting())
        return false;

      if (!($this->isEmpty() || $this->clear('')))
        return false;

      $nullable = $this->model->table()->columns[$this->column]['nullable'] ?? false;
            
      $nullable = $this->model->table()->columns[$this->column]['nullable'] ?? false;
      $this->model->{$this->column} = $nullable ? null : '';

      return $save
        ? $this->model->save()
        : true;
    }
  }

  final class Image extends Uploader {
    const SYMBOL = '_';
    const AUTO_FORMAT = true;

    private static $thumbnail = null;
    
    public static function thumbnail($func) {
      return self::$thumbnail = $func;
    }

    private $versions = [];

    public function versions() {
      return $this->versions;
    }

    public function version($key, $method, $args = []) {
      $key !== '' && $this->versions[$key] = [$method, $args];
      return $this;
    }

    public function put($file, $save = false) {
      if (!$thumbnail = self::$thumbnail)
        return parent::put($file, $save);

      if (!$versions = $this->versions())
        return parent::put($file, $save);
      
      if (!extract($this->putFileCheck($file)))
        return false;

      $info = @exif_read_data($tmpPath);
      $orientation = $info['Orientation'] ?? 0;
      $orientation = $orientation != 6 ? $orientation != 8 ? $orientation == 3 ? 180 : 0 : -90 : 90;

      $news = [];

      try {
        $image = $thumbnail($tmpPath);
        $image->logger(function(...$args) { Model::writeLog(...$args); });

        $image->rotate($orientation);
        $name = static::randomName() . (static::AUTO_FORMAT ? $format ?? ('.' . $image->getFormat()) : '');

        foreach ($versions as $key => $params) {
          $version = $key . static::SYMBOL . $name;
          $newPath = $this->tmpDir . $version;

          if (!$this->_build(clone $image, $newPath, $params))
            return Model::writeLog('圖像處理失敗，儲存路徑：' . $newPath . '，版本：' . $key);

          array_push($news, [
            'name' => $version,
            'path' => $newPath
          ]);
        }

        $newPath = $this->tmpDir . $name;
        $image->save($newPath, true);

        array_push($news, [
          'name' => $name,
          'path' => $newPath
        ]);

      } catch (\Exception $e) {
        return Model::writeLog('圖像處理，發生意外錯誤，錯誤訊息：' . $e->getMessage());
      }

      if (count($news) != count($versions) + 1)
        return Model::writeLog('縮圖未完成，有些圖片未完成縮圖，成功數量：' . count($news) . '，版本數量：' . count($versions));

      foreach ($news as $data) {
        if (!$this->driver->put($data['path'], $path = implode(DIRECTORY_SEPARATOR, array_merge($this->dirs(), [$data['name']]))))
          return Model::writeLog('搬移至指定目錄時發生錯誤，tmpPath：' . $tmpPath . 'path：' . $path);
        
        @unlink($data['path']) || Model::writeLog('移除舊資料錯誤，path：' . $new['path']);
      }

      @unlink($tmpPath) || Model::writeLog('移除舊資料錯誤');

      $this->model->{$this->column} = $name;

      return $save ? $this->model->save() : true;
    }

    public function url($key = '') {
      in_array($key, array_keys($this->versions)) || $key = '';
      return parent::url($key ? $key . static::SYMBOL : '');
    }

    public function saveAs($source, $key = '') {
      return $key === '' || in_array($key, array_keys($this->versions))
        ? parent::saveAs($source, $key !== '' ? $key . static::SYMBOL : '')
        : false;
    }

    public function clean($save = false) {
      if (!$this->checkSetting())
        return false;

      if (!$this->isEmpty())
        foreach (array_merge(array_keys($this->versions), ['']) as $key)
          if (!$this->clear($key !== '' ? $key . static::SYMBOL : ''))
            return false;
      
      $nullable = $this->model->table()->columns[$this->column]['nullable'] ?? false;
      $this->model->{$this->column} = $nullable ? null : '';

      return $save
        ? $this->model->save()
        : true;
    }

    private function _build($image, $file, $params) {
      if (!$params)
        return $image->save($file, true);

      if (!$method = array_shift($params))
        return Model::writeLog('縮圖函式方法錯誤');

      if (!method_exists($image, $method))
        return Model::writeLog('縮圖函式沒有此方法，縮圖函式：' . $method);

      call_user_func_array([$image, $method], array_shift($params));
      return $image->save($file, true);
    }
  }

  final class TinyKey {
    private static $key = null;
    private static $digitals = null;
    private static $map = null;
    private static $width = null;
    private static $height = null;

    public static function key($key = null) {
      return is_string($key)
        ? self::$key = $key
        : self::$key;
    }

    public static function encode($number, $zero = 0) {
      $map = self::_map();

      $res = '';
      $i = 0;

      do {
        $r = $number % self::$width;

        $number = floor($number / self::$width);
        $res = $map[$i++ % self::$height][$r] . $res;
      } while ($number);

      // Pad
      $len = strlen($res);
      $diff = $len - $zero;

      while ($diff++ < 0 && $len++)
        $res = $map[($len - 1) % self::$height][0] . $res;

      return $res;
    }

    public static function decode($str) {
      $map = self::_map();

      $limit = strlen($str);
      $res = 0;
      $i = $limit;

      while ($i--) {
        $res = self::$width * $res + strpos($map[$i % self::$height], $str[$limit - $i - 1]);
      }

      return (int)$res;
    }

    private static function _map() {
      if (self::$map !== null)
        return self::$map;
      
      self::$digitals = implode('', array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'), ['-', '_']));
      
      preg_match('/^[0-9a-f]+$/', self::$key)
        || self::$key = '';

      $tmp = ($len = strlen(self::$key))
        ? array_map(function($i) use ($len) { return self::$key[$i % $len]; }, array_keys(array_fill(0, strlen(self::$digitals), null)))
        : array_fill(0, strlen(self::$digitals), 0);

      $map = array_map('self::_rule', $tmp);
      self::$width = strlen($map[0]);
      self::$height = count($map);

      return self::$map = $map;
    }

    private static function _rule($salt = 0) {
      $digitals = self::$digitals;

      if (!$salt)
        return $digitals;

      $digitals = array_chunk(str_split($digitals), $salt = hexdec($salt));
      $len  = count($digitals);
      $half = floor(count($digitals) / 2);

      for ($i = 0; $i < $half && ($t = $len - $i - 1); $i += 2)
        list($digitals[$i], $digitals[$t]) = [$digitals[$t], $digitals[$i]];

      $digitals = str_split(implode('', array_map(function($dig) { return implode('', $dig); }, $digitals)));

      while ($salt--)
        array_push($digitals, array_shift($digitals));

      return implode('', $digitals);
    }
  }
}

namespace M\Core\Plugin\Uploader\Driver {
  use function \M\Core\umaskChmod;
  use function \M\Core\umaskMkdir;

  use \M\Core\Plugin\Uploader\Driver;

  final class Local extends Driver {
    private $dir = null;

    public function __construct($config) {
      $this->dir = $config['dir'] ?? null;
    }

    public function put($source, $dest) {
      if (!(is_file($source) && is_readable($source)))
        return false;

      $path = pathinfo($dest = $this->dir . $dest, PATHINFO_DIRNAME);

      file_exists($path) || @umaskMkdir($path, 0777, true);

      if (!(is_dir($path) && is_writable($path) && @copy($source, $dest)))
        return false;

      @umaskChmod($dest, 0777);
      return file_exists($path);
    }

    public function delete($path) {
      $path = $this->dir . $path;
      file_exists($path) && @unlink($path);
      return !file_exists($path);
    }

    public function saveAs($source, $dest) {
      $source = $this->dir . $source;
      @copy($source, $dest);
      return file_exists($dest);
    }
  }

  final class S3 extends Driver {
    private static $extensions = ['jpg' => ['image/jpeg', 'image/pjpeg'], 'gif' => ['image/gif'], 'png' => ['image/png', 'image/x-png']];

    public static function extensions($extensions) {
      return self::$extensions = $extensions;
    }

    private $bucket       = null;
    private $access       = null;
    private $secret       = null;
    private $acl          = 'public-read';
    private $ttl          = 0;
    private $isUseSSL     = false;
    private $isVerifyPeer = false;

    public function __construct($config) {
      $this->bucket       = $config['bucket'] ?? null;
      $this->access       = $config['access'] ?? null;
      $this->secret       = $config['secret'] ?? null;
      $this->isUseSSL     = $config['isUseSSL'] ?? false;
      $this->isVerifyPeer = $config['isVerifyPeer'] ?? false;
      $this->acl          = $config['acl'] ?? 'public-read';
      $this->ttl          = $config['ttl'] ?? 0;
    }

    public function put($source, $dest) {
      if (!(is_file($source) && is_readable($source)))
        return false;

      $dest = $dest ? '/' . rawurlencode($dest) : '/';
      $host = $this->bucket . '.s3.amazonaws.com';
      $date = gmdate('D, d M Y H:i:s T');
      $md5  = base64_encode(md5_file($source, true));
      $type = $this->_getMimeByExtension($source);
      $size = filesize($source);
      $source = @fopen($source, 'rb');

      $url  = ($this->isUseSSL && extension_loaded('openssl') ? 'https://' : 'http://') . $host . $dest;

      $headers = ['x-amz-acl: ' . $this->acl, 'Host: ' . $host, 'Date: ' . $date, 'Content-MD5: ' . $md5, 'Content-Type: ' . $type];
      $this->ttl && $this->ttl > 0 && array_push($headers, 'Cache-Control: max-age=' . $this->ttl);
      array_push($headers, 'Authorization: AWS ' . $this->access . ':' . $this->_hashCode($this->secret, implode("\n", ['PUT', $md5, $type, $date, 'x-amz-acl:' . $this->acl, '/' . $this->bucket . $dest])));

      $options = [
        CURLOPT_URL => $url,
        CURLOPT_PUT => true,
        CURLOPT_INFILE => $source,
        CURLOPT_HEADER => false,
        CURLOPT_INFILESIZE => $size,
        CURLOPT_USERAGENT => 'S3/php',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_WRITEFUNCTION => function($curl, $data) { return strlen($data); },
        CURLOPT_HEADERFUNCTION => function($curl, $data) { return strlen($data); },
      ];

      $this->isUseSSL && $options[CURLOPT_SSL_VERIFYHOST] = true;
      $this->isUseSSL && $options[CURLOPT_SSL_VERIFYPEER] = $this->isVerifyPeer ? true : false;

      $curl = curl_init();
      curl_setopt_array($curl, $options);
      
      $code    = null;
      $message = null;

      if (curl_exec($curl)) {
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      } else {
        $code = curl_errno($curl);
        $message = curl_error($curl);
      }
      curl_close($curl);
      is_resource($source) && fclose($source);

      return $code >= 200 && $code <= 299 && $message === null;
    }

    public function delete($path) {
      $path = $path ? '/' . rawurlencode($path) : '/';
      $host = $this->bucket . '.s3.amazonaws.com';
      $date = gmdate('D, d M Y H:i:s T');
      $url  = ($this->isUseSSL && extension_loaded('openssl') ? 'https://' : 'http://') . $host . $path;
      $headers = ['Host: ' . $host, 'Date: ' . $date];
      array_push($headers, 'Authorization: AWS ' . $this->access . ':' . $this->_hashCode($this->secret, implode("\n", ['DELETE', '', '', $date, '/' . $this->bucket . $path])));

      $options = [
        CURLOPT_URL => $url,
        CURLOPT_HEADER => false,
        CURLOPT_USERAGENT => 'S3/php',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_WRITEFUNCTION => function($curl, $data) { return strlen($data); },
        CURLOPT_HEADERFUNCTION => function($curl, $data) { return strlen($data); },
      ];

      $this->isUseSSL && $options[CURLOPT_SSL_VERIFYHOST] = true;
      $this->isUseSSL && $options[CURLOPT_SSL_VERIFYPEER] = $this->isVerifyPeer ? true : false;

      $curl = curl_init();
      curl_setopt_array($curl, $options);
      
      $code    = null;
      $message = null;

      if (curl_exec($curl)) {
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      } else {
        $code = curl_errno($curl);
        $message = curl_error($curl);
      }
      curl_close($curl);

      return $code >= 200 && $code <= 299 && $message === null;
    }

    public function saveAs($source, $dest) {
      $source = $source ? '/' . rawurlencode($source) : '/';
      $host = $this->bucket . '.s3.amazonaws.com';
      $date = gmdate('D, d M Y H:i:s T');
      $url  = ($this->isUseSSL && extension_loaded('openssl') ? 'https://' : 'http://') . $host . $source;
      $headers = ['Host: ' . $host, 'Date: ' . $date];
      array_push($headers, 'Authorization: AWS ' . $this->access . ':' . $this->_hashCode($this->secret, implode("\n", ['GET', '', '', $date, '/' . $this->bucket . $source])));

      $dest = @fopen($dest, 'wb');

      $options = [
        CURLOPT_URL => $url,
        CURLOPT_HEADER => false,
        CURLOPT_USERAGENT => 'S3/php',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_WRITEFUNCTION => function($curl, $data) use ($dest) { fwrite($dest, $data); return strlen($data); },
        CURLOPT_HEADERFUNCTION => function($curl, $data) { return strlen($data); },
      ];

      $this->isUseSSL && $options[CURLOPT_SSL_VERIFYHOST] = true;
      $this->isUseSSL && $options[CURLOPT_SSL_VERIFYPEER] = $this->isVerifyPeer ? true : false;

      $curl = curl_init();
      curl_setopt_array($curl, $options);
      
      $code    = null;
      $message = null;

      if (curl_exec($curl)) {
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      } else {
        $code = curl_errno($curl);
        $message = curl_error($curl);
      }
      curl_close($curl);
      is_resource($dest) && fclose($dest);

      return $code >= 200 && $code <= 299 && $message === null;
    }

    private function _hashCode($secret, $string) {
      return base64_encode(extension_loaded('hash')
        ? hash_hmac('sha1', $string, $secret, true)
        : pack('H*', sha1((str_pad($secret, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) . pack('H*', sha1((str_pad($secret, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $string)))));
    }

    private function _getMimeByExtension($file) {
      return (self::$extensions[strtolower(pathinfo($file, PATHINFO_EXTENSION))] ?? ['text/plain'])[0];
    }
  }
}
