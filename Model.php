<?php

spl_autoload_register(static function($_class): void {
  if (class_exists($_class)) {
    return;
  }

  $namespaces = array_slice(explode('\\', $_class), 0, -1);
  if (!$namespaces) {
    return;
  }

  $namespace = array_shift($namespaces);
  if ($namespace !== 'M') {
    return;
  }

  $class = end(explode('\\', $_class));
  $path = dirname(__FILE__, 1) . DIRECTORY_SEPARATOR . 'Src' . DIRECTORY_SEPARATOR . 'M' . ($namespaces ? DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $namespaces) : '') . DIRECTORY_SEPARATOR . $class . '.php';

  if (is_file($path) && is_readable($path)) {
    include_once $path;
  }
});
