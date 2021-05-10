<?php

namespace M {
  use \M\Core\Where;
  use \M\Core\Table;
  use \M\Core\Connection;
  use \M\Core\Config;
  use \M\Core\Plugin;
  use \M\Core\Inflect;
  use \M\Core\Plugin\DateTime;
  use \M\Core\Plugin\Uploader;
  use \M\Core\Plugin\Uploader\File;
  use \M\Core\Plugin\Uploader\Image;
  use \M\Core\Plugin\Uploader\TinyKey;
  use \M\Core\Plugin\Uploader\Driver\S3;

  function options($options) {
    /* Model::find('one', null); */
    is_array($options) && count($options)
      && $options[0] === ''
      && array_shift($options);

    /* Model::find('one', null); */
    is_array($options) && count($options)
      && $options[0] === null
      && $options[0] = ['where' => Where::create('id = ?', null)];

    /* Model::find('one', 1); */
    isset($options[0])
      && is_numeric($options[0])
      && $options[0] = ['where' => Where::create('id = ?', $options[0])];

    /* Model::count(Where::create('id = ?', 2)); */
    isset($options[0])
      && $options[0] instanceof Where
      && $options[0] = ['where' => $options[0]];

    /* Model::count('id = ?', 2); */
    isset($options[0])
      && is_string($options[0])
      && $options[0] = ['where' => Where::create(...$options)];

    /* 以下為正規格式 ['select' => 'id', 'where' => ...] */
    $options = $options ? array_shift($options) : [];

    /* Model::count(['where' => 'id = 2']); */
    isset($options['where'])
      && is_string($options['where'])
      && $options['where'] = Where::create($options['where']);
    
    /* Model::count(['where' => ['id = ?', 2]]); */
    isset($options['where'])
      && is_array($options['where'])
      && $options['where'] = Where::create(...$options['where']);

    return $options;
  }

  function reverseOrder($order) {
    return trim($order) ? implode(', ', array_map(function($part) {
      $v = trim(strtolower($part));

      return strpos($v,' asc') === false
        ? strpos($v,' desc') === false
          ? $v . ' DESC'
          : preg_replace('/desc/i', 'ASC', $v)
        : preg_replace('/asc/i', 'DESC', $v);
    }, explode(',', $order))) : 'order';
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

    if ($plugin) return (clone $plugin)->setModel($model)->setColumn($column)->setValue($val);

    switch ($format['type']) {
      case 'tinyint': case 'smallint': case 'mediumint': case 'int': case 'bigint': return columnInt($val); 
      case 'float': case 'double': case 'numeric': case 'decimal': case 'dec': return columnFloat($val); 
      case 'datetime': case 'timestamp': case 'date': case 'time': return columnDateTime($format['type'], $val); 
      case 'json': return $val !== null ? @json_decode($val, true) : null;
      case 'enum': return columnEnum($val, $format);
      default: return columnString($val);
    }

    return $val;
  }

  function columnUpdate($format, $oldVal, $newVal) {
    columnNull($format, $newVal);

    if ($oldVal instanceof Plugin) return $oldVal->setValue($newVal);

    switch ($format['type']) {
      case 'tinyint': case 'smallint': case 'mediumint': case 'int': case 'bigint': return columnInt($newVal); 
      case 'float': case 'double': case 'numeric': case 'decimal': case 'dec': return columnFloat($newVal); 
      case 'datetime': case 'timestamp': case 'date': case 'time': return columnDateTime($format['type'], $newVal); 
      case 'json': return $newVal;
      case 'enum': return columnEnum($newVal, $format);
      default: return columnString($newVal);
    }

    return $newVal;
  }

  function columnNull($format, $val) {
    $val === null && !$format['nullable'] && !$format['auto'] && Model::error('「' . $format['field'] . '」欄位不可以為 NULL');
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

  function columnDateTime($type, $val) {
    return DateTime::create($type)->setValue($val);
  }

  function columnString($val) {
    return $val !== null ? (string)$val : null;
  }

  function columnEnum($val, $format) {
    if ($val === null) return null;
    in_array((string)$val, $format['vals']) || Model::error('「' . $format['field'] . '」欄位格式為「' . $format['type'] . '」，選項有「' . implode('、', $format['vals']) . '」，您給予的值為：' . $val . '，不在選項內');

    return (string)$val;
  }

  function toArray($obj) {
    $attrs = [];
    $hides = array_fill_keys($obj->table()->className::$hides ?? [], '1');
    
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

  function relation(&$relation) {
    $type = null;
    $model = null;

    if (is_string($relation)) {
      $relation = trim($relation);
      stripos($relation, '<=') !== false || stripos($relation, '<-') !== false ||  stripos($relation, '=>') !== false ||  stripos($relation, '->') !== false ||  $relation = '<= ' . $relation;
      
      if (!preg_match_all('/^(?P<key1>([A-Za-z][A-Za-z0-9_]*)?)\s*(?P<type>([=-]>|<[=-])+)\s*(?P<model>[A-Za-z][A-Za-z0-9_]*)\.?(?P<key2>([A-Za-z][A-Za-z0-9_\-]*)?)$/', $relation, $result))
        return false;

      if (empty($result['type'][0]) || empty($result['model'][0]))
        return false;

      $relation = [$result['type'][0] => $result['model'][0]];

      if (!empty($result['key1'][0]))
        $relation[in_array($result['type'][0], ['<=', '<-']) ? 'primary' : 'foreign'] = $result['key1'][0];

      if (!empty($result['key2'][0]))
        $relation[in_array($result['type'][0], ['<=', '<-']) ? 'foreign' : 'primary'] = $result['key2'][0];
    }

    $keys = [
      '<=' => 'hasMany',
      '<-' => 'hasOne',
      '=>' => 'belongToMany',
      '->' => 'belongToOne',
      'hasMany'      => 'hasMany',
      'hasOne'       => 'hasOne',
      'belongToMany' => 'belongToMany',
      'belongToOne'  => 'belongToOne'];
    
    foreach ($keys as $key => $method)
      if (isset($relation[$key])) {
        $type = $method;
        $model = $relation[$key];
        unset($relation[$key]);
      }

    if (!isset($type, $model))
      return false;

    $relation = options([$relation]);

    $relation = [
      'type'      => in_array($type, ['hasMany', 'hasOne']) ? 'has' : 'belong',
      'method'    => in_array($type, ['hasMany', 'belongToMany']) ? 'all' : 'one',
      'modelName' => $model,
      'options'   => $relation,
    ];

    return true;
  }

  function foreign($tableName, $foreign = null) {
    return $foreign === null
      ? Model::case() == Model::CASE_SNAKE
        ? (Inflect::singularize($tableName) . '_id')
        : (lcfirst($tableName) . 'Id')
      : $foreign;
  }

  function getNamespaces($className) {
    return array_slice(explode('\\', $className), 0, -1);
  }

  function deNamespace($className) {
    $className = array_slice(explode('\\', $className), -1);
    return array_shift($className);
  }

  function transaction($closure, &$errors = []) {
    $instance = Connection::instance();

    try {
      if (!$instance->beginTransaction()) {
        $errors = ['Transaction 失敗'];
        return null;
      }
      
      if ($result = $closure()) {
        if ($instance->commit()) return $result;
        $errors = ['Commit 失敗'];
        return null;
      }

      throw new \Exception('transaction 回傳 false，故 rollback');
    } catch (\Exception $e) {
      $errors = $instance->rollback()
        ? [$e->getMessage()]
        : ['Rollback 失敗', $e->getMessage()];

      return null;
    }

    return ['不明原因錯誤！'];
  }

  abstract class Model {
    const CASE_CAMEL = 'Camel';
    const CASE_SNAKE = 'Snake';

    static $createAt = 'createAt';
    static $updateAt = 'updateAt';

    public static  $validOptions = ['where', 'limit', 'offset', 'order', 'select', 'group', 'having', 'pre-relation', 'relation', 'pre'];
    
    private static $logger = null;
    private static $errorFunc = null;
    private static $caches = [];
    private static $dirs   = null;
    private static $case   = Model::CASE_CAMEL;

    public static function where(...$args) {
      return Where::create(...$args)->setModel(static::class);
    }

    public static function one(...$args) {
      return self::single('one', ...$args);
    }

    public static function first(...$args) {
      return self::single('first', ...$args);
    }

    public static function last(...$args) {
      return self::single('last', ...$args);
    }

    public static function all(...$args) {
      return call_user_func_array(['static', 'find'], array_merge(['all'], $args));
    }

    public static function table() {
      return Table::instance(static::class);
    }

    public static function truncate() {
      return static::table()->truncate();
    }

    public static function deleteAll(...$args) {
      return static::table()->deleteAll(options($args));
    }

    public static function closeDB() {
      return Connection::close();
    }

    public static function uploader($column, $class) {
      $class = '\\M\\Core\\Plugin\\Uploader\\' . $class;
      return self::table()->plugins[$column] = $class::create();
    }

    public static function find(...$options) {
      $className = static::class;
      $method = array_shift($options);

      in_array($method, $methods = ['one', 'first', 'last', 'all'])
        || Model::error($className . ' 僅能使用 ' . implode('、', $methods) .' 類型');

      $options = options($options);

      $method == 'last'
        && $options['order'] = isset($options['order'])
          ? reverseOrder($options['order'])
          : implode(' DESC, ', static::table()->primaries) . ' DESC';

      $options
        && $options = array_intersect_key($options, array_flip(Model::$validOptions));
      
      in_array($method, ['one', 'first'])
        && $options = array_merge($options, ['limit' => 1]);

      return static::table()->find($options);
    }

    public static function create($attrs = [], $allow = []) {
      $allow && $attrs = array_intersect_key($attrs, array_flip($allow));
      $className = static::class;
      $model = new $className($attrs, true);
      return $model->save();
    }

    public static function creates($rows = [], $limit = 100) {
      $result = true;

      if (!$rows)
        return $result;
      
      foreach (array_chunk($rows, $limit) as $page)
        $result && $result = static::table()->inserts(array_map(function($row) {
          $attrs = [];
          foreach (static::attrDefaults($row) as $name => $value)
            $attrs[$name] = columnInit(static::table()->columns[$name], $value, static::table()->plugins[$name] ?? null);
          return $attrs;
        }, $page)); 

      return $result;
    }

    public static function count(...$options) {
      $options = options($options);
      $options['select'] = 'COUNT(*)';
      unset($options['group']);

      if (!$objs = call_user_func_array(['static', 'find'], ['all', $options])) return 0;
      if (!$objs = array_shift($objs)) return 0;
      if (!$objs = $objs->attrs()) return 0;
      return intval(array_shift($objs));
    }
    
    public static function updateAll($sets, ...$options) {
      $attrs = [];
      foreach (static::attrDefaults($sets, false) as $name => $value)
        $attrs[$name] = columnInit(static::table()->columns[$name], $value, static::table()->plugins[$name] ?? null);

      if (!array_key_exists(static::$createAt, $sets))
        unset($attrs[static::$createAt]);

      return static::table()->updateAll($attrs, options($options));
    }

    public static function relations($name, $relation, $objs) {
      $method  = $relation['method'];
      $options = $relation['options'];
      
      $className = '\\M\\' . $relation['modelName'];
      $primary = $options['primary'] ?? 'id';
      $tmps = [];

      if ($relation['type'] == 'has') {
        $foreign = foreign(static::table()->tableName, $options['foreign'] ?? null);

        foreach ($objs as $obj)
          $tmps[$obj->$primary] = true;
        
        $where = Where::create(quoteName($foreign) . ' IN (?)', array_keys($tmps));

        $options['where'] = isset($options['where']) ? $options['where']->and($where) : $where;
        isset($options['select']) && $options['select'] .= ',' . $foreign;
        $options = array_intersect_key($options, array_flip(Model::$validOptions));
        
        $relations = $className::all($options);

        $tmps = [];
        foreach ($relations as $relation) {
           $tmps[$relation->$foreign] ?? $tmps[$relation->$foreign] = [];
           array_push($tmps[$relation->$foreign], $relation);
        }

        foreach ($objs as $obj) {
          $tmps[$obj->$primary] ?? $tmps[$obj->$primary] = [];
          $obj->relations[$name] = $method == 'one' ? $tmps[$obj->$primary][0] : $tmps[$obj->$primary];
        }

        return $objs;
      }

      if ($relation['type'] == 'belong') {
        $foreign = foreign($className::table()->tableName, $options['foreign'] ?? null);
        
        foreach ($objs as $obj)
          $tmps[$obj->$foreign] = true;

        $where = Where::create(quoteName($primary) . ' IN (?)', array_keys($tmps));
        $options['where'] = isset($options['where']) ? $options['where']->and($where) : $where;
        isset($options['select']) && $options['select'] .= ',' . $primary;
        $options = array_intersect_key($options, array_flip(Model::$validOptions));

        $relations = $className::all($options);

        $tmps = [];
        foreach ($relations as $relation) {
          $tmps[$relation->$primary] ?? $tmps[$relation->$primary] = [];
          array_push($tmps[$relation->$primary], $relation);
        }

        foreach ($objs as $obj) {
          $tmps[$obj->$foreign] ?? $tmps[$obj->$foreign] = [];
          $obj->relations[$name] = $method == 'one' ? $tmps[$obj->$foreign][0] ?? null : $tmps[$obj->$foreign];
        }

        return $objs;
      }
    }

    public static function queryLogger($func) {
      return Connection::logger($func);
    }

    public static function logger($func) {
      return self::$logger = $func;
    }

    public static function log(...$args) {
      $logger = self::$logger; $logger && $logger(...$args);
      return false;
    }

    public static function cacheFunc($type, $func) {
      return self::$caches[$type] = $func;
    }

    public static function cache($type, $key, $closure) {
      $cacheFunc = self::$caches[$type] ?? null;
      return $cacheFunc ? $cacheFunc(...[$key, $closure]) : $closure();
    }
    
    public static function thumbnail($func) {
      return Image::thumbnail($func);
    }

    public static function setUploader($func) {
      return Uploader::func($func);
    }

    public static function config($key = '', $config = []) {
      return Connection::$configs[$key] = new Config($config);
    }
    
    public static function errorFunc($func) {
      return self::$errorFunc = $func;
    }
    
    public static function error(...$args) {
      $errorFunc = self::$errorFunc; $errorFunc ? $errorFunc(...$args) : var_dump($args); exit(1);
    }
    
    public static function case($case = null) {
      return $case !== null
        ? self::$case = in_array($case, [Model::CASE_CAMEL, Model::CASE_SNAKE])
          ? $case
          : self::$case
        : self::$case;
    }
    
    public static function salt($key = null) {
      return TinyKey::key($key);
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

    public static function extensions($extensions) { return S3::extensions($extensions); }

    private static function single($type, ...$args) {
      $list = call_user_func_array(['static', 'find'], array_merge([$type], $args));
      return $list ? array_shift($list) : null;
    }

    private static function attrDefaults($attrs, $full = true) {
      $columns = static::table()->columns;

      isset($columns[static::$createAt])
        && !array_key_exists(static::$createAt, $attrs)
        && $attrs[static::$createAt] = \date(DateTime::formatByType($columns[static::$createAt]['type']));
      
      isset($columns[static::$updateAt])
        && !array_key_exists(static::$updateAt, $attrs)
        && $attrs[static::$updateAt] = \date(DateTime::formatByType($columns[static::$updateAt]['type']));

      $newAttrs = [];
      foreach ($columns as $key => $column) {

        if (!($full || isset($attrs[$key])))
          continue;

        array_key_exists($key, $attrs) || $attrs[$key] = $column['default'];

        $column['nullable'] || isset($attrs[$key]) || $column['auto'] || Model::error($key . ' 不可以為 null');

        $newAttrs[$key] = $column['type'] != 'json'
          ? $attrs[$key] ?? null
          : @json_encode($attrs[$key]);
      }
    
      return $newAttrs;
    }

    private $attrs     = [];
    private $dirty     = [];
    private $relations = [];
    private $isNew     = true;

    public function __construct($attrs = [], $isNew = true) {
      $this->isNew = $isNew;
      
      $isNew && $attrs = static::attrDefaults($attrs);
      foreach ($attrs as $name => $value)
        $this->attrs[$name] = isset(static::table()->columns[$name]) ? columnInit(static::table()->columns[$name], $value, static::table()->plugins[$name] ?? null, $this, $name) : $value;
    }
    
    public function __set($name, $value) {
      return array_key_exists($name, $this->attrs) && isset(static::table()->columns[$name])
        ? $this->updateAttr($name, $value)
        : Model::error('找不到「' . $name . '」變數');
    }

    public function &__get($name) {
      if (array_key_exists($name, $this->attrs))
        return $this->attrs[$name];

      if (array_key_exists($name, $this->relations))
        return $this->relations[$name];

      $className = static::class;
      isset($className::$relations[$name]) && $this->relations[$name] = $this->relation($name, $method, $options)->$method($options);

      if (array_key_exists($name, $this->relations))
        return $this->relations[$name];

      return Model::error('找不到「' . $name . '」變數');
    }

    public function __isset($name) {
      return array_key_exists($name, $this->attrs);
    }

    public function save() {
      return $this->isNew ? $this->insert() : $this->update();
    }

    public function delete() {
      if (!$primaries = $this->primaries())
        return Model::log('刪除資料失敗，錯誤原因：找不到 Primary Key');

      if (!static::table()->delete($primaries))
        return false;

      $afterDeletes = static::$afterDeletes ?? [];
      is_array($afterDeletes) || $afterDeletes = [$afterDeletes];

      $return = $this;
      foreach ($afterDeletes as $afterDelete) {
        method_exists($this, $afterDelete) || Model::error('Model「' . static::class . '」內沒有名為「' . $afterDelete . '」的 method');

        if (!$return = $this->$afterDelete($return))
          return Model::log('Model「' . static::class . '」執行「' . $afterDelete . '」after create 失敗');
      }
      return true;
    }
    
    public function attrs($key = null, $default = null) {
      return $key !== null ? array_key_exists($key, $this->attrs) ? $this->attrs[$key] : $default : $this->attrs;
    }

    public function toArray() {
      return toArray($this);
    }

    public function relation($key, &$method = null, &$options = null) {
      $className = static::class;

      $relation = $className::$relations[$key] ?? Model::error('未設定 ' . $key . ' 的關聯結構');
      relation($relation) || Model::error('關聯結構錯誤');

      $type       = $relation['type'];
      $method     = $relation['method'];
      $modelName  = $relation['modelName'];
      $options    = $relation['options'];

      $className = '\\M\\' . $relation['modelName'];
      $primary = $options['primary'] ?? 'id';

      if ($type == 'has') {
        $foreign = foreign(static::table()->tableName, $options['foreign'] ?? null);
        $where = $className::where(quoteName($foreign) . ' = ?', $this->$primary);
      } else {
        $foreign = foreign($className::table()->tableName, $options['foreign'] ?? null);
        $where = $className::where(quoteName($primary) . ' = ?', $this->$foreign);
      }

      if (isset($options['where'])) {
        $where->and($options['where']);
        unset($options['where']);
      }

      $options = array_intersect_key($options, array_flip(Model::$validOptions));

      return $where;
    }

    public function set($attrs = [], $allow = [], $save = false) {
      $allow === true  && ($save = $allow) && $allow = [];
      $allow === false && !($save = $allow) && $allow = [];
      $allow && $attrs = array_intersect_key($attrs, array_flip($allow));

      foreach ($attrs as $key => $val)
        $this->$key = $val;

      return $save ? $this->save() : $this;
    }

    private function updateAttr($name, $value) {
      $this->attrs[$name] = columnUpdate(static::table()->columns[$name], $this->attrs[$name], $value);
      $this->flagDirty($name);
      return $this->attrs[$name];
    }

    private function flagDirty($name = null) {
      $this->dirty[$name] = true;
      return $this;
    }

    private function cleanFlagDirty() {
      $this->dirty = [];
      return $this;
    }
    
    private function update() {
      if (!$primaries = $this->primaries())
        return Model::log('更新資料失敗，錯誤原因：找不到 Primary Key') ?: null;

      if (!$dirties = array_intersect_key($this->attrs, $this->dirty))
        return $this;

      if (isset(static::table()->columns[static::$updateAt]) && array_key_exists(static::$updateAt, $this->attrs) && !array_key_exists(static::$updateAt, $this->dirty)) {
        $this->updateAttr(static::$updateAt, \date(DateTime::formatByType(static::table()->columns[static::$updateAt]['type'])));
      }

      return static::table()->update(array_intersect_key($this->attrs, $this->dirty), $primaries)
        ? $this
        : null;
    }

    private function insert() {
      $this->attrs = array_intersect_key($this->attrs, static::table()->columns);

      if (!static::table()->insert($this->attrs))
        return null;

      foreach (static::table()->primaries as $primary)
        if (isset(static::table()->columns[$primary]) && static::table()->columns[$primary]['auto'])
          $this->attrs[$primary] = (int)Connection::instance()->lastInsertId();

      $this->isNew = false;

      $afterCreates = static::$afterCreates ?? [];
      is_array($afterCreates) || $afterCreates = [$afterCreates];

      $return = $this;
      foreach ($afterCreates as $afterCreate) {
        method_exists($this, $afterCreate) || Model::error('Model「' . static::class . '」內沒有名為「' . $afterCreate . '」的 method');

        if (!$return = $this->$afterCreate($return))
          return Model::log('Model「' . static::class . '」執行「' . $afterCreate . '」after create 失敗') ?: null;
      }

      return $this;
    }

    private function primaries() {
      $tmp = [];

      foreach (static::table()->primaries as $primary)
        if (array_key_exists($primary, $this->attrs))
          $tmp[$primary] = $this->attrs[$primary];

      return $tmp;
    }
  }
};

namespace M\Core {
  use function \M\options;
  use function \M\relation;
  use function \M\quoteName;
  use function \M\deNamespace;
  use function \M\columnFormat;

  use \M\Model;
  use \M\Core\Plugin\DateTime;
  use \M\Core\Plugin\Uploader;

  function arrayFlatten($arr) {
    $new = [];

    foreach ($arr as $val)
      if (is_array($val))
        $new = array_merge($new, arrayFlatten($val));
      else
        array_push($new, $val);

    return $new;
  }

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

  final class Where {
    public static function create(...$args) {
      return new static(...$args);
    }

    private static function validator(...$vals) {
      $raw  = array_shift($vals);

      is_numeric($raw) && $raw && ($vals = [$raw]) && $raw = 'id = ?';

      is_array($raw) && ($vals = [$raw]) && $raw = 'id IN (?)';

      $i = 0;
      $raw = preg_replace_callback('/(?P<key>\(\s*\?\s*\)|\?)/', function($m) use (&$i, &$vals) {
        if ($m['key'] == '?') { $i++; return '?'; }
        $val = $vals[$i];
        is_array($val) || Model::error('Where 條件錯誤，(?) 相對應的參數必須為陣列');
        $c = count($vals[$i]); $vals[$i] = $c ? $vals[$i] : null; $i++;
        return '(' . ($c ? implode(',', array_fill(0, $c, '?')) : '?') . ')';
      }, $raw);

      $vals = arrayFlatten($vals);
      $count = substr_count($raw, '?');
      $count <= count($vals) || Model::error('Where 條件錯誤，「' . $raw . '」 有 ' . count($vals) . ' 個參數，目前只給 ' . $count . ' 個');
      return [$raw, array_slice($vals, 0, $count)];
    }

    private $model = null;
    private $raw   = '';
    private $vals  = [];

    public function __construct(...$args) {
      $rawVals    = static::validator(...$args);
      $this->raw  = array_shift($rawVals);
      $this->vals = array_shift($rawVals);
    }

    public function setModel($model) {
      $this->model = $model; return $this;
    }

    public function getRaw() {
      return $this->raw;
    }

    public function getVals() {
      return $this->vals;
    }

    public function and(...$args) {
      return $this->merge('AND', ...$args);
    }

    public function or(...$args) {
      return $this->merge('OR', ...$args);
    }

    public function merge($type, ...$args) {
      if ($args && $args[0] instanceof static) {
        $rows  = array_filter([$this->raw, $args[0]->getRaw()], function($t) { return $t !== ''; });
        $valss = array_merge([$this->vals, $args[0]->getVals()]);
      } else {
        $rawVals = static::validator(...$args);
        $rows  = array_filter([$this->raw, array_shift($rawVals)], function($t) { return $t !== ''; });
        $valss = array_merge([$this->vals], [array_shift($rawVals)]);
      }

      $this->raw  = implode(' ' . $type . ' ', array_map(function($row) { return '(' . $row . ')'; }, $rows));
      $this->vals = array_reduce($valss, function($a, $b) { return array_merge($a, $b); }, []);
      return $this;
    }

    public function one(...$args) {
      isset($args[0]) && is_string($args[0]) && $args = [['select' => $args[0]]];
      return $this->get('one', ...$args);
    }

    public function first(...$args) {
      isset($args[0]) && is_string($args[0]) && $args = [['select' => $args[0]]];
      return $this->get('first', ...$args);
    }

    public function last(...$args) {
      isset($args[0]) && is_string($args[0]) && $args = [['select' => $args[0]]];
      return $this->get('last', ...$args);
    }

    public function all(...$args) {
      isset($args[0]) && is_string($args[0]) && $args = [['select' => $args[0]]];
      return $this->get('all', ...$args);
    }

    public function count(...$args) {
      return $this->get('count', ...$args);
    }

    public function delete(...$args) {
      return $this->get('deleteAll', ...$args);
    }

    public function update(...$args) {
      isset($args['where']) ? $args['where']->and($this) : $args['where'] = $this;
      return call_user_func_array([$this->model, 'updateAll'], $args);
    }

    private function get($type, ...$args) {
      $args = options($args);
      isset($args['where']) ? $args['where']->and($this) : $args['where'] = $this;
      return call_user_func_array([$this->model, $type], [$args]);
    }
  }

  final class Table {
    private static $instances = [];

    public static function instance($className) {
      return self::$instances[$className] ?? self::$instances[$className] = new Table($className);
    }

    public $columns   = [];
    public $primaries = [];
    public $plugins   = [];

    private function __construct($className) {
      $this->className = $className;
      $this->tableName = $className::$tableName ?? deNamespace($className);
      $this->getMetaData()->setPrimaries();
    }

    public function find($options) {
      $sql = SQLBuilder::select(quoteName($this->tableName), $options);

      return $this->findBySQL(
        $sql->getRaw(),
        $sql->getVals(),
        $options['pre-relation'] ?? $options['relation'] ?? $options['pre'] ?? []);
    }

    public function findBySQL($sql, $values = [], $preRelations = []) {
      $sth = null;
      $objs = [];

      if ($error = Connection::instance()->query($sql, $values, $sth))
        return Model::log('查詢資料庫錯誤，錯誤原因：' . $error) ?: $objs;

      if (!$objs = array_map(function($row) { return new $this->className($row, false); }, $sth->fetchAll()))
        return $objs;

      is_array($preRelations) || $preRelations = [$preRelations];

      foreach ($preRelations as $preRelation) {
        $preRelation = explode('.', $preRelation);
        $name = array_shift($preRelation);

        if (!isset($this->className::$relations[$name]))
          continue;

        $relation = $this->className::$relations[$name];
        relation($relation) || Model::error('關聯結構錯誤');
        $preRelation && $relation['options'] = array_merge($relation['options'], ['pre-relation' => implode('.', $preRelation)]);
        $this->className::relations($name, $relation, $objs);
      }

      return $objs;
    }

    public function update($datas, $primaries) {
      $datas = $this->attrsToStrings($datas);
      $where = $this->primariesToWhere($primaries);

      if (!$sql = SQLBuilder::update(quoteName($this->tableName), $datas, ['where' => $where])) {
        return Model::log('更新資料失敗，錯誤原因：SQL Builder 錯誤');
      }

      if ($error = Connection::instance()->query($sql->getRaw(), $sql->getVals())) {
        return Model::log('更新資料失敗，錯誤原因：' . $error);
      }

      return true;
    }

    public function attrsToStrings($datas) {
      foreach ($datas as $name => &$data) {
        isset($this->columns[$name]) && $this->columns[$name]['type'] == 'json' && $data = @json_encode($data);
        $data instanceof DateTime && $data = $data->format();
        $data instanceof Uploader && $data = $data->value();
      }
      return $datas;
    }

    public function insert($datas) {
      $datas = $this->attrsToStrings($datas);
      
      if (!$sql = SQLBuilder::insert(quoteName($this->tableName), $datas))
        return Model::log('新增資料失敗，錯誤原因：SQL Builder 錯誤');

      if ($error = Connection::instance()->query($sql->getRaw(), $sql->getVals()))
        return Model::log('新增資料失敗，錯誤原因：' . $error);

      return true;
    }

    public function inserts($rows) {
      if (!$rows)
        return true;

      $rows = array_map([$this, 'attrsToStrings'], $rows);
      $keys = array_keys($rows[0]);
      $rows = array_filter(array_map(function($row) use ($keys) { $new = []; foreach ($keys as $key) if (!array_key_exists($key, $row)) return null; else $new[$key] = $row[$key]; return $new; }, $rows));

      $sql = SQLBuilder::inserts(quoteName($this->tableName), $rows);
      $error = Connection::instance()->query($sql->getRaw(), $sql->getVals());

      return $error
        ? Model::log('新增多筆資料失敗，錯誤原因：' . $error)
        : true;
    }

    public function truncate() {
      $sql = SQLBuilder::truncate(quoteName($this->tableName));
      $error = Connection::instance()->query($sql->getRaw(), $sql->getVals());

      return $error
        ? Model::log('清除「' . quoteName($this->tableName) . '」的資料失敗，錯誤原因：' . $error)
        : true;
    }

    public function delete($primaries) {
      $where = $this->primariesToWhere($primaries);

      if (!$sql = SQLBuilder::delete(quoteName($this->tableName), array_intersect_key(['where' => $where], ['where' => '', 'order' => '', 'limit' => '', 'offset' => ''])))
        return Model::log('刪除資料失敗，錯誤原因：SQL Builder 錯誤');
      
      if ($error = Connection::instance()->query($sql->getRaw(), $sql->getVals()))
        return Model::log('刪除資料失敗，錯誤原因：' . $error);

      return true;
    }

    public function deleteAll($options) {
      if (!$sql = SqlBuilder::delete(quoteName($this->tableName), array_intersect_key($options, ['where' => '', 'order' => '', 'limit' => '', 'offset' => ''])))
        return Model::log('刪除多筆資料失敗，錯誤原因：SQL Builder 錯誤');

      if ($error = Connection::instance()->query($sql->getRaw(), $sql->getVals()))
        return Model::log('刪除多筆資料失敗，錯誤原因：' . $error);

      return true;
    }

    public function updateAll($attr, $options) {
      if (!$sql = SQLBuilder::update(quoteName($this->tableName), $this->attrsToStrings($attr), array_intersect_key($options, ['where' => '', 'order' => '', 'limit' => '', 'offset' => ''])))
        return Model::log('更新多筆資料失敗，錯誤原因：SQL Builder 錯誤');

      if ($error = Connection::instance()->query($sql->getRaw(), $sql->getVals()))
        return Model::log('更新多筆資料失敗，錯誤原因：' . $error);

      return true;
    }

    private function getMetaData() {
      $sth = null;
      
      $closure = function() {
        $error = Connection::instance()->query("SHOW COLUMNS FROM " . quoteName($this->tableName), [], $sth);
        $error && Model::error('取得「' . $this->tableName . '」Table 的 Meta Data 失敗，錯誤原因：' . $error);

        $columns = [];
        foreach ($sth->fetchAll() as $row)
          if ($column = columnFormat($row))
            $columns[$column['field']] = $column;

        return $columns;
      };

      $this->columns = Model::cache('MetaData', $this->tableName, $closure);

      return $this;
    }

    private function setPrimaries() {
      $className = $this->className;

      if (isset($className::$primaries))
        $this->primaries = is_array($className::$primaries) ? $className::$primaries : [$className::$primaries];
      else
        $this->primaries = array_values(array_column(array_filter($this->columns, function($column) { return $column['primary']; }), 'field'));

      return $this;
    }

    private function primariesToWhere($primaries) {
      $where = Where::create();
      
      foreach ($primaries as $primary => $value)
        $where->and(quoteName($primary) . ' = ?', $value);

      return $where;
    }
  }

  final class SQLBuilder {
    public static function select($table, $options) {
      $vals = [];
      $strs = ['SELECT'];

      array_push($strs, empty($options['select']) ? '*' : $options['select']);
      array_push($strs, 'FROM');
      array_push($strs, $table);
      
      if (!empty($options['where']) && $options['where']->getRaw() != '') {
        $vals = $options['where']->getVals();
        array_push($strs, 'WHERE', $options['where']->getRaw());
      }

      empty($options['group'])  || array_push($strs, 'GROUP BY', $options['group']);
      empty($options['having']) || array_push($strs, 'HAVING', $options['having']);
      empty($options['order'])  || array_push($strs, 'ORDER BY', $options['order']);

      $limit = empty($options['limit']) ? 0 : intval($options['limit']);
      $offset = empty($options['offset']) ? 0 : intval($options['offset']);

      if ($limit || $offset)
        array_push($strs, 'LIMIT', intval($offset) . ', ' . intval($limit));

      return new self(implode(' ', array_filter($strs)), $vals);
    }

    public static function update($table, $datas, $options) {
      if (!$datas)
        return null;

      $sets = [];
      $vals = [];
      foreach ($datas as $key => $val) {
        array_push($sets, quoteName($key) . ' = ?');
        array_push($vals, $val);
      }

      if (!$sets)
        return null;

      $strs = ['UPDATE'];
      array_push($strs, $table);
      array_push($strs, 'SET');
      array_push($strs, implode(', ', $sets));

      if (!empty($options['where']) && $options['where']->getRaw() != '') {
        $vals = array_merge($vals, $options['where']->getVals());
        array_push($strs, 'WHERE', $options['where']->getRaw());
      }

      empty($options['order']) || array_push($strs, 'ORDER BY', $options['order']);

      $limit = empty($options['limit']) ? 0 : intval($options['limit']);
      $limit && array_push($strs, 'LIMIT', intval($limit));

      return new self(implode(' ', array_filter($strs)), $vals);
    }

    public static function insert($table, $datas) {
      $keys = $vals = [];
      foreach ($datas as $key => $val) {
        array_push($keys, quoteName($key));
        array_push($vals, $val);
      }

      $strs = ['INSERT'];
      array_push($strs, 'INTO');
      array_push($strs, $table);
      array_push($strs, '(' . implode(', ', $keys) . ')');
      array_push($strs, 'VALUES');
      array_push($strs, '(' . implode(', ', array_fill(0, count($keys), '?')) . ')');

      return new self(implode(' ', array_filter($strs)), $vals);
    }

    public static function inserts($table, $rows) {
      $strs = ['INSERT'];
      array_push($strs, 'INTO');
      array_push($strs, $table);
      array_push($strs, '(' . implode(', ', array_map('\M\quoteName', array_keys($rows[0]))) . ')');
      array_push($strs, 'VALUES');
      $values = $vals = [];

      foreach ($rows as $row) {
        array_push($values, '(' . implode(', ', array_fill(0, count($row), '?')) . ')');
        array_push($vals, array_values($row));
      }

      $vals = arrayFlatten($vals);
      array_push($strs, implode(',', $values));

      return new self(implode(' ', $strs), $vals);
    }

    public static function truncate($table) {
      $strs = ['TRUNCATE'];
      array_push($strs, 'TABLE');
      array_push($strs, $table);
      return new self(implode(' ', array_filter($strs)), []);
    }

    public static function delete($table, $options) {
      $strs = ['DELETE'];
      array_push($strs, 'FROM');
      array_push($strs, $table);

      $vals = [];
      if (!empty($options['where']) && $options['where']->getRaw() != '') {
        $vals = array_merge($vals, $options['where']->getVals());
        array_push($strs, 'WHERE', $options['where']->getRaw());
      }
      
      empty($options['order']) || array_push($strs, 'ORDER BY', $options['order']);

      $limit = empty($options['limit']) ? 0 : intval($options['limit']);
      $limit && array_push($strs, 'LIMIT', intval($limit));

      return new self(implode(' ', array_filter($strs)), $vals);
    }

    private $raw  = '';
    private $vals = [];

    private function __construct($raw, $vals) {
      $count = substr_count($raw, '?');
      $count <= count($vals) || Model::error('SQLBuilder 參數錯誤，「' . $raw . '」 有 ' . count($vals) . ' 個參數，目前只給 ' . $count . ' 個');

      $this->raw  = $raw;
      $this->vals = array_slice($vals, 0, $count);
    }

    public function getRaw() {
      return $this->raw;
    }

    public function getVals() {
      return $this->vals;
    }
  }

  final class Connection extends \PDO {
    public  static $configs   = [];
    private static $logger    = null;
    private static $instances = [];
    private static $options   = [\PDO::ATTR_CASE => \PDO::CASE_NATURAL, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL, \PDO::ATTR_STRINGIFY_FETCHES => false];

    public static function logger($func) {
      return self::$logger = $func;
    }

    public static function create($config) {
      try {
        return new static($config);
      } catch (\PDOException $e) {
        Model::error('PDO 連線錯誤，請檢查 Database Config 設定值，錯誤原因：' . $e->getMessage());
        return null;
      }
    }

    public static function instance($key = '') {
      return self::$instances[$key] ?? self::$instances[$key] = static::create(self::$configs[$key] ?? Model::error('尚未設定連線方式'));
    }

    public static function close() {
      foreach (self::$instances as &$instance)
        $instance = null;
      self::$instances = [];
      return true;
    }

    private static function implodeRecursive($glue, $pieces) {
      $ret = '';

      foreach ($pieces as $piec)
        $ret .= isset($piec)
          ? !is_object($piec)
            ? !is_bool($piec)
              ? is_array($piec)
                ? '[' . self::implodeRecursive($glue, $piec) . ']' . $glue
                : $piec . $glue
              : ($piec ? 'true' : 'false') . $glue
            : get_class($piec) . $glue
          : 'null' . $glue;

      return substr($ret, 0, 0 - strlen($glue));
    }

    public function __construct($config) {
      parent::__construct('mysql:host=' . $config->hostname() . ';dbname=' . $config->database(), $config->username(), $config->password(), self::$options);
      $this->setEncoding($config->encoding());
    }

    public function setEncoding($encoding) {
      $error = $this->query('SET NAMES ?', [$encoding]);
      $error && Model::error('設定編碼格式「' . $encoding . '」失敗，錯誤原因：' . $error);
      return $this;
    }

    public function query($sql, $vals = [], &$sth = null, $fetchModel = \PDO::FETCH_ASSOC, $log = true) {
      try {
        if (!$sth = $this->prepare((string)$sql))
          return '執行 Connection prepare 失敗';

        $sth->setFetchMode($fetchModel);
        
        $start = \microtime(true);
        $status = $sth->execute($vals);

        $logger = self::$logger ?? null;
        $logger && $logger($sql, $vals, $status, \number_format((\microtime(true) - $start) * 1000, 1), $log);

        if (!$status)
          return '執行 Connection execute 失敗';

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

    public function hostname($hostname = null) {
      if (!isset($hostname))
        return $this->hostname;
      $this->hostname = $hostname;
      return $this;
    }

    public function username($username = null) {
      if (!isset($username))
        return $this->username;
      $this->username = $username;
      return $this;
    }

    public function password($password = null) {
      if (!isset($password))
        return $this->password;
      $this->password = $password;
      return $this;
    }

    public function database($database = null) {
      if (!isset($database))
        return $this->database;
      $this->database = $database;
      return $this;
    }

    public function encoding($encoding = null) {
      if (!isset($encoding))
        return $this->encoding;
      $this->encoding = $encoding;
      return $this;
    }
  }

  abstract class Plugin {
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
      $this->value !== null && !$this->validate() && Model::error('「' . $this->value . '」無法轉為 ' . static::class . ' 格式');
      return $this;
    }
    
    public function value() {
      return $this->value;
    }
    
    public function isNull() {
      return $this->value === null;
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

    public function __toString() {
      return $this->format(null, '');
    }

    public function setValue($value = null) {
      parent::setValue($value);
      $this->datetime = $this->value !== null
        ? $this->value instanceof \DateTime
          ? $this->value
          : \DateTime::createFromFormat($this->format, $this->value)
        : null;
      return $this;
    }
  }

  abstract class Uploader extends Plugin {
    abstract public function clean();
    
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
      return array_merge([
        get_class($this->model)::table()->tableName,
        $this->column],
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
        return Model::log('搬移至指定目錄時發生錯誤，tmpPath：' . $tmpPath . '，path：' . $path);

      @unlink($tmpPath) || Model::log('移除舊資料錯誤');

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
        return Model::log('下載時發生錯誤，path：' . $path);

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
        return Model::log('未設定 Model 與 Column');
      
      if (!$this->driver)
        return Model::log('取得 Save Driver 物件失敗');
      
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
        return Model::log('無法取得圖片，網址：' . $url) ?: '';

      $write = fopen($file = $this->tmpDir . static::randomName() . ($format ? '.' . $format : ''), 'w');
      fwrite($write, $data);
      fclose($write);
      @umaskChmod($file, 0777);

      return $file;
    }

    protected function putFileCheck(&$file) {
      if (is_string($file)) {
        if (!(($file = $this->download($file)) && file_exists($file)))
          return Model::log('檔案格式有誤(1)') ?: [];
        
        $file = ['name' => basename($file), 'tmp_name' => $file, 'type' => '', 'error' => '', 'size' => filesize($file)];
      }

      if (!is_array($file))
        return Model::log('檔案格式有誤(3)', '缺少 key：' . $key) ?: [];

      foreach (['name', 'tmp_name', 'type', 'error', 'size'] as $key)
        if (!array_key_exists($key, $file))
          return Model::log('檔案格式有誤(2)', '缺少 key：' . $key) ?: [];

      $pathinfo     = pathinfo($file['name']);
      $file['name'] = preg_replace("/[^a-zA-Z0-9\\._-]/", "", $file['name']);
      $format       = !empty($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';
      $file['name'] = ($pathinfo['filename'] ? $pathinfo['filename'] : static::randomName()) . $format;

      if (!$this->checkSetting())
        return [];

      if (!$this->moveOriFile($file, $format, $tmp))
        return Model::log('搬移至暫存目錄時發生錯誤') ?: [];

      return [
        'name' => $file['name'],
        'format' => $format === '' ? null : $format,
        'tmpPath' => $tmp,
      ];
    }

    protected function clear($key = '') {
      return $this->driver->delete($path = implode(DIRECTORY_SEPARATOR, array_merge($this->dirs(), [$key . $this->value])))
        ? true
        : Model::log('移除時發生錯誤，path：' . $path);
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

    public function clean() {
      if (!$this->checkSetting())
        return false;

      if (!$this->clear(''))
        return false;

      $nullable = $this->model->table()->columns[$this->column]['nullable'] ?? false;
      $this->value = $nullable ? null : '';
      return !$this->value;
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
        $image->logger(function(...$args) { Model::log(...$args); });

        $image->rotate($orientation);
        $name = static::randomName() . (static::AUTO_FORMAT ? $format ?? ('.' . $image->getFormat()) : '');

        foreach ($versions as $key => $params) {
          $version = $key . static::SYMBOL . $name;
          $newPath = $this->tmpDir . $version;

          if (!$this->build(clone $image, $newPath, $params))
            return Model::log('圖像處理失敗，儲存路徑：' . $newPath . '，版本：' . $key);

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
        return Model::log('圖像處理，發生意外錯誤，錯誤訊息：' . $e->getMessage());
      }

      if (count($news) != count($versions) + 1)
        return Model::log('縮圖未完成，有些圖片未完成縮圖，成功數量：' . count($news) . '，版本數量：' . count($versions));

      foreach ($news as $data) {
        if (!$this->driver->put($data['path'], $path = implode(DIRECTORY_SEPARATOR, array_merge($this->dirs(), [$data['name']]))))
          return Model::log('搬移至指定目錄時發生錯誤，' . 'tmpPath：' . $tmpPath . 'path：' . $path);
        
        @unlink($data['path']) || Model::log('移除舊資料錯誤，path：' . $new['path']);
      }

      @unlink($tmpPath) || Model::log('移除舊資料錯誤');

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

    public function clean() {
      if (!$this->checkSetting())
        return false;

      foreach (array_merge(array_keys($this->versions), ['']) as $key)
        if (!$this->clear($key !== '' ? $key . static::SYMBOL : ''))
          return false;

      $nullable = $this->model->table()->columns[$this->column]['nullable'] ?? false;
      $this->value = $nullable ? null : '';
      return !$this->value;
    }

    private function build($image, $file, $params) {
      if (!$params)
        return $image->save($file, true);

      if (!$method = array_shift($params))
        return Model::log('縮圖函式方法錯誤');

      if (!method_exists($image, $method))
        return Model::log('縮圖函式沒有此方法，縮圖函式：' . $method);

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
      $map = self::map();

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
      $map = self::map();

      $limit = strlen($str);
      $res = 0;
      $i = $limit;

      while ($i--) {
        $res = self::$width * $res + strpos($map[$i % self::$height], $str[$limit - $i - 1]);
      }

      return (int)$res;
    }

    private static function map() {
      if (self::$map !== null)
        return self::$map;
      
      self::$digitals = implode('', array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'), ['-', '_']));
      
      preg_match('/^[0-9a-f]+$/', self::$key)
        || self::$key = '';

      $tmp = ($len = strlen(self::$key))
        ? array_map(function($i) use ($len) { return self::$key[$i % $len]; }, array_keys(array_fill(0, strlen(self::$digitals), null)))
        : array_fill(0, strlen(self::$digitals), 0);

      $map = array_map('self::rule', $tmp);
      self::$width = strlen($map[0]);
      self::$height = count($map);

      return self::$map = $map;
    }

    private static function rule($salt = 0) {
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
      $type = $this->getMimeByExtension($source);
      $size = filesize($source);
      $source = @fopen($source, 'rb');

      $url  = ($this->isUseSSL && extension_loaded('openssl') ? 'https://' : 'http://') . $host . $dest;

      $headers = ['x-amz-acl: ' . $this->acl, 'Host: ' . $host, 'Date: ' . $date, 'Content-MD5: ' . $md5, 'Content-Type: ' . $type];
      $this->ttl && $this->ttl > 0 && array_push($headers, 'Cache-Control: max-age=' . $this->ttl);
      array_push($headers, 'Authorization: ' . 'AWS ' . $this->access . ':' . $this->hashCode($this->secret, implode("\n", ['PUT', $md5, $type, $date, 'x-amz-acl:' . $this->acl, '/' . $this->bucket . $dest])));

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
      array_push($headers, 'Authorization: ' . 'AWS ' . $this->access . ':' . $this->hashCode($this->secret, implode("\n", ['DELETE', '', '', $date, '/' . $this->bucket . $path])));

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
      array_push($headers, 'Authorization: ' . 'AWS ' . $this->access . ':' . $this->hashCode($this->secret, implode("\n", ['GET', '', '', $date, '/' . $this->bucket . $source])));

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

    private function hashCode($secret, $string) {
      return base64_encode(extension_loaded('hash')
        ? hash_hmac('sha1', $string, $secret, true)
        : pack('H*', sha1((str_pad($secret, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) . pack('H*', sha1((str_pad($secret, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $string)))));
    }

    private function getMimeByExtension($file) {
      return (self::$extensions[strtolower(pathinfo($file, PATHINFO_EXTENSION))] ?? ['text/plain'])[0];
    }
  }
}
