<?php

namespace M\Core\Plugin\Uploader;

use \M\Core\Plugin\Uploader\Driver\S3;
use \M\Core\Plugin\Uploader\Driver\Local;

abstract class Driver {
  abstract public function put(string $source, string $dest): bool;
  abstract public function delete(string $path): bool;
  abstract public function saveAs(string $source, string $dest): bool;
}