<?php

namespace Orm\Core\Thumbnail;

use \Orm\Core\Thumbnail;

final class Gd extends Thumbnail {
  public static function getImage($format, $path) {
    switch ($format) {
      case 'gif':
        return imagecreatefromgif($path);
      case 'jpg':
        return imagecreatefromjpeg($path);
      case 'png':
        return imagecreatefrompng($path);
      case 'webp':
        return imagecreatefromwebp($path);
      default:
        throw new \Exception('找尋不到符合的格式，或者不支援此檔案格式！格式：' . $format);
    }
  }
  public static function block9(array $files, string $path, ?bool $interlace = true, ?int $quality = null): bool {
    if (count($files) < 9) {
      throw new \Exception('參數錯誤，檔案數量要大於等於 9，數量：' . count($files));
    }

    $positions = [
      ['left' =>   2, 'top' =>   2, 'width' => 130, 'height' => 130],
      ['left' => 134, 'top' =>   2, 'width' =>  64, 'height' =>  64],
      ['left' => 200, 'top' =>   2, 'width' =>  64, 'height' =>  64],
      ['left' => 134, 'top' =>  68, 'width' =>  64, 'height' =>  64],
      ['left' => 200, 'top' =>  68, 'width' =>  64, 'height' =>  64],
      ['left' =>   2, 'top' => 134, 'width' =>  64, 'height' =>  64],
      ['left' =>  68, 'top' => 134, 'width' =>  64, 'height' =>  64],
      ['left' => 134, 'top' => 134, 'width' =>  64, 'height' =>  64],
      ['left' => 200, 'top' => 134, 'width' =>  64, 'height' =>  64],
    ];

    $image = imagecreatetruecolor(266, 200);
    imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));

    $c = count($positions);
    for ($i = 0; $i < $c; $i++) {
      imagecopymerge(
        $image,
        self::create($files[$i])
          ->adaptiveResizeQuadrant($positions[$i]['width'], $positions[$i]['height'])
          ->_getImage(),

        $positions[$i]['left'],
        $positions[$i]['top'],
        0,
        0,
        $positions[$i]['width'],
        $positions[$i]['height'],
        100
      );
    }

    if ($interlace !== null) {
      imageinterlace($image, $interlace ? 1 : 0);
    }

    switch (pathinfo($path, PATHINFO_EXTENSION)) {
      case 'jpg':
        return imagejpeg($image, $path, $quality !== null ? $quality : -1);
      case 'gif':
        return imagegif($image, $path);
      case 'webp':
        return imagewebp($image, $path, $quality !== null ? $quality : -1);
      case 'png':
      default:
        return imagepng($image, $path, $quality !== null ? $quality : -1);
    }
    return false;
  }
  public static function photos(array $files, string $path, bool $interlace = true, ?int $quality = null): bool {
    if (count($files) <= 0) {
      throw new \Exception('參數錯誤！檔案數量要大於等於 1，數量：' . count($files));
    }

    $w = 1200;
    $h = 630;

    $image = imagecreatetruecolor($w, $h);
    imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));

    $spacing = 5;
    $positions = [];

    switch (count($files)) {
      case 1:
        $positions = [['left' => 0, 'top' => 0, 'width' => $w, 'height' => $h]];
        break;
      case 2:
        $positions = [['left' => 0, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h], ['left' => $w / 2 + $spacing, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h]];
        break;
      case 3:
        $positions = [['left' => 0, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h], ['left' => $w / 2 + $spacing, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 2 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing]];
        break;
      case 4:
        $positions = [['left' => 0, 'top' => 0, 'width' => $w, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]];
        break;
      case 5:
        $positions = [['left' => 0, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 2 + $spacing, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]];
        break;
      case 6:
        $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]];
        break;
      case 7:
        $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]];
        break;
      case 8:
        $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing]];
        break;
      default:
      case 9:
        $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing]];
        break;
    }

    $c = count($positions);
    for ($i = 0; $i < $c; $i++) {
      imagecopymerge(
        $image,
        self::create($files[$i])->adaptiveResizeQuadrant($positions[$i]['width'], $positions[$i]['height'])->_getImage(),

        $positions[$i]['left'],
        $positions[$i]['top'],
        0,
        0,
        $positions[$i]['width'],
        $positions[$i]['height'],
        100
      );
    }

    if ($interlace !== null) {
      imageinterlace($image, $interlace ? 1 : 0);
    }

    switch (pathinfo($path, PATHINFO_EXTENSION)) {
      case 'jpg':
        return imagejpeg($image, $path, $quality !== null ? $quality : -1);
      case 'gif':
        return imagegif($image, $path);
      case 'webp':
        return imagewebp($image, $path, $quality !== null ? $quality : -1);
      case 'png':
      default:
        return imagepng($image, $path, $quality !== null ? $quality : -1);
    }
    return false;
  }

  protected static function _getAllows(): array {
    return ['gif', 'jpg', 'png', 'webp'];
  }
  protected static function _getImageDimension($image): array {
    return [imagesx($image), imagesy($image)];
  }

  private array $_options = [
    'resizeUp' => true,
    'interlace' => true,
    'jpgQuality' => 90,
    'pngQuality' => 1,
    'webpQuality' => 90,
    'preserveAlpha' => true,
    'preserveTransparency' => true,
    'alphaMaskColor' => [255, 255, 255],
    'transparencyMaskColor' => [0, 0, 0]
  ];

  public function __construct($path, $options = []) {
    parent::__construct($path);
    $this->_options = array_merge($this->_options, array_intersect_key($options, $this->_options));
  }

  public function save(string $path): bool {
    if (!$this->_getIsDirty()) {
      return copy($this->getPath(), $path);
    }

    $image = $this->_getImage();
    imageinterlace($image, $this->_options['interlace'] ? 1 : 0);

    switch ($this->getExtension()) {
      case 'jpg':
        return imagejpeg($image, $path, $this->_options['jpgQuality']);
      case 'gif':
        return imagegif($image, $path);
      case 'png':
        return imagepng($image, $path, $this->_options['pngQuality']);
      case 'webp':
        return imagewebp($image, $path, $this->_options['webpQuality']);
      default:
        return false;
    }
  }
  public function pad(int $width, int $height, $color = [255, 255, 255]): self {
    self::_checkWH($width, $height);

    $dimension = $this->getDimension();
    if ($width == $dimension[0] && $height == $dimension[1]) {
      return $this;
    }

    self::_checkC($color);

    if ($width < $dimension[0] || $height < $dimension[1]) {
      $this->resize($width, $height);
      $dimension = $this->getDimension();
    }

    $newImage = function_exists('imagecreatetruecolor')
      ? imagecreatetruecolor($width, $height)
      : imagecreate($width, $height);

    imagefill($newImage, 0, 0, imagecolorallocate($newImage, $color[0], $color[1], $color[2]));


    return $this->_copyReSampled(
      $newImage,
      $this->_getImage(),
      intval(($width - $dimension[0]) / 2),
      intval(($height - $dimension[1]) / 2),
      0,
      0,
      $dimension[0],
      $dimension[1],
      $dimension[0],
      $dimension[1]
    );
  }
  public function resizeByWidth(int $width): self {
    return $this->resize($width, $width, 'w');
  }
  public function resizeByHeight(int $height): self {
    return $this->resize($height, $height, 'h');
  }
  public function resize(int $width, int $height, string $method = 'auto'): self {
    self::_checkWH($width, $height);

    $dimension = $this->getDimension();
    if ($width == $dimension[0] && $height == $dimension[1]) {
      return $this;
    }

    $newDimension = self::_createNewDimension($this->_options['resizeUp'], $dimension, $width, $height);

    $method = strtolower(trim($method));
    switch ($method) {
      case 'w':
      case 'width':
        $newDimension = self::_calcWidth($dimension, $newDimension);
        break;

      case 'h':
      case 'height':
        $newDimension = self::_calcHeight($dimension, $newDimension);
        break;

      case 'a':
      case 'auto':
      default:
        $newDimension = self::_calcImageSize($dimension, $newDimension);
        break;
    }

    $newImage = function_exists('imagecreatetruecolor')
      ? imagecreatetruecolor($newDimension[0], $newDimension[1])
      : imagecreate($newDimension[0], $newDimension[1]);

    $newImage = $this->_preserveAlpha($newImage);

    return $this->_copyReSampled(
      $newImage,
      $this->_getImage(),
      0,
      0,
      0,
      0,
      $newDimension[0],
      $newDimension[1],
      $dimension[0],
      $dimension[1]
    );
  }
  public function adaptiveResizePercent(int $width, int $height, float $percent): self {
    self::_checkWH($width, $height);
    self::_checkP($percent);

    $dimension = $this->getDimension();
    if ($width == $dimension[0] && $height == $dimension[1]) {
      return $this;
    }

    $newDimension = self::_createNewDimension($this->_options['resizeUp'], $dimension, $width, $height);
    $newDimension = self::_calcImageSizeStrict($dimension, $newDimension);

    $this->resize($newDimension[0], $newDimension[1]);
    $dimension = $this->getDimension();

    $newDimension = self::_createNewDimension($this->_options['resizeUp'], $dimension, $width, $height);

    $newImage = function_exists('imagecreatetruecolor')
      ? imagecreatetruecolor($newDimension[0], $newDimension[1])
      : imagecreate($newDimension[0], $newDimension[1]);

    $newImage = $this->_preserveAlpha($newImage);

    $cropX = 0;
    $cropY = 0;

    if ($dimension[0] > $newDimension[0]) {
      $cropX = intval($percent * ($dimension[0] - $newDimension[0]));
    } else if ($dimension[1] > $newDimension[1]) {
      $cropY = intval($percent * ($dimension[1] - $newDimension[1]));
    }

    return $this->_copyReSampled(
      $newImage,
      $this->_getImage(),
      0,
      0,
      $cropX,
      $cropY,
      $newDimension[0],
      $newDimension[1],
      $newDimension[0],
      $newDimension[1]
    );
  }
  public function adaptiveResize(int $width, int $height): self {
    return $this->adaptiveResizePercent($width, $height, 0.5);
  }
  public function scale(float $percent = 0): self {
    if ($percent == 0) {
      throw new \Exception('百分比例錯誤，百分比不能為 0 的浮點數，百分比：' . $percent);
    }
    if ($percent == 1) {
      return $this;
    }

    $newDimension = self::_calcImageSizePercent($percent, $this->getDimension());
    return $this->resize($newDimension[0], $newDimension[1]);
  }
  public function crop(int $startX, int $startY, int $width, int $height): self {
    self::_checkWH($width, $height);
    self::_checkXY($startX, $startY);

    $dimension = $this->getDimension();
    if ($startX == 0 && $startY == 0 && $width == $dimension[0] && $height == $dimension[1]) {
      return $this;
    }

    $width  = $dimension[0] < $width ? $dimension[0] : $width;
    $height = $dimension[1] < $height ? $dimension[1] : $height;

    if (($startX + $width) > $dimension[0]) {
      $startX = $dimension[0] - $width;
    }
    if (($startY + $height) > $dimension[1]) {
      $startY = $dimension[1] - $height;
    }

    $newImage = function_exists('imagecreatetruecolor')
      ? imagecreatetruecolor($width, $height)
      : imagecreate($width, $height);

    $newImage = $this->_preserveAlpha($newImage);

    return $this->_copyReSampled(
      $newImage,
      $this->_getImage(),
      0,
      0,
      $startX,
      $startY,
      $width,
      $height,
      $width,
      $height
    );
  }
  public function cropCenter(int $width, int $height): self {
    self::_checkWH($width, $height);

    $dimension = $this->getDimension();
    if ($width == $dimension[0] && $height == $dimension[1]) {
      return $this;
    }

    if ($width > $dimension[0] && $height > $dimension[1]) {
      return $this->pad($width, $height);
    }

    $startX = intval(($dimension[0] - $width) / 2);
    $startY = intval(($dimension[1] - $height) / 2);

    $width  = $dimension[0] < $width ? $dimension[0] : $width;
    $height = $dimension[1] < $height ? $dimension[1] : $height;

    return $this->crop($startX, $startY, $width, $height);
  }
  public function rotate(int $degree, $color = [255, 255, 255]): self {
    if (!function_exists('imagerotate')) {
      throw new \Exception('沒有載入 imagerotate 函式');
    }

    self::_checkC($color);
    self::_checkD($degree);

    $temp = function_exists('imagecreatetruecolor')
      ? imagecreatetruecolor(1, 1)
      : imagecreate(1, 1);

    $newImage = imagerotate($this->_getImage(), 0 - $degree, imagecolorallocate($temp, $color[0], $color[1], $color[2]));
    return $this->_updateImage($newImage);
  }
  public function adaptiveResizeQuadrant(int $width, int $height, string $item = 'c'): self {
    self::_checkWH($width, $height);

    $dimension = $this->getDimension();
    if ($width == $dimension[0] && $height == $dimension[1]) {
      return $this;
    }

    $newDimension = self::_createNewDimension($this->_options['resizeUp'], $dimension, $width, $height);
    $newDimension = self::_calcImageSizeStrict($dimension, $newDimension);

    $this->resize($newDimension[0], $newDimension[1]);
    $dimension = $this->getDimension();

    $newDimension = self::_createNewDimension($this->_options['resizeUp'], $dimension, $width, $height);

    $newImage = function_exists('imagecreatetruecolor')
      ? imagecreatetruecolor($newDimension[0], $newDimension[1])
      : imagecreate($newDimension[0], $newDimension[1]);

    $newImage = $this->_preserveAlpha($newImage);

    $cropX = 0;
    $cropY = 0;
    $item = strtolower(trim($item));

    if ($dimension[0] > $newDimension[0]) {
      switch ($item) {
        case 'l':
        case 'left':
          $cropX = 0;
          break;

        case 'r':
        case 'right':
          $cropX = intval($dimension[0] - $newDimension[0]);
          break;

        case 'c':
        case 'center':
        default:
          $cropX = intval(($dimension[0] - $newDimension[0]) / 2);
          break;
      }
    } else if ($dimension[1] > $newDimension[1]) {
      switch ($item) {
        case 't':
        case 'top':
          $cropY = 0;
          break;

        case 'b':
        case 'bottom':
          $cropY = intval($dimension[1] - $newDimension[1]);
          break;

        case 'c':
        case 'center':
        default:
          $cropY = intval(($dimension[1] - $newDimension[1]) / 2);
          break;
      }
    }

    return $this->_copyReSampled(
      $newImage,
      $this->_getImage(),
      0,
      0,
      $cropX,
      $cropY,
      $newDimension[0],
      $newDimension[1],
      $newDimension[0],
      $newDimension[1]
    );
  }

  private function _copyReSampled($newImage, $oldImage, int $newX, int $newY, int $oldX, int $oldY, int $newWidth, int $newHeight, int $oldWidth, int $oldHeight): self {
    imagecopyresampled(
      $newImage,
      $oldImage,
      $newX,
      $newY,
      $oldX,
      $oldY,
      $newWidth,
      $newHeight,
      $oldWidth,
      $oldHeight
    );

    return $this->_updateImage($newImage);
  }
  private function _preserveAlpha($image) {
    $ext = $this->getExtension();

    if ($ext == 'png' && $this->_options['preserveAlpha'] === true) {
      imagealphablending($image, false);
      imagefill($image, 0, 0, imagecolorallocatealpha($image, $this->_options['alphaMaskColor'][0], $this->_options['alphaMaskColor'][1], $this->_options['alphaMaskColor'][2], 0));
      imagesavealpha($image, true);
    }

    if ($ext == 'gif' && $this->_options['preserveTransparency'] === true) {
      imagecolortransparent($image, imagecolorallocate($image, $this->_options['transparencyMaskColor'][0], $this->_options['transparencyMaskColor'][1], $this->_options['transparencyMaskColor'][2]));
      imagetruecolortopalette($image, true, 256);
    }

    return $image;
  }
}
