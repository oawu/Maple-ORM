<?php

namespace Orm\Core\Thumbnail;

use \Orm\Core\Thumbnail;

final class Imagick extends Thumbnail {
  public static function block9(array $files, string $path): bool {
    if (count($files) < 9) {
      throw new \Exception('參數錯誤，檔案數量要大於等於 9，數量：' . count($files));
    }

    $newImage = new \Imagick();
    $newImage->newImage(266, 200, new \ImagickPixel('white'));
    $newImage->setFormat(pathinfo($path, PATHINFO_EXTENSION));

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

    $c = count($positions);
    for ($i = 0; $i < $c; $i++) {
      $newImage->compositeImage(
        self::create($files[$i])
          ->adaptiveResizeQuadrant($positions[$i]['width'], $positions[$i]['height'])
          ->_getImage(),
        \Imagick::COMPOSITE_DEFAULT,
        $positions[$i]['left'],
        $positions[$i]['top']
      );
    }

    return $newImage->writeImages($path, true);
  }
  public static function photos(array $files, string $path): bool {
    if (count($files) <= 0) {
      throw new \Exception('參數錯誤，檔案數量要大於等於 1，數量：' . count($files));
    }

    $w = 1200;
    $h = 630;

    $newImage = new \Imagick();
    $newImage->newImage($w, $h, new \ImagickPixel('white'));
    $newImage->setFormat(pathinfo($path, PATHINFO_EXTENSION));

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
      $newImage->compositeImage(
        self::create($files[$i])
          ->adaptiveResizeQuadrant($positions[$i]['width'], $positions[$i]['height'])
          ->_getImage(),
        \Imagick::COMPOSITE_DEFAULT,
        $positions[$i]['left'],
        $positions[$i]['top']
      );
    }

    return $newImage->writeImages($path, true);
  }
  public static function getAnalysisDatas(string $path, int $limit = 10, float $range = 69.28203222525013): array {
    if ($limit <= 0) {
      throw new \Exception('參數錯誤，分析數量一定要大於 0，分析數量：' . $limit);
    }

    $extension = self::create($path)->getExtension();

    $temp = new \Imagick($path);
    $dimension = static::_getImageDimension($temp);
    $dimension = self::_calcImageSize($dimension, self::_createNewDimension(true, $dimension, 64, 64));

    $temp = $temp->coalesceImages();

    if ($extension == 'gif') {
      do {
        $temp->thumbnailImage($dimension[0], $dimension[1], false);
      } while ($temp->nextImage() || !$temp = $temp->deconstructImages());
    } else {
      $temp->thumbnailImage($dimension[0], $dimension[1], false);
    }

    $pixels = [];

    $it = $temp->getPixelIterator();
    $it->resetIterator();
    while ($row = $it->getNextIteratorRow()) {
      foreach ($row as $pixel) {
        $pixels[] = $pixel->getColor();
      }
    }

    $p1 = array_splice($pixels, 0, count($pixels) / 2);
    $p1 = array_reverse($p1);
    $p2 = array_values($pixels);
    $c = max(count($p1), count($p2));

    $pixels = [];
    for ($i = 0; $i < $c; $i++) {
      if (isset($p2[$i])) {
        $pixels[] = $p2[$i];
      }
      if (isset($p1[$i])) {
        $pixels[] = $p1[$i];
      }
    }

    $colors = [];
    foreach ($pixels as $pixel) {
      $r = $pixel['r'];
      $g = $pixel['g'];
      $b = $pixel['b'];

      if (!$colors) {
        $colors[] = ['color' => ['r' => $r, 'g' => $g, 'b' => $b], 'colors' => [['r' => $r, 'g' => $g, 'b' => $b]]];
        continue;
      }

      // 最長距離 441.6729555992689

      $c = count($colors);
      $x = false;

      for ($i = 0; $i < $c; $i++) {
        $color = $colors[$i]['color'];
        $val = abs(sqrt(pow($color['r'] - $r, 2) + pow($color['g'] - $g, 2) + pow($color['b'] - $b, 2)));

        if ($val <= $range) {
          $x = true;
          array_push($colors[$i]['colors'], ['r' => $r, 'g' => $g, 'b' => $b]);
          break;
        }
      }
      if (!$x) {
        $colors[] = ['color' => ['r' => $r, 'g' => $g, 'b' => $b], 'colors' => [['r' => $r, 'g' => $g, 'b' => $b]]];
      }
    }

    $colors = array_map(fn($newColor) => [
      'color' => [
        'r' => ($c = count($tmps = array_column($newColor['colors'], 'r'))) > 0 ? round(array_sum($tmps) / $c) : 0,
        'g' => ($c = count($tmps = array_column($newColor['colors'], 'g'))) > 0 ? round(array_sum($tmps) / $c) : 0,
        'b' => ($c = count($tmps = array_column($newColor['colors'], 'b'))) > 0 ? round(array_sum($tmps) / $c) : 0
      ],
      'count' => count($newColor['colors']),
    ], $colors);

    $max = array_sum(array_column($colors, 'count'));

    $colors = array_map(fn($newColor) => [
      'rgb' => [
        max(0, min(255, (int)$newColor['color']['r'])),
        max(0, min(255, (int)$newColor['color']['g'])),
        max(0, min(255, (int)$newColor['color']['b'])),
      ],
      'percent' => $max > 0 ? round(($newColor['count'] / $max) * 100, 1) : 0,
    ], $colors);

    usort($colors, fn($a, $b) => $b['percent'] - $a['percent']);
    return array_slice($colors, 0, $limit);
  }
  public static function saveAnalysis(string $src, string $desc, string $fontPath, int $limit = 10, int $fontSize = 14): bool {
    self::_checkF($fontPath, $fontSize);

    if ($limit <= 0) {
      throw new \Exception('參數錯誤，分析數量一定要大於 0，分析數量：' . $limit);
    }
    $extension = self::create($src)->getExtension();

    if (!$datas = self::getAnalysisDatas($src, $limit)) {
      throw new \Exception('圖像分析錯誤');
    }

    $newImage = new \Imagick();

    foreach ($datas as $data) {
      $newImage->newImage(400, 20, new \ImagickPixel('white'));

      $draw = new \ImagickDraw();
      $draw->setFont($fontPath);
      $draw->setFontSize($fontSize);
      $newImage->annotateImage($draw, 25, 14, 0, 'rgb(' . $data['rgb'][0] . ',' . $data['rgb'][1] . ',' . $data['rgb'][2] . ') ' . $data['percent'] . '%');

      $tile = new \Imagick();
      $tile->newImage(20, 20, new \ImagickPixel('rgb(' . $data['rgb'][0] . ',' . $data['rgb'][1] . ',' . $data['rgb'][2] . ')'));

      $newImage->compositeImage($tile, \Imagick::COMPOSITE_OVER, 0, 0);
    }

    $newImage = $newImage->montageImage(new \imagickdraw(), '1x' . count($datas) . '+0+0', '400x20+4+2>', \Imagick::MONTAGEMODE_UNFRAME, '0x0+3+3');
    $newImage->setImageFormat($extension);
    $newImage->setFormat($extension);
    return $newImage->writeImages($desc, true);
  }

  protected static function _getAllows(): array {
    return ['gif', 'jpg', 'png', 'webp'];
  }
  protected static function _getImageDimension($image): array {
    if (!($image instanceof \Imagick)) {
      throw new \Exception('參數錯誤');
    }

    $imagePage = $image->getImagePage();
    if (is_array($imagePage) && is_numeric($imagePage['width']) && is_numeric($imagePage['height']) && $imagePage['width'] > 0 && $imagePage['height'] > 0) {
      return [
        $imagePage['width'],
        $imagePage['height']
      ];
    }

    $imagePage = $image->getImageGeometry();
    if (is_array($imagePage) && is_numeric($imagePage['width']) && is_numeric($imagePage['height']) && $imagePage['width'] > 0 && $imagePage['height'] > 0) {
      return [
        $imagePage['width'],
        $imagePage['height']
      ];
    }

    throw new \Exception('無法圖片取得尺寸');
  }

  private array $_options = [
    'resizeUp' => true,
  ];

  public function __construct(string $path, array $options = []) {
    parent::__construct($path);
    $this->_options = array_merge($this->_options, array_intersect_key($options, $this->_options));
  }

  public function save(string $path): bool {
    if (!$this->_getIsDirty()) {
      return copy($this->getPath(), $path);
    }
    return $this->_getImage()->writeImages($path, true);
  }
  public function pad(int $width, int $height, $color = 'transparent'): self {
    self::_checkWH($width, $height);

    $dimension = $this->getDimension();
    if ($width == $dimension[0] && $height == $dimension[1]) {
      return $this;
    }

    if ($width < $dimension[0] || $height < $dimension[1]) {
      $this->resize($width, $height);
      $dimension = $this->getDimension();
    }
    $extension = $this->getExtension();

    $newImage = new \Imagick();
    $newImage->setFormat($extension);

    if ($extension == 'gif') {
      $imagick = clone $this->_getImage();
      $imagick = $imagick->coalesceImages();

      do {
        $temp = new \Imagick();
        $temp->newImage($width, $height, new \ImagickPixel($color));
        $temp->compositeImage($imagick, \Imagick::COMPOSITE_DEFAULT, intval(($width - $dimension[0]) / 2), intval(($height - $dimension[1]) / 2));
        $newImage->addImage($temp);
        $newImage->setImageDelay($imagick->getImageDelay());
      } while ($imagick->nextImage());
    } else {
      $imagick = clone $this->_getImage();
      $newImage->newImage($width, $height, new \ImagickPixel($color));
      $newImage->compositeImage($imagick, \Imagick::COMPOSITE_DEFAULT, intval(($width - $dimension[0]) / 2), intval(($height - $dimension[1]) / 2));
    }

    return $this->_updateImage($newImage);
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

    $workingImage = $this->_machiningImageResize($newDimension);
    return $this->_updateImage($workingImage);
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

    $cropX = 0;
    $cropY = 0;

    if ($dimension[0] > $newDimension[0]) {
      $cropX = intval($percent * ($dimension[0] - $newDimension[0]));
    } else if ($dimension[1] > $newDimension[1]) {
      $cropY = intval($percent * ($dimension[1] - $newDimension[1]));
    }

    $workingImage = $this->_machiningImageCrop($cropX, $cropY, $newDimension[0], $newDimension[1]);
    return $this->_updateImage($workingImage);
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

    $workingImage = $this->_machiningImageCrop($startX, $startY, $width, $height);
    return $this->_updateImage($workingImage);
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
  public function rotate(int $degree, $color = 'transparent'): self {
    self::_checkD($degree);

    $workingImage = $this->_machiningImageRotate($degree, $color);
    return $this->_updateImage($workingImage);
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

    $workingImage = $this->_machiningImageCrop($cropX, $cropY, $newDimension[0], $newDimension[1]);
    return $this->_updateImage($workingImage);
  }
  public function filter(float $radius, float $sigma, int $channel = \Imagick::CHANNEL_DEFAULT): self {
    $items = [
      \Imagick::CHANNEL_UNDEFINED,
      \Imagick::CHANNEL_RED,
      \Imagick::CHANNEL_GRAY,
      \Imagick::CHANNEL_CYAN,
      \Imagick::CHANNEL_GREEN,
      \Imagick::CHANNEL_MAGENTA,
      \Imagick::CHANNEL_BLUE,
      \Imagick::CHANNEL_YELLOW,
      \Imagick::CHANNEL_ALPHA,
      \Imagick::CHANNEL_OPACITY,
      \Imagick::CHANNEL_BLACK,
      \Imagick::CHANNEL_INDEX,
      \Imagick::CHANNEL_ALL,
      \Imagick::CHANNEL_DEFAULT
    ];

    if (!in_array($channel, $items)) {
      throw new \Exception('參數錯誤，參數 Channel 格式不正確，Channel：' . $channel);
    }

    $workingImage = $this->_machiningImageFilter($radius, $sigma, $channel);
    return $this->_updateImage($workingImage);
  }
  public function lomography(): self {
    $extension = $this->getExtension();

    $newImage = new \Imagick();
    $newImage->setFormat($extension);
    $dimension = $this->getDimension();

    if ($extension == 'gif') {
      $imagick = clone $this->_getImage();
      $imagick = $imagick->coalesceImages();

      do {
        $temp = new \Imagick();
        $imagick->setimagebackgroundcolor('black');
        $imagick->gammaImage(0.75);
        $imagick->vignetteImage(0, max($dimension[0], $dimension[1]) * 0.2, 0 - ($dimension[0] * 0.05), 0 - ($dimension[1] * 0.05));
        $temp->newImage($dimension[0], $dimension[1], new \ImagickPixel('transparent'));
        $temp->compositeImage($imagick, \Imagick::COMPOSITE_DEFAULT, 0, 0);

        $newImage->addImage($temp);
        $newImage->setImageDelay($imagick->getImageDelay());
      } while ($imagick->nextImage());
    } else {
      $newImage = clone $this->_getImage();
      $newImage->setimagebackgroundcolor('black');
      $newImage->gammaImage(0.75);
      $newImage->vignetteImage(0, max($dimension[0], $dimension[1]) * 0.2, 0 - ($dimension[0] * 0.05), 0 - ($dimension[1] * 0.05));
    }
    return $this->_updateImage($newImage);
  }
  public function addFont(string $text, string $fontPath, int $startX = 0, int $startY = 12, string $color = 'black', int $fontSize = 12, int $alpha = 1, int $degree = 0): self {
    self::_checkF($fontPath, $fontSize);
    self::_checkXY($startX, $startY);
    self::_checkD($degree);

    if ($text === '') {
      throw new \Exception('沒有文字，內容：' . $text);
    }

    if ($alpha < 0 || $alpha > 1) {
      throw new \Exception('參數錯誤，參數 Alpha 一定要是 0 ~ 1，Alpha：' . $alpha);
    }

    $draw = self::_createFont($fontPath, $fontSize, $color, $alpha);
    if (!$draw) {
      throw new \Exception('產生文字物件失敗');
    }

    $extension = $this->getExtension();
    $dimension = $this->getDimension();

    if ($extension == 'gif') {
      $newImage = new \Imagick();
      $newImage->setFormat($extension);

      $imagick = clone $this->_getImage();
      $imagick = $imagick->coalesceImages();

      do {
        $temp = new \Imagick();
        $temp->newImage($dimension[0], $dimension[1], new \ImagickPixel('transparent'));
        $temp->compositeImage($imagick, \Imagick::COMPOSITE_DEFAULT, 0, 0);
        $temp->annotateImage($draw, $startX, $startY, $degree, $text);
        $newImage->addImage($temp);
        $newImage->setImageDelay($imagick->getImageDelay());
      } while ($imagick->nextImage());
    } else {
      $newImage = clone $this->_getImage();
      $newImage->annotateImage($draw, $startX, $startY, $degree, $text);
    }

    return $this->_updateImage($newImage);
  }

  private function _machiningImageRotate(int $degree, $color = 'transparent') {
    $extension = $this->getExtension();

    $newImage = new \Imagick();
    $newImage->setFormat($extension);

    $imagick = clone $this->_getImage();

    if ($extension == 'gif') {
      $imagick->coalesceImages();

      do {
        $temp = new \Imagick();
        $imagick->rotateImage(new \ImagickPixel($color), $degree);
        $newDimension = self::_getImageDimension($imagick);
        $temp->newImage($newDimension[0], $newDimension[1], new \ImagickPixel($color));
        $temp->compositeImage($imagick, \Imagick::COMPOSITE_DEFAULT, 0, 0);
        $newImage->addImage($temp);
        $newImage->setImageDelay($imagick->getImageDelay());
      } while ($imagick->nextImage());
    } else {
      $imagick->rotateImage(new \ImagickPixel($color), $degree);
      $newDimension = self::_getImageDimension($imagick);
      $newImage->newImage($newDimension[0], $newDimension[1], new \ImagickPixel($color));
      $newImage->compositeImage($imagick, \Imagick::COMPOSITE_DEFAULT, 0, 0);
    }
    return $newImage;
  }
  private function _machiningImageResize(array $newDimension): \Imagick {
    $newImage = clone $this->_getImage();
    $newImage = $newImage->coalesceImages();

    if ($this->getExtension() == 'gif') {
      do {
        $newImage->thumbnailImage($newDimension[0], $newDimension[1], false);
      } while ($newImage->nextImage() || !$newImage = $newImage->deconstructImages());
    } else {
      $newImage->thumbnailImage($newDimension[0], $newDimension[1], false);
    }

    return $newImage;
  }
  private function _machiningImageCrop(int $cropX, int $cropY, int $width, int $height, $color = 'transparent'): \Imagick {
    $extension = $this->getExtension();

    $newImage = new \Imagick();
    $newImage->setFormat($extension);

    $imagick = clone $this->_getImage();
    if ($extension == 'gif') {
      $imagick = $imagick->coalesceImages();

      do {
        $temp = new \Imagick();
        $temp->newImage($width, $height, new \ImagickPixel($color));
        $imagick->chopImage($cropX, $cropY, 0, 0);
        $temp->compositeImage($imagick, \Imagick::COMPOSITE_DEFAULT, 0, 0);

        $newImage->addImage($temp);
        $newImage->setImageDelay($imagick->getImageDelay());
      } while ($imagick->nextImage());
    } else {
      $imagick->chopImage($cropX, $cropY, 0, 0);
      $newImage->newImage($width, $height, new \ImagickPixel($color));
      $newImage->compositeImage($imagick, \Imagick::COMPOSITE_DEFAULT, 0, 0);
    }
    return $newImage;
  }
  private function _machiningImageFilter(float $radius, float $sigma, int $channel): \Imagick {
    $extension = $this->getExtension();

    $newImage = clone $this->_getImage();
    if ($extension == 'gif') {
      $newImage = $newImage->coalesceImages();

      do {
        $newImage->adaptiveBlurImage($radius, $sigma, $channel);
      } while ($newImage->nextImage() || !$newImage = $newImage->deconstructImages());
    } else {
      $newImage->adaptiveBlurImage($radius, $sigma, $channel);
    }
    return $newImage;
  }
  private static function _createFont(string $font, int $size, string $color, float $alpha): \ImagickDraw {
    $draw = new \ImagickDraw();
    $draw->setFont($font);
    $draw->setFontSize($size);
    $draw->setFillColor($color);
    $draw->setFillAlpha($alpha);
    return $draw;
  }
}
