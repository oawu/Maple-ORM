<?php

$namespaceReplace = 'M';
$src = dirname(__FILE__, 1) . DIRECTORY_SEPARATOR . 'Src' . DIRECTORY_SEPARATOR . 'M' . DIRECTORY_SEPARATOR;

spl_autoload_register(static function($_class) use ($namespaceReplace, $src): void {
  if (class_exists($_class)) {
    return;
  }
  $tokens = explode('\\', $_class);
  $namespaces = array_slice($tokens, 0, -1);
  if (!$namespaces) {
    return;
  }

  $namespace = array_shift($namespaces);
  $class = $tokens[count($tokens) - 1];

  $realClass = '';
  if ($namespaceReplace !== 'M' && $namespaceReplace !== '' && $namespace === $namespaceReplace) {
    $namespace = 'M';
    $realClass = 'M\\' . ($namespaces ? implode('\\', $namespaces) . '\\' : '') . $class;
  }

  if ($namespace !== 'M') {
    return;
  }

  $path = $src . ($namespaces ? implode(DIRECTORY_SEPARATOR, $namespaces) . DIRECTORY_SEPARATOR : '') . $class . '.php';

  if (is_file($path) && is_readable($path)) {
    include_once $path;
  }

  if ($realClass !== '' && class_exists($realClass) && !class_exists($_class)) {
    class_alias($realClass, $_class);
  }
});
