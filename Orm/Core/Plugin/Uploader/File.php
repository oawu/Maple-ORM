<?php

namespace Orm\Core\Plugin\Uploader;

use \Orm\Model;
use \Orm\Core\Column;
use \Orm\Core\Plugin\Uploader;

final class File extends Uploader {
  public function __construct(?Model $model, Column $column, ?string $value, ?callable $func = null) {
    parent::__construct($model, $column, $value, $func, [
      self::NAME_SORT_ORIGIN,
      self::NAME_SORT_MD5,
      self::NAME_SORT_RANDOM
    ]);
  }

  public function updateValue($value): self {
    $driver = $this->_getDriver();

    if ($value === null) {
      return parent::_setValue($this->_clean($driver));
    }

    if (is_string($value) && trim($value) === '') {
      return parent::_setValue($this->_clean($driver));
    }

    if (is_string($value)) {
      $value = trim($value);

      if ($value[0] === DIRECTORY_SEPARATOR && is_file($value) && is_readable($value)) {
        ['name' => $name, 'ext' => $ext] = self::_getFileNameFromUrl($value);

        return parent::_setValue($this->_file($driver, [
          'name' => $name,
          'ext' => $ext,
          'path' => $value,
          'size' => filesize($value),
        ]));
      }

      if (preg_match('/^https?:\/\/.*/', $value)) {
        ['name' => $name, 'ext' => $ext, 'path' => $path] = $this->_download($value);

        return parent::_setValue($this->_file($driver, [
          'name' => $name,
          'ext' => $ext,
          'path' => $path,
          'size' => filesize($path),
        ]));
      }
    }

    if (is_array($value)) {
      foreach (['name', 'tmp_name', 'type', 'error', 'size'] as $key) {
        if (!array_key_exists($key, $value)) {
          throw new \Exception('檔案格式錯誤');
        }
      }

      ['name' => $name, 'ext' => $ext] = self::_getFileNameFromUrl($value['name']);

      return parent::_setValue($this->_file($driver, [
        'name' => $name,
        'ext' => $ext,
        'path' => $value['tmp_name'],
        'size' => $value['size'],
      ]));
    }

    throw new \Exception('「' . $value . '」無法轉為 ' . static::class . ' 格式');
  }
  public function getUrl(): string {
    $value = $this->getValue();
    if (is_string($value) && $value !== '') {
      return $this->getBaseUrl() . implode('/', [...$this->getDirs(), $value]);
    }

    return $this->getDefaultUrl();
  }
  public function saveAs(string $dest, bool $throwException = false): bool {
    try {
      $driver = $this->_getDriver();
      $value = $this->getValue();
      if (!(is_string($value) && $value !== '')) {
        throw new \Exception('值為空');
      }

      $soruce = implode(DIRECTORY_SEPARATOR, [...$this->getDirs(), $value]);

      if (!$driver->saveAs($soruce, $dest)) {
        throw new \Exception('發生錯誤，soruce：' . $soruce . '，dest：' . $dest);
      }
    } catch (\Exception $errow) {
      if ($throwException) {
        throw $errow;
      }
      return false;
    }

    return true;
  }
  public function toArray(bool $isRaw = false) {
    return $isRaw ? $this->getValue() : $this->getUrl();
  }

  protected function _clean(Driver $driver): ?string {
    $oldValue = $this->getValue();
    $newValue = $this->getColumn()->getIsNullable() ? null : '';

    if (!(is_string($oldValue) && $oldValue !== '')) {
      return $newValue;
    }

    $path = implode(DIRECTORY_SEPARATOR, [...$this->getDirs(), $oldValue]);
    $result = $driver->delete($path);
    if (!$result) {
      throw new \Exception('移除時發生錯誤，path：' . $path);
    }

    return $newValue;
  }

  private function _file(Driver $driver, array $file): string {
    $path = $this->getTmpDir() . '_uploader_' . static::randomName() . $file['ext'];
    $result = $this->_moveOriFile($file['path'], $path);
    if (!$result) {
      throw new \Exception('搬移至暫存目錄時發生錯誤');
    }

    $name = static::randomName();
    $namingSorts = $this->_getNamingSorts();
    foreach ($namingSorts as $namingSort) {
      switch ($namingSort) {
        case self::NAME_SORT_ORIGIN:
          $name = $file['name'];
          break;
        case self::NAME_SORT_MD5:
          $name = md5_file($path);
          break;
        case self::NAME_SORT_RANDOM:
          $name = static::randomName();
          break;
      }

      if ($name !== '') {
        break;
      }
    }

    $value = $name . $file['ext'];
    $dest = implode(DIRECTORY_SEPARATOR, [...$this->getDirs(), $value]);

    if (!$driver->put($path, $dest)) {
      throw new \Exception('搬移至指定目錄時發生錯誤，檔案：' . $path . '，指定目錄：' . $dest);
    }

    if (!@unlink($path)) {
      throw new \Exception('移除舊資料錯誤，檔案：' . $path);
    }

    return $value;
  }
}
