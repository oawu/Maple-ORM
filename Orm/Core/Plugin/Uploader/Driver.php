<?php

namespace Orm\Core\Plugin\Uploader;

abstract class Driver {
  abstract public function put(string $source, string $dest): bool;
  abstract public function delete(string $path): bool;
  abstract public function saveAs(string $source, string $dest): bool;
}
