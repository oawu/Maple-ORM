<?php

namespace Orm\Core;

use \Orm\Model;

final class Connection extends \PDO {
  private static ?string $_firstKey = null;
  private static array $_instances = [];
  private static array $_configs = [];
  private static array $_options = [\PDO::ATTR_CASE => \PDO::CASE_NATURAL, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL, \PDO::ATTR_STRINGIFY_FETCHES => false];

  public static function instance(?string $db = null): ?Connection {
    if ($db === null) {
      $db = self::_getFirstKey();
    }

    if (isset(self::$_instances[$db])) {
      return self::$_instances[$db];
    }

    if (!array_key_exists($db, self::$_configs)) {
      Model::executeError('尚未設定 MySQL「' . $db . '」的連線方式');
      return null;
    }

    $connection = self::_create($db, self::$_configs[$db]);
    if (!($connection instanceof Connection)) {
      Model::executeError('資料庫連線錯誤');
      return null;
    }

    self::$_instances[$db] = $connection;
    return self::$_instances[$db];
  }
  public static function close(): bool {
    foreach (self::$_instances as &$instance) {
      $instance = null;
    }
    self::$_instances = [];
    return true;
  }
  public static function getDbs(): array {
    return array_keys(self::$_configs);
  }
  public static function setConfig(string $db, Config $config): Config {
    self::$_configs[$db] = $config;
    return $config;
  }

  private static function _getFirstKey(): string {
    if (self::$_firstKey !== null) {
      return self::$_firstKey;
    }
    return self::$_firstKey = array_key_first(self::$_configs) ?? '';
  }
  private static function _create(string $db, Config $config): ?Connection {
    $connection = null;
    try {
      $connection = new Connection($db, $config);
    } catch (\Exception $error) {
      Model::executeError('PDO 連線錯誤，請檢查 Database Config 設定值，錯誤原因：' . $error->getMessage());
      return null;
    }

    $encoding = $config->getEncoding();
    try {
      $connection->setEncoding($encoding);
    } catch (\Exception $error) {
      Model::executeError('設定編碼格式「' . $encoding . '」失敗，錯誤原因：' . $error->getMessage());
      return null;
    }

    return $connection;
  }

  private string $_db;

  private function __construct(string $db, Config $config) {

    $this->_db = $db;

    $host = $config->getHostname();
    $base = $config->getDatabase();
    $user = $config->getUsername();
    $pass = $config->getPassword();
    parent::__construct('mysql:host=' . $host . ';dbname=' . $base, $user, $pass, self::$_options);
  }

  public function setEncoding(?string $encoding): self {
    if ($encoding === null) {
      return $this;
    }

    $error = $this->runQuery('SET NAMES ?;', [$encoding]);
    if ($error instanceof \Exception) {
      throw $error;
    }

    return $this;
  }
  public function runQuery(string $sql, array $vals = [], &$stmt = null, int $fetchModel = \PDO::FETCH_ASSOC, bool $log = true): ?\Exception {
    $db = $this->_getDb();
    try {
      if (!$stmt = $this->prepare($sql)) {
        throw new \Exception('執行 Connection prepare 失敗');
      }

      $stmt->setFetchMode($fetchModel);

      $start = \microtime(true);
      $status = $stmt->execute($vals);
      Model::executeQueryLog($db, $sql, $vals, $status, \microtime(true) - $start, $log);

      if (!$status) {
        throw new \Exception('執行 Connection execute 失敗');
      }
      return null;
    } catch (\Exception $e) {
      return $e;
    }
  }

  private function _getDb(): string {
    return $this->_db;
  }
}
