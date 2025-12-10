<?php

namespace Orm\Core;

use \Orm\Model;
use \Orm\Helper;
use \Orm\Core\Plugin;
use \Orm\Core\Plugin\DateTime;
use \Orm\Core\Plugin\Binary;

final class Column {
  public static function create(array $row): ?Column { // php8 -> return static
    $row = array_change_key_case($row, CASE_LOWER);

    $type = $row['type'] ?? null;
    if (!(is_string($type) && $type !== '')) {
      return null;
    }
    $type = self::_parseIntType($type);
    if ($type === null) {
      return null;
    }

    $name = $row['field'] ?? null;
    if (!(is_string($name) && $name !== '')) {
      return null;
    }

    $isNullable = $row['null'] ?? null;
    $isNullable = is_string($isNullable) && strtolower($isNullable) === 'yes';

    $isPrimary = $row['key'] ?? null;
    $isPrimary = is_string($isPrimary) && strtolower($isPrimary) == 'pri';

    $isAutoIncrement = $row['extra'] ?? null;
    $isAutoIncrement = is_string($isAutoIncrement) && strtolower($isAutoIncrement) == 'auto_increment';

    $defaultValue = $row['default'] ?? null;

    $isUnsigned = $type['isUnsigned'];
    $isZerofill = $type['isZerofill'];
    $length = $type['length'];
    $items = $type['items'];
    $precision = $type['precision'];
    $scale = $type['scale'];

    $type = $type['type'];

    if ($type == 'timestamp') {
      $type = 'datetime';
    }

    if ($type == 'integer') {
      $type = 'int';
    }
    if ($type == 'varchar' && $length === null) {
      return null;
    }

    return new static($type, $name, $isNullable, $isPrimary, $isAutoIncrement, $defaultValue, $isUnsigned, $isZerofill, $length, $items, $precision, $scale);
  }

  private static function _parseIntType(string $definition): ?array {
    $definition = preg_replace('/\s+/', ' ', trim($definition));

    $pattern = '/^enum\s*\(\s*(?P<items>.+)\s*\)$/i';
    if (preg_match($pattern, $definition, $enumMatches)) {
      $items = [];
      preg_match_all("/'([^']*)'/", $enumMatches['items'], $itemMatches);
      if (!empty($itemMatches[1])) {
        $items = $itemMatches[1];
      }

      return [
        'type' => 'enum',
        'length' => null,
        'isUnsigned' => null,
        'isZerofill' => null,
        'items' => $items,
        'precision' => null,
        'scale' => null,
      ];
    }


    $pattern = '/^decimal\s*\(\s*(?P<precision>\d+)\s*,\s*(?P<scale>\d+)\s*\)(?:\s*(?P<unsigned>unsigned))?(?:\s*(?P<zerofill>zerofill))?$/i';
    if (preg_match($pattern, $definition, $decimalMatches)) {
      return [
        'type' => 'decimal',
        'length' => null,
        'isUnsigned' => isset($decimalMatches['zerofill']) || isset($decimalMatches['unsigned']),
        'isZerofill' => isset($decimalMatches['zerofill']),
        'items' => null,
        'precision' => (int)$decimalMatches['precision'],
        'scale' => (int)$decimalMatches['scale'],
      ];
    }

    $pattern = '/^(?P<type>\w+)(?:\s*\(\s*(?P<length>\d+)\s*\))?(?:\s*(?P<unsigned>unsigned))?(?:\s*(?P<zerofill>zerofill))?$/i';
    if (preg_match($pattern, $definition, $matches)) {
      return [
        'type' => strtolower($matches['type']), // 確保類型名稱是小寫
        'length' => isset($matches['length']) ? (int)$matches['length'] : null,
        'isUnsigned' => isset($matches['zerofill']) || isset($matches['unsigned']),
        'isZerofill' => isset($matches['zerofill']),
        'items' => null,
        'precision' => null,
        'scale' => null,
      ];
    }

    return null;
  }

  private string $_type;
  private string $_name;
  private bool $_isNullable;
  private bool $_isPrimary;
  private bool $_isAutoIncrement;
  private ?string $_defaultValue;
  private ?bool $_isUnsigned;
  private ?bool $_isZerofill;
  private ?int $_length;
  private ?array $_items;
  private ?int $_precision;
  private ?int $_scale;

  private function __construct(string $type, string $name, bool $isNullable, bool $isPrimary, bool $isAutoIncrement, ?string $defaultValue, ?bool $isUnsigned, ?bool $isZerofill, ?int $length, ?array $items, ?int $precision, ?int $scale) {
    $this->_type = $type;
    $this->_name = $name;
    $this->_isNullable = $isNullable;
    $this->_isPrimary = $isPrimary;
    $this->_isAutoIncrement = $isAutoIncrement;
    $this->_defaultValue = $defaultValue;

    $this->_isUnsigned = $isUnsigned;
    $this->_isZerofill = $isZerofill;
    $this->_length = $length;
    $this->_items = $items;
    $this->_precision = $precision;
    $this->_scale = $scale;
  }

  public function updateWith($oldValue, $newValue) {
    if ($newValue === null && !$this->getIsNullable() && !$this->getIsAutoIncrement()) {
      throw new \Exception('欄位「' . $this->getName() . '」不可以為 NULL');
    }

    if ($oldValue instanceof Plugin) {
      try {
        return $oldValue->updateValue($newValue, false);
      } catch (\Exception $error) {
        throw new \Exception('欄位「' . $this->getName() . '」值' . $error->getMessage());
      }
    }

    $type = $this->getType();

    switch ($type) {
      case 'tinyint':
      case 'smallint':
      case 'mediumint':
      case 'int':
      case 'bigint':
        return $this->_parseInt($newValue);

      case 'float':
      case 'double':
      case 'numeric':
      case 'decimal':
      case 'dec':
        return $this->_parseFloat($newValue);

      case 'enum':
        return $this->_parseEnum($newValue);

      case 'json':
        return $newValue;

      default:
        return $this->_parseString($newValue);
    }

    return $newValue;
  }
  public function initWith(?Model $model, $value, ?array $plugin) {
    if ($value === null && !$this->getIsNullable() && !$this->getIsAutoIncrement()) {
      throw new \Exception('欄位「' . $this->getName() . '」不可以為 NULL');
    }

    $type = $this->getType();
    if (in_array($type, ['datetime', 'timestamp', 'date', 'time'])) {
      $plugin = [
        'class' => DateTime::class,
        'func' => null,
      ];
    }
    if (in_array($type, ['binary'])) {
      $plugin = [
        'class' => Binary::class,
        'func' => null,
      ];
    }

    if ($plugin !== null) {
      $class = $plugin['class'];
      $func = $plugin['func'] ?? null;

      $allowTypes = $class::allowTypes();
      if ($allowTypes && !in_array($type, $allowTypes)) {
        throw new \Exception('欄位「' . $this->getName() . '」格式為「' . $this->getType() . '」，不適用於「' . $class . '」');
      }

      try {
        return $class::create($model, $this, $value, $func);
      } catch (\Exception $error) {
        throw new \Exception('欄位「' . $this->getName() . '」值' . $error->getMessage());
      }
    }

    switch ($type) {
      case 'tinyint':
      case 'smallint':
      case 'mediumint':
      case 'int':
      case 'bigint':
        return $this->_parseInt($value);

      case 'float':
      case 'double':
      case 'numeric':
      case 'decimal':
      case 'dec':
        return $this->_parseFloat($value);

      case 'enum':
        return $this->_parseEnum($value);

      case 'json':
        return $this->_parseJson($value);

      default:
        return $this->_parseString($value);
    }

    return $value;
  }
  public function getType(): string {
    return $this->_type;
  }
  public function getName(): string {
    return $this->_name;
  }
  public function getIsNullable(): bool {
    return $this->_isNullable;
  }
  public function getIsPrimary(): bool {
    return $this->_isPrimary;
  }
  public function getIsAutoIncrement(): bool {
    return $this->_isAutoIncrement;
  }
  public function getDefaultValue(): ?string {
    return $this->_defaultValue;
  }
  public function getIsUnsigned(): ?bool {
    return $this->_isUnsigned;
  }
  public function getIsZerofill(): ?bool {
    return $this->_isZerofill;
  }
  public function getLength(): ?int {
    return $this->_length;
  }
  public function getItems(): ?array {
    return $this->_items;
  }
  public function getPrecision(): ?int {
    return $this->_precision;
  }
  public function getScale(): ?int {
    return $this->_scale;
  }

  private function _parseInt($value) { // php8 -> $val: string|int|float|null  return int|float|null
    if ($value === null) {
      return null;
    }

    if (is_string($value)) {
      if (preg_match('/^\d+$/', $value)) {
        if (Helper::bccomp($value, (string)PHP_INT_MAX) <= 0) {
          return (int)$value;
        }
        if (Helper::bccomp($value, number_format(PHP_FLOAT_MAX, 0, '', '')) <= 0) {
          return (float)$value;
        }
        return $value;
      }
    }

    if (is_int($value)) {
      return $value;
    }

    if (is_float($value)) {
      if (Helper::bccomp($value, number_format(PHP_FLOAT_MAX, 0, '', '')) <= 0) {
        return (float)$value;
      }
      return number_format($value, 0, '', '');
    }

    return (int)$value;
  }
  private function _parseFloat($value) { // php8 -> $val: string|int|float|null  return float|string|null
    if ($value === null) {
      return null;
    }

    if (is_string($value)) {
      if (preg_match('/^\d+(\.\d+)?$/', $value)) {
        if (Helper::bccomp($value, number_format(PHP_FLOAT_MAX, 0, '', '')) <= 0) {
          return (float)$value;
        }
        return $value;
      }
    }

    if (is_int($value)) {
      return (float)$value;
    }

    if (is_float($value)) {
      if (Helper::bccomp($value, number_format(PHP_FLOAT_MAX, 0, '', '')) <= 0) {
        return (float)$value;
      }
      return number_format($value, 0, '', '');
    }

    return (float)$value;
  }
  private function _parseEnum($value) {
    if ($value === null) {
      return null;
    }

    $value = (string)$value;

    if (!in_array($value, $this->_items)) {
      throw new \Exception('欄位「' . $this->getName() . '」格式為「' . $this->getType() . '」，選項有「' . implode('、', $this->getItems() ?? []) . '」，欄位值「' . $value . '」不在選項內');
    }

    return $value;
  }
  private function _parseJson($value) {
    if ($value === null) {
      return null;
    }

    $json = json_decode($value, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new \Exception('欄位「' . $this->getName() . '」格式為「' . $this->getType() . '」，欄位值「' . $value . '」無法轉為 Json 格式');
    }

    return $json;
  }
  private function _parseString($value) {
    if ($value === null) {
      return null;
    }

    $length = $this->getLength();
    if ($length !== null && mb_strlen($value) > $length) {
      throw new \Exception('欄位「' . $this->getName() . '」格式為「' . $this->getType() . '」，欄位值「' . $value . '」長度超過「' . $this->getLength() . '」');
    }

    return (string)$value;
  }
}
