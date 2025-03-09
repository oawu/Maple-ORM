<?php

namespace Orm\Core\Plugin\Uploader;

use \Orm\Model;
use \Orm\Core\Column;
use \Orm\Core\Plugin\Uploader;
use \Orm\Core\Plugin\Uploader\Image\Version;

use \Orm\Core\Thumbnail;
use \Orm\Core\Thumbnail\Gd;
use \Orm\Core\Thumbnail\Imagick;

final class Image extends Uploader {
  private const _VERSION_SYMBOL = '_';
  private const _AUTO_EXTENSION = true;

  private static $_thumbnailFunc = null;

  public static function setThumbnail(callable $func): void {
    self::$_thumbnailFunc = $func;
  }
  private static function _getThumbnail(): ?callable {
    if (!is_callable(self::$_thumbnailFunc)) {
      throw new \Exception('未設定 Image Uploader Thumbnail');
    }
    return self::$_thumbnailFunc;
  }

  private array $_versions = [];

  public function __construct(?Model $model, Column $column, ?string $value, ?callable $func = null) {
    parent::__construct($model, $column, $value, $func, [
      self::NAME_SORT_MD5,
      self::NAME_SORT_RANDOM,
      self::NAME_SORT_ORIGIN
    ]);
  }

  public function addVersion(string $key): Version {
    $version = Version::create();
    $this->_versions[$key] = $version;
    return $version;
  }
  public function updateValue($value): self {
    $driver = $this->_getDriver();
    $thumbnail = self::_getThumbnail();

    if ($value === null) {
      return parent::_setValue($this->_clean($driver, $thumbnail));
    }

    if (is_string($value) && trim($value) === '') {
      return parent::_setValue($this->_clean($driver, $thumbnail));
    }

    if (is_string($value)) {
      $value = trim($value);

      if ($value[0] === DIRECTORY_SEPARATOR && is_file($value) && is_readable($value)) {
        ['name' => $name, 'ext' => $ext] = self::_getFileNameFromUrl($value);

        return parent::_setValue($this->_file($driver, $thumbnail, [
          'name' => $name,
          'ext' => $ext,
          'path' => $value,
          'size' => filesize($value),
        ]));
      }

      if (preg_match('/^https?:\/\/.*/', $value)) {
        ['name' => $name, 'ext' => $ext, 'path' => $path] = $this->_download($value);

        return parent::_setValue($this->_file($driver, $thumbnail, [
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

      return parent::_setValue($this->_file($driver, $thumbnail, [
        'name' => $name,
        'ext' => $ext,
        'path' => $value['tmp_name'],
        'size' => $value['size'],
      ]));
    }

    throw new \Exception('「' . $value . '」無法轉為 ' . static::class . ' 格式');
  }
  public function getUrl(string $key = ''): string {
    $key = $this->_getKey($key);
    $value = $this->getValue();

    if (is_string($value) && $value !== '') {
      return $this->getBaseUrl() . implode('/', [...$this->getDirs(), $key . $value]);
    }

    return $this->getDefaultUrl();
  }
  public function saveAs(string $dest, string $key = '', bool $throwException = false): bool {
    try {
      $key = $this->_getKey($key);
      $driver = $this->_getDriver();
      $value = $this->getValue();

      if (!(is_string($value) && $value !== '')) {
        throw new \Exception('值為空');
      }

      $soruce = implode(DIRECTORY_SEPARATOR, [...$this->getDirs(), $key . $value]);

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
    return $isRaw ? $this->getValue() : array_map(fn($key) => $this->getUrl($key), array_keys($this->getVersions()));
  }
  public function getVersions(): array {
    return $this->_versions;
  }

  protected function _clean(Driver $driver): ?string {
    $oldValue = $this->getValue();
    $newValue = $this->getColumn()->getIsNullable() ? null : '';

    if (!(is_string($oldValue) && $oldValue !== '')) {
      return $newValue;
    }

    $keys = [...array_keys($this->getVersions()), ''];
    foreach ($keys as $key) {
      $path = implode(DIRECTORY_SEPARATOR, [...$this->getDirs(), $this->_getKey($key) . $oldValue]);
      $result = $driver->delete($path);
      if (!$result) {
        throw new \Exception('移除時發生錯誤，path：' . $path);
      }
    }

    return $newValue;
  }

  private function _file(Driver $driver, callable $thumbnail, array $file): string {
    $path = $this->getTmpDir() . '_uploader_' . static::randomName() . $file['ext'];
    $result = $this->_moveOriFile($file['path'], $path);

    if (!$result) {
      throw new \Exception('搬移至暫存目錄時發生錯誤');
    }

    $info = @exif_read_data($path);
    $orientation = $info['Orientation'] ?? 0;
    if ($orientation == 6) {
      $orientation = 90;
    } else if ($orientation == 8) {
      $orientation = -90;
    } else if ($orientation == 3) {
      $orientation = 180;
    } else {
      $orientation = 0;
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

    $files = [];

    try {
      $image = $thumbnail($path);

      if (!($image instanceof Thumbnail)) {
        throw new \Exception('縮圖 Lib 錯誤');
      }

      if ($image instanceof Imagick) {
        $image->rotate($orientation);
      }
      if ($image instanceof Gd) {
        $image->rotate($orientation);
      }

      if (self::_AUTO_EXTENSION) {
        if ($file['ext'] !== '') {
          $name .= $file['ext'];
        } else {
          $name .= '.' . $image->getExtension();
        }
      }

      $versions = $this->getVersions();
      foreach ($versions as $key => $version) {
        $_name = $this->_getKey($key) . $name;
        $dest = $this->getTmpDir() . $_name;

        $_image = clone $image;

        $method = $version->getMethod();
        $args = $version->getArgs();

        $result = false;

        if ($method === '') {
          $result = $_image->save($dest, true);
        } else {
          if (!method_exists($_image, $method)) {
            throw new \Exception('縮圖函式沒有此方法，縮圖函式：' . $method);
          }

          $_image->$method(...$args);
          $result = $_image->save($dest, true);
        }

        if (!$result) {
          throw new \Exception('圖像處理失敗，儲存路徑：' . $dest . '，版本：' . $key);
        }

        $files[] = [
          'name' => $_name,
          'path' => $dest
        ];
      }

      $dest = $this->getTmpDir() . $name;
      $image->save($dest, true);
      $files[] = [
        'name' => $name,
        'path' => $dest
      ];

      if (count($files) != count($versions) + 1) {
        throw new \Exception('有些圖片未完成縮圖，成功數量：' . count($files) . '，版本數量：' . count($versions));
      }
    } catch (\Exception $e) {
      throw new \Exception('圖像處理發生錯誤，錯誤訊息：' . $e->getMessage());
    }

    foreach ($files as $file) {
      ['name' => $_name, 'path' => $_path] = $file;

      $dest = implode(DIRECTORY_SEPARATOR, [...$this->getDirs(), $_name]);

      if (!$driver->put($_path, $dest)) {
        throw new \Exception('搬移至指定目錄時發生錯誤，檔案：' . $_path . '，指定目錄：' . $dest);
      }

      if (!@unlink($_path)) {
        throw new \Exception('移除舊資料錯誤，檔案：' . $_path);
      }
    }

    if (!@unlink($path)) {
      throw new \Exception('移除舊資料錯誤，檔案：' . $path);
    }
    return $name;
  }
  private function _getKey(string $key = ''): string {
    $versions = $this->getVersions();
    if (isset($versions[$key])) {
      return $key . self::_VERSION_SYMBOL;
    } else {
      return '';
    }
  }
}
