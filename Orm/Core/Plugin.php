<?php

namespace Orm\Core;

use \Orm\Model;

abstract class Plugin {
  abstract public function toArray(bool $isRaw = false);
  abstract public function toSqlString(): ?string;
  abstract public function updateValue($value): self; // php8 -> return static
  abstract public function __toString(): string;
  abstract public static function allowTypes(): array;

  static public function create(...$args) { // php8 -> return static
    return new static(...$args);
  }

  private $_value; // null | any
  private ?Model $_model = null; // Model
  private Column $_column; // Column

  public function __construct(?Model $model, Column $column, $value) {
    $this->_model = $model;
    $this->_column = $column;
    $this->_setValue($value);
  }

  public function getValue() {
    return $this->_value;
  }
  public function getModel(): ?Model {
    return $this->_model;
  }
  public function getColumn(): Column {
    return $this->_column;
  }

  protected function _setValue($value): self { // php8 -> return static
    $this->_value = $value;
    return $this;
  }
}
