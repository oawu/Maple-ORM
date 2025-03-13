<?php

namespace Orm\Core\Plugin;

use \Orm\Model;
use \Orm\Helper;
use \Orm\Core\Column;
use \Orm\Core\Hashids;
use \Orm\Core\Plugin;
use \Orm\Core\Plugin\Uploader\Driver;
use \Orm\Core\Plugin\Uploader\Driver\S3;
use \Orm\Core\Plugin\Uploader\Driver\Local;

abstract class Uploader extends Plugin {
  abstract protected function _clean(Driver $driver): ?string; // php8 -> return static

  public const DRIVER_S3 = 'S3';
  public const DRIVER_LOCAL = 'Local';

  public const NAME_SORT_MD5 = 'md5';
  public const NAME_SORT_ORIGIN = 'origin';
  public const NAME_SORT_RANDOM = 'random';

  private static $_func = null;

  public static function func(callable $func): void {
    self::$_func = $func;
  }
  public static function randomName(): string {
    return bin2hex(random_bytes(16));
  }
  public static function allowTypes(): array {
    return ['varchar'];
  }

  protected static function _getFileNameFromUrl(string $path): array {
    $tokens = array_filter(array_map('trim', explode('/', $path)), fn($t) => $t !== '');
    $name = array_pop($tokens) ?? '';

    if ($name === '') {
      return [
        'name' => 'random_' . date('YmdHis') . '_' . self::randomName(),
        'ext' => '',
      ];
    }

    $name = preg_replace('/[^a-zA-Z0-9_\x{4e00}-\x{9fa5}\.\-\s]/u', '_', $name);
    $name = preg_replace('/_{2,}/', '_', $name);

    $tokens = explode('.', $name);

    $name = trim(implode('.', $tokens));
    $ext = '';

    if (count($tokens) > 1) {
      $ext = trim(array_pop($tokens));
      $name = trim(implode('.', $tokens));
      $ext = ($ext === '' ? '' : '.') . $ext;
    }

    return [
      'name' => preg_replace('/\s/', '_', $name),
      'ext' => $ext,
    ];
  }

  private string $_defaultUrl = '';
  private string $_baseUrl = '';
  private ?array $_driver = null;
  private ?string $_tmpDir = null;
  private array $_baseDirs   = [];
  private array $_namingSorts = [];

  public function __construct(?Model $model, Column $column, ?string $value, ?callable $func = null, array $sorts = []) {
    parent::__construct($model, $column, $value);

    $this->setNamingSort(...$sorts);

    $_func = self::$_func ?? null;
    if (is_callable($_func)) {
      $_func($this);
    }

    if ($func !== null) {
      $func($this);
    }
  }

  public function __toString(): string {
    return (string)$this->getValue();
  }
  public function toSqlString(): ?string {
    return $this->getValue();
  }
  public function setNamingSort(string ...$rules): self {
    $this->_namingSorts = array_unique(array_filter($rules, fn($rule) => in_array($rule, [self::NAME_SORT_ORIGIN, self::NAME_SORT_MD5, self::NAME_SORT_RANDOM])));
    return $this;
  }
  public function setTmpDir(string $dir): self { // php8 -> return static
    $isRoot = $dir[0] === DIRECTORY_SEPARATOR;

    $this->_tmpDir = ($isRoot ? DIRECTORY_SEPARATOR : '') . implode(DIRECTORY_SEPARATOR, Helper::explode($dir, '/', ['\\', '/']));

    if ($this->_tmpDir !== '') {
      $this->_tmpDir .= DIRECTORY_SEPARATOR;
    }

    return $this;
  }
  public function getTmpDir(): string {
    $tmpDir = $this->_tmpDir;

    if (is_string($tmpDir) && $tmpDir !== '') {
      return $tmpDir;
    }

    return sys_get_temp_dir() . DIRECTORY_SEPARATOR;
  }
  public function setBaseDir(string ...$dirs): self { // php8 -> return static
    $baseDirs = [];
    foreach ($dirs as $dir) {
      $dir = Helper::explode($dir);
      foreach ($dir as $d) {
        $baseDirs[] = $d;
      }
    }
    $this->_baseDirs = $baseDirs;
    return $this;
  }
  public function getBaseDir(): array {
    return $this->_baseDirs;
  }
  public function setBaseUrl(string $url): self { // php8 -> return static
    $this->_baseUrl = rtrim($url, '/');
    if ($this->_baseUrl !== '') {
      $this->_baseUrl .= '/';
    }
    return $this;
  }
  public function setDriver(?string $driver, ?array $options = null): self { // php8 -> return static
    if ($driver === null) {
      $this->_driver = null;
    }

    if ($driver == Uploader::DRIVER_S3) {
      $this->_driver = [
        'class' => S3::class,
        'options' => $options,
        'instance' => null,
      ];
    }
    if ($driver == Uploader::DRIVER_LOCAL) {
      $this->_driver = [
        'class' => Local::class,
        'options' => $options,
        'instance' => null,
      ];
    }
    return $this;
  }
  public function setDefaultUrl(string $defaultUrl): self { // php8 -> return static
    $this->_defaultUrl = $defaultUrl;
    return $this;
  }
  public function getDefaultUrl(): string {
    return $this->_defaultUrl;
  }
  public function getBaseUrl(): string {
    return $this->_baseUrl;
  }
  public function getPaths(): array {
    $model = $this->getModel();
    if (!($model instanceof Model)) {
      return [];
    }

    $class = get_class($model);
    $tableName = $class::getTable($model->getDb())->getName(false);

    $column = $this->getColumn();
    $columnName = $column->getName();

    $id = $model->id ?? 0;

    if ($id === 0) {
      return [$tableName, $columnName];
    }

    $hashids = Model::getHashids();

    if ($hashids instanceof Hashids) {
      return [
        $tableName,
        $columnName,
        ...str_split($hashids->encode($id), 2)
      ];
    }

    return [
      $tableName,
      $columnName,
      ...str_split(sprintf('%08s', base_convert($id, 10, 36)), 4)
    ];
  }
  public function getDirs(): array {
    return [...$this->getBaseDir(), ...$this->getPaths()];
  }

  protected function _getNamingSorts(): array {
    return $this->_namingSorts;
  }
  protected function _setValue($value): self {
    if ($value === null) {
      return parent::_setValue($value);
    }
    if (is_string($value)) {
      return parent::_setValue($value);
    }
    throw new \Exception('「' . $value . '」無法轉為 ' . static::class . ' 格式');
  }
  protected function _download(string $url): array {
    $parsed = parse_url($url);

    ['name' => $name, 'ext' => $ext] = self::_getFileNameFromUrl($parsed['path'] ?? '');
    $path = $this->getTmpDir() . 'download_' . date('YmdHis') . '_' . self::randomName() . $ext;

    $file = fopen($path, 'wb');

    $curl = curl_init($url);
    curl_setopt_array($curl, [
      CURLOPT_URL => $url,
      CURLOPT_TIMEOUT => 120,
      CURLOPT_HEADER => false,
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_AUTOREFERER => true,
      CURLOPT_CONNECTTIMEOUT => 30,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_USERAGENT => 'Maple 9.0',
      CURLOPT_FILE => $file,
    ]);

    $success = curl_exec($curl);
    curl_close($curl);

    fclose($file);

    if (!$success) {
      throw new \Exception('下載檔案失敗，網址：' . $url);
    }

    return [
      'name' => $name,
      'ext' => $ext,
      'path' => $path,
    ];
  }
  protected function _moveOriFile(string $source, string $dest): bool {
    if (!file_exists($source)) {
      return false;
    }
    if (is_uploaded_file($source)) {
      @move_uploaded_file($source, $dest);
    } else {
      @rename($source, $dest);
    }
    if (!file_exists($dest)) {
      return false;
    }

    @Helper::umaskChmod($dest, 0777);
    return true;
  }
  protected function _getDriver(): Driver {
    $driver = $this->_driver;
    if ($driver === null) {
      throw new \Exception('未設定 Uploader Driver');
    }

    if ($driver['instance'] instanceof Driver) {
      return $driver['instance'];
    }

    $class = $driver['class'] ?? '';
    $options = $driver['options'] ?? null;
    if (!class_exists($class)) {
      throw new \Exception('找不到 Uploader Driver「' . $class . '」');
    }

    $driver['instance'] = new $class($options);
    return $driver['instance'];
  }
}
