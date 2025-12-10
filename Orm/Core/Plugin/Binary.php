<?php

namespace Orm\Core\Plugin;

use \Orm\Model;
use \Orm\Core\Column;
use \Orm\Core\Plugin;

final class Binary extends Plugin {
  public static function allowTypes(): array {
    return ['binary'];
  }

  public function __construct(?Model $model, Column $column, $value, ?callable $func = null) {
    parent::__construct($model, $column, $value);
  }

  public function __toString(): string {
    return base64_encode($this->getValue());
  }

  public function updateValue($value): self {
    return parent::_setValue($value);
  }

  public function toSqlString(): ?string {
    return $this->getValue();
  }

  public function toArray(bool $isRaw = false) {
    return $isRaw ? base64_encode($this->getValue()) : $this->getValue();
  }
}
