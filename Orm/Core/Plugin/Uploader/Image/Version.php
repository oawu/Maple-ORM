<?php

namespace Orm\Core\Plugin\Uploader\Image;

final class Version {
  private string $_method = '';
  private array $_args = [];

  public static function create(): self { // php8 -> return static
    return new static();
  }

  private function __construct() {}

  public function setMethod(string $method): self {
    $this->_method = $method;
    return $this;
  }
  public function setArgs(...$args): self {
    $this->_args = $args;
    return $this;
  }
  public function getMethod(): string {
    return $this->_method;
  }
  public function getArgs(): array {
    return $this->_args;
  }
}
