<?php

namespace Orm\Core\Plugin\Uploader\Driver;

use \Orm\Helper;
use \Orm\Core\Plugin\Uploader\Driver;

final class Local extends Driver {
  private $_storage = null;

  public function __construct(array $options = []) {
    $storage = $options['storage'] ?? '';
    $isRoot = $storage[0] === DIRECTORY_SEPARATOR;

    $storage = ($isRoot ? DIRECTORY_SEPARATOR : '') . implode(DIRECTORY_SEPARATOR, Helper::explode($storage, '/', ['\\', '/']));
    if ($storage !== '') {
      $storage .= DIRECTORY_SEPARATOR;
    }

    $this->_storage = $storage;
  }

  public function put(string $source, string $dest): bool {
    if (!(is_file($source) && is_readable($source))) {
      return false;
    }

    $dest = $this->_storage . $dest;
    $path = pathinfo($dest, PATHINFO_DIRNAME);

    if (!file_exists($path)) {
      @Helper::umaskMkdir($path, 0777, true);
    }

    if (is_dir($path) && is_writable($path)) {
      @copy($source, $dest);
    }

    if (!file_exists($dest)) {
      return false;
    }

    @Helper::umaskChmod($dest, 0777);

    return true;
  }
  public function delete(string $path): bool {
    $path = $this->_storage . $path;

    if (!file_exists($path)) {
      return true;
    }

    @unlink($path);
    return !file_exists($path);
  }
  public function saveAs(string $source, string $dest): bool {
    $source = $this->_storage . $source;

    @copy($source, $dest);

    return file_exists($dest);
  }
}
