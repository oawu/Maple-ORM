<?php

spl_autoload_register(static function ($newClassName): void {
  if (class_exists($newClassName)) {
    return;
  }

  $tokens = explode('\\', $newClassName);

  $path = PATH . implode(DIRECTORY_SEPARATOR, $tokens) . '.php';

  if (file_exists($path) && is_file($path) && is_readable($path)) {
    require_once $path;
  }
});
