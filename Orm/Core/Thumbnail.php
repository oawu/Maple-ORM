<?php

namespace Orm\Core;

use \Orm\Core\Thumbnail\Gd;
use \Orm\Core\Thumbnail\Imagick;

abstract class Thumbnail {
  abstract public function save(string $path): bool;
  abstract protected static function _getAllows(): array;
  abstract protected static function _getImageDimension($image): array;

  private static $_extensions = [
    'jpg' => ['image/jpeg', 'image/pjpeg'],
    'gif' => ['image/gif'],
    'png' => ['image/png', 'image/x-png'],
    'webp' => ['image/webp'],

    'ico' => ['image/x-icon'],
    'jpeg' => ['image/jpeg', 'image/pjpeg'],
    'jpe' => ['image/jpeg', 'image/pjpeg'],
    'bmp' => ['image/bmp', 'image/x-windows-bmp'],
    'svg' => ['image/svg+xml'],
    'heic' => ['image/heic', 'image/heif'],
  ];

  public static function create(string $path, array $options = []) { // php8 -> return static
    return new static($path, $options);
  }

  protected static function _colorHex2Rgb(string $hex): array {
    $hex = str_replace('#', '', $hex);

    if (strlen($hex) == 3) {
      return [
        hexdec(substr($hex, 0, 1) . substr($hex, 0, 1)),
        hexdec(substr($hex, 1, 1) . substr($hex, 1, 1)),
        hexdec(substr($hex, 2, 1) . substr($hex, 2, 1))
      ];
    }
    if (strlen($hex) == 6) {
      return [
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2))
      ];
    }

    return [];
  }
  protected static function _calcWidth(array $oldDimension, array $newDimension): array {
    $newWidthPercentage = 100 * $newDimension[0] / $oldDimension[0];
    $height = ceil($oldDimension[1] * $newWidthPercentage / 100);
    return [$newDimension[0], $height];
  }
  protected static function _calcHeight(array $oldDimension, array $newDimension): array {
    $newHeightPercentage  = 100 * $newDimension[1] / $oldDimension[1];
    $width = ceil($oldDimension[0] * $newHeightPercentage / 100);
    return [$width, $newDimension[1]];
  }
  protected static function _calcImageSize(array $oldDimension, array $newDimension): array {
    $newSize = [$oldDimension[0], $oldDimension[1]];
    if ($newDimension[0] > 0) {
      $newSize = self::_calcWidth($oldDimension, $newDimension);
      $newDimension[1] > 0 && $newSize[1] > $newDimension[1] && $newSize = self::_calcHeight($oldDimension, $newDimension);
    }
    if ($newDimension[1] > 0) {
      $newSize = self::_calcHeight($oldDimension, $newDimension);
      $newDimension[0] > 0 && $newSize[0] > $newDimension[0] && $newSize = self::_calcWidth($oldDimension, $newDimension);
    }
    return $newSize;
  }
  protected static function _calcImageSizeStrict(array $oldDimension, array $newDimension): array {
    $newSize = [$newDimension[0], $newDimension[1]];

    if ($newDimension[0] >= $newDimension[1]) {
      if ($oldDimension[0] > $oldDimension[1]) {
        $newSize = self::_calcHeight($oldDimension, $newDimension);
        $newSize[0] < $newDimension[0] && $newSize = self::_calcWidth($oldDimension, $newDimension);
      } else if ($oldDimension[1] >= $oldDimension[0]) {
        $newSize = self::_calcWidth($oldDimension, $newDimension);
        $newSize[1] < $newDimension[1] && $newSize = self::_calcHeight($oldDimension, $newDimension);
      }
      return $newSize;
    }

    if ($oldDimension[0] >= $oldDimension[1]) {
      $newSize = self::_calcWidth($oldDimension, $newDimension);
      $newSize[1] < $newDimension[1] && $newSize = self::_calcHeight($oldDimension, $newDimension);
    } else if ($oldDimension[1] > $oldDimension[0]) {
      $newSize = self::_calcHeight($oldDimension, $newDimension);
      $newSize[0] < $newDimension[0] && $newSize = self::_calcWidth($oldDimension, $newDimension);
    }
    return $newSize;
  }
  protected static function _calcImageSizePercent(float $percent, array $dimension): array {
    return [ceil($dimension[0] * $percent), ceil($dimension[1] * $percent)];
  }
  protected static function _createNewDimension(bool $resizeUp, array $dimension, int $width, int $height): array {
    if (!$resizeUp && ($width > $dimension[0])) {
      $width = $dimension[0];
    }
    if (!$resizeUp && ($height > $dimension[1])) {
      $height = $dimension[1];
    }
    return [$width, $height];
  }
  protected static function _checkXY(int $startX, int $startY) {
    if ($startX < 0 || $startY < 0) {
      throw new \Exception('起始點錯誤，水平、垂直的起始點一定要大於 0，水平點：' . $startX . '，垂直點：' . $startY);
    }
  }
  protected static function _checkWH(int $width, int $height) {
    if ($width <= 0 || $height <= 0) {
      throw new \Exception('新尺寸錯誤，尺寸寬高一定要大於 0，寬：' . $width . '，高：' . $height);
    }
  }
  protected static function _checkF(string $font, int $size) {
    if (!is_readable($font)) {
      throw new \Exception('參數錯誤，字型檔案不存在或不可讀，字型：' . $font);
    }
    if ($size <= 0) {
      throw new \Exception('參數錯誤，字體大小一定要大於 0，字體大小：' . $size);
    }
  }
  protected static function _checkP(float $percent) {
    if ($percent < 0 || $percent > 1) {
      throw new \Exception('百分比例錯誤，百分比要在 0 ~ 1 之間的浮點數，百分比：' . $percent);
    }
  }
  protected static function _checkC(&$color) {
    if (is_string($color)) {
      $color = self::_colorHex2Rgb($color);
    }

    if (is_array($color) && (count(array_filter($color, fn($color) => $color >= 0 && $color <= 255)) == 3)) {
      return true;
    }

    throw new \Exception('色碼格式錯誤，目前只支援字串 HEX、RGB 陣列格式');
  }
  protected static function _checkD(&$degree) {
    $degree = $degree % 360;
  }

  private static function _getExtensionByMime(string $mime): ?string {
    static $_extensions = [];

    if (array_key_exists($mime, $_extensions)) {
      return $_extensions[$mime];
    }

    foreach (self::$_extensions as $ext => $mimes) {
      if (in_array($mime, $mimes)) {
        return $_extensions[$mime] = $ext;
      }
    }

    return $_extensions[$mime] = null;
  }

  private $_image = null;
  private string $_path = '';
  private string $_mime = '';
  private string $_extension = '';
  private array $_dimension = [];
  private bool $_isDirty = false;

  public function __construct($path) {
    if (!function_exists('mime_content_type')) {
      throw new \Exception('mime_content_type 函式不存在');
    }

    if (!(is_file($path) && is_readable($path))) {
      throw new \Exception('檔案不可讀取，或者不存在，檔案路徑：' . $path);
    }

    $mime = mime_content_type($path);
    if ($mime === false || $mime === '') {
      throw new \Exception('取不到檔案的 mime 格式，檔案路徑：' . $path);
    }
    $mime = strtolower($mime);

    $extension = self::_getExtensionByMime($mime);
    if ($extension === null) {
      throw new \Exception('取不到符合的格式！Mime：' . $mime);
    }

    if ($allows = static::_getAllows()) {
      if (!in_array($extension, $allows)) {
        throw new \Exception('不支援此檔案格式，格式：' . $extension . '，目前只允許：' . implode('、', $allows));
      }
    }

    $class = static::class;

    $image = $class == Imagick::class
      ? new \Imagick($path)
      : Gd::getImage($extension, $path);

    if ($image === null) {
      throw new \Exception('產生 image 物件失敗');
    }

    $this->_path = $path;
    $this->_mime = $mime;
    $this->_extension = $extension;
    $this->_updateImage($image);
    $this->_isDirty = false;
  }

  public function getPath(): string {
    return $this->_path;
  }
  public function getExtension(): string {
    return $this->_extension;
  }
  public function getMime(): string {
    return $this->_mime;
  }
  public function getDimension(): array {
    return $this->_dimension;
  }

  protected function _updateImage($image): self {
    $this->_isDirty = true;
    $this->_image = $image;
    $this->_dimension = static::_getImageDimension($image);
    return $this;
  }
  protected function _getImage() {
    return $this->_image;
  }
  protected function _getIsDirty(): bool {
    return $this->_isDirty;
  }
}
