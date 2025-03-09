<?php

namespace Orm;

use \Orm\Core\Plugin;
use \Orm\Core\Connection;

abstract class Helper {
  public static function version() {
    return '9.0.1';
  }
  public static function attrsToStrings(string $type, $val) {
    if ($type == 'json') {
      return $val === null ? null : json_encode($val);
    }
    if ($val instanceof Plugin) {
      return $val->toSqlString();
    }
    return $val;
  }
  public static function quoteName(string $string): string {
    if ($string === '') {
      return '``';
    }
    if (substr($string, 0, 1) === '`' || substr($string, -1) === '`') {
      return $string;
    }
    return "`{$string}`";
  }
  public static function bccomp(string $left, string $right): int {
    $lLen = strlen($left);
    $rLen = strlen($right);
    if ($lLen > $rLen) {
      return 1;
    }
    if ($lLen < $rLen) {
      return -1;
    }

    for ($i = 0; $i < $lLen; $i++) {
      $l = (int)$left[$i];
      $r = (int)$right[$i];
      if ($l > $r) {
        return 1;
      }
      if ($l < $r) {
        return -1;
      }
    }
    return 0;
  }
  public static function explode(string $path, string $separator = '/', array $includes = ['\\', '/']): array {
    foreach ($includes as $include) {
      if ($include !== $separator) {
        $path = str_replace($include, $separator, $path);
      }
    }

    $parts = explode($separator, $path);

    $paths = [];
    foreach ($parts as $part) {
      $part = trim($part);
      if ($part !== '' && $part !== '.') {
        $paths[] = $part;
      }
    }

    return $paths;
  }
  public static function umaskChmod(string $path, int $mode = 0777): bool {
    if (!file_exists($path)) {
      return false;
    }

    $oldmask = umask(0);
    $result = chmod($path, $mode);
    umask($oldmask);
    return $result;
  }
  public static function umaskMkdir(string $path, int $mode = 0777, bool $recursive = false): bool {
    if (is_dir($path)) {
      return true;
    }

    $oldmask = umask(0);
    $result = mkdir($path, $mode, $recursive);
    umask($oldmask);
    return $result;
  }
  public static function arrayIsList(array $arr): bool {
    if ($arr === []) {
      return true;
    }
    return array_keys($arr) === range(0, count($arr) - 1);
  }
  public static function whereQuestion(int $cnt): string {
    return implode(', ', array_fill(0, $cnt, '?'));
  }
  public static function toArray(Model $obj, bool $isRaw = false): array {
    $_attrs = [];
    $table = $obj->getTable($obj->getDb());
    $class = $table->getClass();
    $attrs = $obj->attrs();

    $hides = array_fill_keys($class::$hides ?? [], '1');

    foreach ($attrs as $key => $attr) {
      if (isset($hides[$key])) {
        continue;
      }

      if ($attr instanceof Plugin) {
        $_attrs[$key] = $attr->toArray($isRaw);
      } else {
        $_attrs[$key] = $attr;
      }
    }

    return $_attrs;
  }
  public static function rollback(?string $message = null) {
    throw new \Exception($message ?? Model::getLastLog());
  }
  public static function transaction(?string $db, callable $closure, ?array &$errors = null) {
    $instance = Connection::instance($db);

    try {
      if (!$instance->beginTransaction()) {
        throw new \Exception('Transaction 失敗');
      }

      $result = $closure();

      if (!$result) {
        throw new \Exception('transaction 回傳 false，故 rollback');
      }

      if (!$instance->commit()) {
        throw new \Exception('Commit 失敗');
      }

      $errors = null;
      return $result;
    } catch (\Exception $e) {
      $errors = $instance->rollback()
        ? [$e->getMessage()]
        : ['Rollback 失敗', $e->getMessage()];

      return null;
    }

    $errors = ['不明原因錯誤！'];

    return null;
  }
  /**
   * 產生 SQL WHERE 條件語句
   *
   * @param string $tableName 資料表名稱
   * @param mixed ...$args 條件參數
   * @return ?array 回傳 ['str' => SQL字串, 'vals' => 參數陣列] 或 null
   *
   * @example
   * // 不帶參數
   * where('users') // null
   *
   * // 單一參數
   * where('users', null)  // ['str' => 'users.id IS NULL', 'vals' => []]
   * where('users', 1)     // ['str' => 'users.id = ?', 'vals' => [1]]
   * where('users', '1')   // ['str' => 'users.id = ?', 'vals' => ['1']]
   * where('users', [1,2,3]) // ['str' => 'users.id IN (?,?,?)', 'vals' => [1,2,3]]
   * where('users', [])    // ['str' => '1=0', 'vals' => []]
   * where('users', ['id' => [1,2], 'name' => 'a']) // 複雜條件
   * where('users', ['id' => [], 'name' => 'a'])    // 複雜條件
   *
   * // 兩個參數
   * where('users', 'id', null)     // ['str' => 'users.id IS NULL', 'vals' => []]
   * where('users', 'id', 1)        // ['str' => 'users.id = ?', 'vals' => [1]]
   * where('users', 'id', '1')      // ['str' => 'users.id = ?', 'vals' => ['1']]
   * where('users', 'id', [1,2,3])  // ['str' => 'users.id IN (?,?,?)', 'vals' => [1,2,3]]
   * where('users', 'id', [])       // ['str' => '1=0', 'vals' => []]
   *
   * // 三個參數
   * where('users', 'id', null)              // ['str' => 'users.id IS NULL', 'vals' => []]
   * where('users', 'id', '!=', null)        // ['str' => 'users.id IS NOT NULL', 'vals' => []]
   * where('users', 'id', '=', 1)            // ['str' => 'users.id = ?', 'vals' => [1]]
   * where('users', 'id', '=', '1')          // ['str' => 'users.id = ?', 'vals' => ['1']]
   * where('users', 'id', 'IN', [1,2,3])     // ['str' => 'users.id IN (?,?,?)', 'vals' => [1,2,3]]
   * where('users', 'id', 'NOT IN', [1,2,3]) // ['str' => 'users.id NOT IN (?,?,?)', 'vals' => [1,2,3]]
   * where('users', 'id', 'BETWEEN', [1,2])  // ['str' => 'users.id BETWEEN ? AND ?', 'vals' => [1,2]]
   * where('users', 'id', 'LIKE', '%a%')     // ['str' => 'users.id LIKE ?', 'vals' => ['%a%']]
   */
  public static function where(string $tableName, ...$args): ?array {
    $length = count($args);

    if ($length == 0) {
      return null;
    }

    if ($length == 1) {
      $key = Model::PRIMARY_ID;
      $val = array_shift($args);

      $key = self::quoteName($key);

      if ($val === null) {
        return [
          'str' => $tableName . '.' . $key . ' IS NULL',
          'vals' => []
        ];
      }
      if (is_numeric($val)) {
        $val = 1 * $val;

        if (!is_int($val)) {
          return null;
        }
        return [
          'str' => $tableName . '.' . $key . ' = ?',
          'vals' => [$val]
        ];
      }
      if (is_string($val)) {
        return [
          'str' => $tableName . '.' . $key . ' = ?',
          'vals' => [$val]
        ];
      }
      if (is_array($val) && self::arrayIsList($val)) {
        $val = array_unique($val);

        if (!$val) {
          return [
            'str' => '1=0',
            'vals' => []
          ];
        }

        return [
          'str' => $tableName . '.' . $key . ' IN (' . self::whereQuestion(count($val)) . ')',
          'vals' => $val
        ];
      }
      if (is_array($val) && !self::arrayIsList($val)) {
        $_strs = [];
        $_vals = [];

        foreach ($val as $_key => $_val) {
          $_key = self::quoteName($_key);

          if ($_val === null) {
            $_strs[] = $tableName . '.' . $_key . ' IS NULL';
            continue;
          }

          if (is_numeric($_val)) {
            $_val = 1 * $_val;
            if (!is_int($_val)) {
              continue;
            }
            $_strs[] = $tableName . '.' . $_key . ' = ?';
            $_vals[] = $_val;
            continue;
          }

          if (is_string($_val)) {
            $_strs[] = $tableName . '.' . $_key . ' = ?';
            $_vals[] = $_val;
            continue;
          }

          if (is_array($_val) && self::arrayIsList($_val)) {
            $_val = array_unique($_val);

            if (!$_val) {
              $_strs[] = '1=0';
              continue;
            }

            $_strs[] = $tableName . '.' . $_key . ' IN (' . self::whereQuestion(count($_val)) . ')';
            foreach ($_val as $v) {
              $_vals[] = $v;
            }
          }
        }

        if (!$_strs) {
          return null;
        }

        return [
          'str' => implode(' AND ', $_strs),
          'vals' => $_vals
        ];
      }
      return null;
    }

    if ($length == 2) {
      $key = array_shift($args);
      $val = array_shift($args);

      $key = self::quoteName($key);

      if ($val === null) {
        return [
          'str' => $tableName . '.' . $key . ' IS NULL',
          'vals' => []
        ];
      }
      if (is_numeric($val)) {
        $val = 1 * $val;

        if (is_int($val)) {
          return [
            'str' => $tableName . '.' . $key . ' = ?',
            'vals' => [$val]
          ];
        } else {
          return null;
        }
      }
      if (is_string($val)) {
        return [
          'str' => $tableName . '.' . $key . ' = ?',
          'vals' => [$val]
        ];
      }
      if (is_array($val) && self::arrayIsList($val)) {
        $val = array_unique($val);
        $key = self::quoteName($key);

        if (!$val) {
          return [
            'str' => '1=0',
            'vals' => []
          ];
        }

        return [
          'str' => $tableName . '.' . $key . ' IN (' . self::whereQuestion(count($val)) . ')',
          'vals' => $val
        ];
      }
      return null;
    }

    $key = array_shift($args);
    $cmp = array_shift($args);
    $val = array_shift($args);

    if (!is_string($cmp)) {
      return null;
    }

    $key = self::quoteName($key);
    $cmp = strtoupper(trim($cmp));

    if ($val === null) {
      return in_array($cmp, ['!=', '!==', 'IS NOT'])
        ? [
          'str' => $tableName . '.' . $key . ' IS NOT NULL',
          'vals' => []
        ]
        : [
          'str' => $tableName . '.' . $key . ' IS NULL',
          'vals' => []
        ];
    }
    if (is_numeric($val)) {
      $val = 1 * $val;

      if (!is_int($val)) {
        return null;
      }
      return [
        'str' => $tableName . '.' . $key . ' ' . $cmp . ' ?',
        'vals' => [$val]
      ];
    }
    if (is_string($val)) {
      return [
        'str' => $tableName . '.' . $key . ' ' . $cmp . ' ?',
        'vals' => [$val]
      ];
    }
    if (is_array($val) && self::arrayIsList($val)) {
      $val = array_unique($val);

      if ($cmp == 'BETWEEN') {
        if (count($val) != 2) {
          return null;
        }
        return [
          'str' => $tableName . '.' . $key . ' BETWEEN ? AND ?',
          'vals' => $val
        ];
      }

      if (in_array($cmp, ['!=', '!==', 'NOT IN'])) {
        if (!$val) {
          return [
            'str' => '1=1',
            'vals' => []
          ];
        }
        return [
          'str' => $tableName . '.' . $key . ' NOT IN (' . self::whereQuestion(count($val)) . ')',
          'vals' => $val
        ];
      } else {
        if (!$val) {
          return [
            'str' => '1=0',
            'vals' => []
          ];
        }
        return [
          'str' => $tableName . '.' . $key . ' IN (' . self::whereQuestion(count($val)) . ')',
          'vals' => $val
        ];
      }
    }
    return null;
  }
}
