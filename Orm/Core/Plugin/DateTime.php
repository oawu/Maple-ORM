<?php

namespace Orm\Core\Plugin;

use \Orm\Model;
use \Orm\Core\Column;
use \Orm\Core\Plugin;

final class DateTime extends Plugin {
  public const TYPE_TIME = 'time';
  public const TYPE_DATE = 'date';
  public const TYPE_DATETIME = 'datetime';

  public const FORMAT = [
    self::TYPE_TIME => 'H:i:s',
    self::TYPE_DATE => 'Y-m-d',
    self::TYPE_DATETIME => 'Y-m-d H:i:s',
  ];
  public const ZERO = [
    self::TYPE_TIME => '00:00:00',
    self::TYPE_DATE => '0000-00-00',
    self::TYPE_DATETIME => '0000-00-00 00:00:00',
  ];

  public static function allowTypes(): array {
    return ['datetime', 'timestamp', 'date', 'time'];
  }
  public static function formatByType(string $type): string {
    return self::FORMAT[$type] ?? self::FORMAT[self::TYPE_DATETIME];
  }

  private string $_type;
  private string $_format;
  private ?\DateTime $_datetime;

  public function __construct(?Model $model, Column $column, $value, ?callable $func = null) {
    $type = $column->getType();
    switch ($type) {
      case 'date':
        $this->_type = self::TYPE_DATE;
        break;
      case 'time':
        $this->_type = self::TYPE_TIME;
        break;
      default:
        $this->_type = self::TYPE_DATETIME;
        break;
    }

    $this->_format = self::formatByType($type);

    parent::__construct($model, $column, $value);

    if ($func !== null) {
      $func($this);
    }
  }

  public function __toString(): string {
    return $this->format();
  }
  public function updateValue($value): self {
    if ($value === null) {
      $this->_datetime = null;
      return parent::_setValue($value);
    }

    if ($value instanceof \DateTime) {
      $this->_datetime = $value;
      $value = $this->_datetime->format($this->getFormat());
      return parent::_setValue($value);
    }

    if ($this->isZero($value)) {
      $this->_datetime = null;
      return parent::_setValue($value);
    }

    $format = $this->getFormat();
    $datetime = \DateTime::createFromFormat($format, $value);

    if ($datetime === false) {
      throw new \Exception('「' . $value . '」無法轉為 ' . static::class . ' 格式');
    }

    $this->_datetime = $datetime;
    $value = $this->_datetime->format($this->getFormat());
    return parent::_setValue($value);
  }
  public function getType(): string {
    return $this->_type;
  }
  public function getFormat(): string {
    return $this->_format;
  }
  public function getDatetime(): ?\DateTime {
    return $this->_datetime;
  }
  public function format(?string $format = null, ?string $default = null): ?string {
    // U -> timestamp, 'c' -> ISO 8601 date(2004-02-12T15:19:21+00:00)
    // http://php.net/manual/en/function.date.php

    $datetime = $this->getDatetime();
    if ($datetime === null) {
      return $default;
    }

    $format = $format === null ? $this->getFormat() : $format;
    return $datetime->format($format);
  }
  public function unix(?int $default = null): ?int {
    if (!$this->getDatetime()) {
      return $default;
    }

    $tmp = $this->getType() == self::TYPE_DATE ? strtotime($this->format('Y-m-d 00:00:00')) : $this->format('U');
    return 0 + $tmp;
  }
  public function isZero($value): bool {
    return $value === self::ZERO[$this->getType()];
  }
  public function toSqlString(): ?string {
    $value = $this->getValue();

    if ($value === null) {
      return null;
    }

    if ($this->isZero($value)) {
      return $value;
    }

    return $this->format(null, null);
  }
  public function toArray(bool $isRaw = false) {
    return $isRaw ? $this->getValue() : $this->format();
  }

  protected function _setValue($value): self {
    if ($value === 'CURRENT_TIMESTAMP') {
      $type = $this->getType();
      $value = \date(self::FORMAT[$type]);
    }

    return $this->updateValue($value);
  }
}
