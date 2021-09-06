<?php

namespace Thumbnail {

  abstract class Core {
    private static $extensions = ['jpg' => ['image/jpeg', 'image/pjpeg'], 'gif' => ['image/gif'], 'png' => ['image/png', 'image/x-png'], 'ico' => ['image/x-icon'], 'jpeg' => ['image/jpeg', 'image/pjpeg'], 'jpe' => ['image/jpeg', 'image/pjpeg'], 'bmp' => ['image/bmp', 'image/x-windows-bmp'], 'svg' => ['image/svg+xml']];

    protected static function error($message) {
      throw new \Exception($message);
    }

    private static function getExtensionByMime($mime) {
      static $extensions;

      if (isset($extensions[$mime]))
        return $extensions[$mime];

      foreach (self::$extensions as $ext => $mimes)
        if (in_array($mime, $mimes))
          return $extensions[$mime] = $ext;

      return $extensions[$mime] = null;
    }

    public static function colorHex2Rgb($hex) {
      return ($hex = str_replace('#', '', $hex)) && ((strlen($hex) == 3) || (strlen($hex) == 6))
        ? strlen($hex) == 3
          ? [hexdec(substr($hex, 0, 1) . substr($hex, 0, 1)), hexdec(substr($hex, 1, 1) . substr($hex, 1, 1)), hexdec(substr($hex, 2, 1) . substr($hex, 2, 1))]
          : [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))]
        : [];
    }

    public static function sort2DArr($key, $list) {
      if (!$list) return $list;

      $tmp = [];
      foreach ($list as &$ma)
        $tmp[] = &$ma[$key];

      array_multisort($tmp, SORT_DESC, $list);

      return $list;
    }

    public static function create($filePath, $options = []) {
      return new static($filePath, $options);
    }

    private $class = null;
    protected $filePath = null;

    protected $mime = null;
    protected $format = null;
    protected $image = null;
    protected $dimension = null;
    protected $logger = null;

    public function __construct($filePath) {
      is_file($filePath)
        && is_readable($filePath)
        || self::error('檔案不可讀取，或者不存在！檔案路徑：' . $filePath);

      $this->class = static::class;
      $this->filePath = $filePath;

      function_exists('mime_content_type')
        || self::error('mime_content_type 函式不存在！');

      $this->mime = strtolower(mime_content_type($this->filePath));
      $this->mime || self::error('取不到檔案的 mime 格式！檔案路徑：' . $this->filePath);

      $this->format = self::getExtensionByMime($this->mime);
      $this->format !== null || self::error('取不到符合的格式！Mime：' . $this->mime);

      if ($allows = static::allows())
        in_array($this->format, $allows) || self::error('不支援此檔案格式！格式：' . $this->format . '，目前只允許：' . json_encode(static::allows()));

      $this->image = $this->class == 'Thumbnail\Imagick'
        ? new Imagick($this->filePath)
        : $this->getOldImage($this->format);

      $this->image || self::error('產生 image 物件失敗！');

      $this->dimension = $this->getDimension($this->image);
    }

    abstract protected function allows();

    public function logger($func) {
      $this->logger = $func;
      return $this;
    }
    public function log(...$args) {
      $logger = $this->logger; $logger && $logger(...$args);
      return $this;
    }
    public function getImage() {
      return $this->image;
    }
    public function getFormat() {
      return $this->format;
    }
    protected function calcImageSizePercent($percent, $dimension) {
      return [ceil($dimension[0] * $percent / 100), ceil($dimension[1] * $percent / 100)];
    }
    protected function calcWidth($oldDimension, $newDimension) {
      $newWidthPercentage = 100 * $newDimension[0] / $oldDimension[0];
      $height = ceil($oldDimension[1] * $newWidthPercentage / 100);
      return [$newDimension[0], $height];
    }
    protected function calcHeight($oldDimension, $newDimension) {
      $newHeightPercentage  = 100 * $newDimension[1] / $oldDimension[1];
      $width = ceil($oldDimension[0] * $newHeightPercentage / 100);
      return [$width, $newDimension[1]];
    }
    protected function calcImageSize($oldDimension, $newDimension) {
      $newSize = [$oldDimension[0], $oldDimension[1]];
      if ($newDimension[0] > 0) {
        $newSize = $this->calcWidth($oldDimension, $newDimension);
        $newDimension[1] > 0 && $newSize[1] > $newDimension[1] && $newSize = $this->calcHeight($oldDimension, $newDimension);
      }
      if ($newDimension[1] > 0) {
        $newSize = $this->calcHeight($oldDimension, $newDimension);
        $newDimension[0] > 0 && $newSize[0] > $newDimension[0] && $newSize = $this->calcWidth($oldDimension, $newDimension);
      }
      return $newSize;
    }
    protected function calcImageSizeStrict($oldDimension, $newDimension) {
      $newSize = [$newDimension[0], $newDimension[1]];

      if ($newDimension[0] >= $newDimension[1]) {
        if ($oldDimension[0] > $oldDimension[1])  {
          $newSize = $this->calcHeight($oldDimension, $newDimension);
          $newSize[0] < $newDimension[0] && $newSize = $this->calcWidth($oldDimension, $newDimension);
        } else if ($oldDimension[1] >= $oldDimension[0]) {
          $newSize = $this->calcWidth($oldDimension, $newDimension);
          $newSize[1] < $newDimension[1] && $newSize = $this->calcHeight($oldDimension, $newDimension);
        }
        return $newSize;
      }

      if ($oldDimension[0] >= $oldDimension[1]) {
        $newSize = $this->calcWidth($oldDimension, $newDimension);
        $newSize[1] < $newDimension[1] && $newSize = $this->calcHeight($oldDimension, $newDimension);
      } else if ($oldDimension[1] > $oldDimension[0]) {
        $newSize = $this->calcHeight($oldDimension, $newDimension);
        $newSize[0] < $newDimension[0] && $newSize = $this->calcWidth($oldDimension, $newDimension);
      }
      return $newSize;
    }
  }

  final class Gd extends Core {
    private $options = [
      'resizeUp' => true,
      'interlace' => null,
      'jpegQuality' => 90,
      'preserveAlpha' => true,
      'preserveTransparency' => true,
      'alphaMaskColor' => [255, 255, 255],
      'transparencyMaskColor' => [0, 0, 0]
    ];

    public function __construct($fileName, $options = []) {
      parent::__construct($fileName);
      $this->options = array_merge($this->options, array_intersect_key($options, $this->options));
    }

    protected function allows() {
      return ['gif', 'jpg', 'png'];
    }

    protected function getOldImage($format) {
      switch ($format) {
        case 'gif': return imagecreatefromgif($this->filePath);
        case 'jpg': return imagecreatefromjpeg($this->filePath);
        case 'png': return imagecreatefrompng($this->filePath);
        default: static::error('找尋不到符合的格式，或者不支援此檔案格式！格式：' . $format);
      }
    }

    public function getDimension($image = null) {
      $image = $image ?? $this->getOldImage($this->format);
      return [imagesx($image), imagesy($image)];
    }

    private function _preserveAlpha($image) {
      if ($this->format == 'png' && $this->options['preserveAlpha'] === true) {
        imagealphablending($image, false);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, $this->options['alphaMaskColor'][0], $this->options['alphaMaskColor'][1], $this->options['alphaMaskColor'][2], 0));
        imagesavealpha($image, true);
      }

      if ($this->format == 'gif' && $this->options['preserveTransparency'] === true) {
        imagecolortransparent($image, imagecolorallocate($image, $this->options['transparencyMaskColor'][0], $this->options['transparencyMaskColor'][1], $this->options['transparencyMaskColor'][2]));
        imagetruecolortopalette($image, true, 256);
      }

      return $image;
    }

    private function _copyReSampled($newImage, $oldImage, $newX, $newY, $oldX, $oldY, $newWidth, $newHeight, $oldWidth, $oldHeight) {
      imagecopyresampled($newImage, $oldImage, $newX, $newY, $oldX, $oldY, $newWidth, $newHeight, $oldWidth, $oldHeight);
      return $this->_updateImage($newImage);
    }

    private function _updateImage($image) {
      $this->image = $image;
      $this->dimension = $this->getDimension($this->image);
      return $this;
    }

    private function createNewDimension($width, $height) {
      return [!$this->options['resizeUp'] && ($width > $this->dimension[0])
        ? $this->dimension[0]
        : $width, !$this->options['resizeUp'] && ($height > $this->dimension[1])
        ? $this->dimension[1]
        : $height];
    }

    public function save($filename) {
      imageinterlace($this->image, $this->options['interlace'] ? 1 : 0);
      switch ($this->format) {
        case 'jpg': return @imagejpeg($this->image, $filename, $this->options['jpegQuality']);
        case 'gif': return @imagegif($this->image, $filename);
        case 'png': return @imagepng($this->image, $filename);
        default: return false;
      }
    }

    static function verifyColor(&$color) {
      is_array($color) || $color = static::colorHex2Rgb($color);
      return is_array($color) && (count(array_filter($color, function ($color) { return $color >= 0 && $color <= 255; })) == 3);
    }

    public function pad($width, $height, $color = [255, 255, 255]) {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);

      if ($width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;

      if (!self::verifyColor($color))
        return $this->log('色碼格式錯誤，目前只支援字串 HEX、RGB 陣列格式！', '色碼：' . (is_string($color) ? $color : json_encode($color)));
        
      if ($width < $this->dimension[0] || $height < $this->dimension[1])
        $this->resize($width, $height);

      $newImage = function_exists('imagecreatetruecolor')
        ? imagecreatetruecolor($width, $height)
        : imagecreate($width, $height);

      imagefill($newImage, 0, 0, imagecolorallocate($newImage, $color[0], $color[1], $color[2]));

      return $this->_copyReSampled($newImage, $this->image, intval(($width - $this->dimension[0]) / 2), intval(($height - $this->dimension[1]) / 2), 0, 0, $this->dimension[0], $this->dimension[1], $this->dimension[0], $this->dimension[1]);
    }

    public function resizeByWidth($width) {
      return $this->resize($width, $width, 'w');
    }

    public function resizeByHeight($height) {
      return $this->resize($height, $height, 'h');
    }

    public function resize($width, $height, $method = 'auto') {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);
        
      if ($width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;

      $newDimension = $this->createNewDimension($width, $height);
      $method = strtolower(trim($method));

      switch ($method) {
        case 'a': case 'auto': default:
          $newDimension = $this->calcImageSize($this->dimension, $newDimension);
          break;

        case 'w': case 'width':
          $newDimension = $this->calcWidth($this->dimension, $newDimension);
          break;

        case 'h': case 'height':
          $newDimension = $this->calcHeight($this->dimension, $newDimension);
          break;
      }

      $newImage = function_exists('imagecreatetruecolor') ? imagecreatetruecolor($newDimension[0], $newDimension[1]) : imagecreate($newDimension[0], $newDimension[1]);
      $newImage = $this->_preserveAlpha($newImage);

      return $this->_copyReSampled($newImage, $this->image, 0, 0, 0, 0, $newDimension[0], $newDimension[1], $this->dimension[0], $this->dimension[1]);
    }

    public function adaptiveResizePercent($width, $height, $percent) {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);

      if ($percent < 0 || $percent > 100)console.error();
      
        return $this->log('百分比例錯誤！', '百分比要在 0 ~ 100 之間！', '百分比：' . $percent);
      

      if ($width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;

      $newDimension = $this->createNewDimension($width, $height);
      $newDimension = $this->calcImageSizeStrict($this->dimension, $newDimension);
      $this->resize($newDimension[0], $newDimension[1]);
      $newDimension = $this->createNewDimension($width, $height);

      $newImage = function_exists('imagecreatetruecolor') ? imagecreatetruecolor($newDimension[0], $newDimension[1]) : imagecreate($newDimension[0], $newDimension[1]);
      $newImage = $this->_preserveAlpha($newImage);

      $cropX = $cropY = 0;

      if ($this->dimension[0] > $newDimension[0])
        $cropX = intval(($percent / 100) * ($this->dimension[0] - $newDimension[0]));
      else if ($this->dimension[1] > $newDimension[1])
        $cropY = intval(($percent / 100) * ($this->dimension[1] - $newDimension[1]));

      return $this->_copyReSampled($newImage, $this->image, 0, 0, $cropX, $cropY, $newDimension[0], $newDimension[1], $newDimension[0], $newDimension[1]);
    }

    public function adaptiveResize($width, $height) {
      return $this->adaptiveResizePercent($width, $height, 50);
    }

    public function resizePercent($percent = 0) {
      if ($percent < 1)
        return $this->log('縮圖比例錯誤！', '百分比要大於 1', '百分比：' . $percent);

      if ($percent == 100)
        return $this;

      $newDimension = $this->calcImageSizePercent($percent, $this->dimension);
      return $this->resize($newDimension[0], $newDimension[1]);
    }

    public function crop($startX, $startY, $width, $height) {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);

      if ($startX < 0 || $startY < 0)
        return $this->log('起始點錯誤！', '水平、垂直的起始點一定要大於 0', '水平點：' . $startX, '垂直點：' . $startY);

      if ($startX == 0 && $startY == 0 && $width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;

      $width  = $this->dimension[0] < $width ? $this->dimension[0] : $width;
      $height = $this->dimension[1] < $height ? $this->dimension[1] : $height;
      $startX = ($startX + $width) > $this->dimension[0] ? $this->dimension[0] - $width : $startX;
      $startY = ($startY + $height) > $this->dimension[1] ? $this->dimension[1] - $height : $startY;
      
      $newImage = function_exists('imagecreatetruecolor') ? imagecreatetruecolor($width, $height) : imagecreate($width, $height);
      $newImage = $this->_preserveAlpha($newImage);

      return $this->_copyReSampled($newImage, $this->image, 0, 0, $startX, $startY, $width, $height, $width, $height);
    }

    public function cropCenter($width, $height) {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);

      if ($width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;

      if ($width > $this->dimension[0] && $height > $this->dimension[1])
        return $this->pad($width, $height);

      $startX = intval(($this->dimension[0] - $width) / 2);
      $startY = intval(($this->dimension[1] - $height) / 2);

      $width  = $this->dimension[0] < $width ? $this->dimension[0] : $width;
      $height = $this->dimension[1] < $height ? $this->dimension[1] : $height;

      return $this->crop($startX, $startY, $width, $height);
    }

    public function rotate($degree, $color = [255, 255, 255]) {
      if (!function_exists('imagerotate'))
        return $this->log('沒有載入 imagerotate 函式！');

      if (!self::verifyColor($color))
        return $this->log('色碼格式錯誤，目前只支援字串 HEX、RGB 陣列格式！', '色碼：' . (is_string($color) ? $color : json_encode($color)));

      if (!($degree % 360))
        return $this;

      $temp = function_exists('imagecreatetruecolor') ? imagecreatetruecolor(1, 1) : imagecreate(1, 1);
      $newImage = imagerotate($this->image, 0 - $degree, imagecolorallocate($temp, $color[0], $color[1], $color[2]));

      return $this->_updateImage($newImage);
    }

    public function adaptiveResizeQuadrant($width, $height, $item = 'c') {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);

      if ($width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;

      $newDimension = $this->createNewDimension($width, $height);
      $newDimension = $this->calcImageSizeStrict($this->dimension, $newDimension);
      $this->resize($newDimension[0], $newDimension[1]);

      $newDimension = $this->createNewDimension($width, $height);
      $newImage = function_exists('imagecreatetruecolor') ? imagecreatetruecolor($newDimension[0], $newDimension[1]) : imagecreate($newDimension[0], $newDimension[1]);
      $newImage = $this->_preserveAlpha($newImage);

      $cropX = 0;
      $cropY = 0;
      $item = strtolower(trim($item));

      if ($this->dimension[0] > $newDimension[0]) {
        switch ($item) {
          case 'l': case 'left':
            $cropX = 0;
            break;

          case 'r': case 'right':
            $cropX = intval($this->dimension[0] - $newDimension[0]);
            break;

          case 'c': case 'center': default:
            $cropX = intval(($this->dimension[0] - $newDimension[0]) / 2);
            break;
        }
      } else if ($this->dimension[1] > $newDimension[1]) {
        switch ($item) {
          case 't': case 'top': 
            $cropY = 0;
            break;

          case 'b': case 'bottom':
            $cropY = intval($this->dimension[1] - $newDimension[1]);
            break;

          case 'c': case 'center': default:
            $cropY = intval(($this->dimension[1] - $newDimension[1]) / 2);
            break;
        }
      }

      return $this->_copyReSampled($newImage, $this->image, 0, 0, $cropX, $cropY, $newDimension[0], $newDimension[1], $newDimension[0], $newDimension[1]);
    }

    public static function block9($files, $file, $interlace = null, $jpegQuality = 100) {
      count($files) >= 9 || static::error('參數錯誤！檔案數量要大於等於 9，數量：' . count($files));
      $file              || static::error('錯誤的儲存路徑！儲存路徑：' . $file);

      $positions = [
        ['left' =>   2, 'top' =>   2, 'width' => 130, 'height' => 130], ['left' => 134, 'top' =>   2, 'width' =>  64, 'height' =>  64], ['left' => 200, 'top' =>   2, 'width' =>  64, 'height' =>  64],
        ['left' => 134, 'top' =>  68, 'width' =>  64, 'height' =>  64], ['left' => 200, 'top' =>  68, 'width' =>  64, 'height' =>  64], ['left' =>   2, 'top' => 134, 'width' =>  64, 'height' =>  64],
        ['left' =>  68, 'top' => 134, 'width' =>  64, 'height' =>  64], ['left' => 134, 'top' => 134, 'width' =>  64, 'height' =>  64], ['left' => 200, 'top' => 134, 'width' =>  64, 'height' =>  64],
      ];

      $image = imagecreatetruecolor(266, 200);
      imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));

      for ($i = 0, $c = count($positions); $i < $c; $i++)
        imagecopymerge($image,
          static::create($files[$i])->adaptiveResizeQuadrant($positions[$i]['width'],
          $positions[$i]['height'])->getImage(),
          $positions[$i]['left'],
          $positions[$i]['top'],
          0,
          0,
          $positions[$i]['width'],
          $positions[$i]['height'],
          100);

      isset($interlace) && imageinterlace($image, $interlace ? 1 : 0);

      switch (pathinfo($file, PATHINFO_EXTENSION)) {
        case 'jpg':          return @imagejpeg($image, $file, $jpegQuality);
        case 'gif':          return @imagegif($image, $file);
        case 'png': default: return @imagepng($image, $file);
      }
      return false;
    }

    public static function photos($files, $file, $interlace = null, $jpegQuality = 100) {
      $files || static::error('參數錯誤！檔案數量要大於等於 1，數量：' . count($files));
      $file  || static::error('錯誤的儲存路徑！儲存路徑：' . $file);

      $w = 1200;
      $h = 630;

      $image = imagecreatetruecolor($w, $h);
      imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));

      $spacing = 5;
      $positions = [];

      switch (count($files)) {
        case 1:          $positions = [['left' => 0, 'top' => 0, 'width' => $w, 'height' => $h]]; break;
        case 2:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h], ['left' => $w / 2 + $spacing, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h]]; break;
        case 3:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h], ['left' => $w / 2 + $spacing, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 2 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing]]; break;
        case 4:          $positions = [['left' => 0, 'top' => 0, 'width' => $w, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]]; break;
        case 5:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 2 + $spacing, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]]; break;
        case 6:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]]; break;
        case 7:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]]; break;
        case 8:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing]]; break;
        default: case 9: $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing]]; break;
      }

      for ($i = 0, $c = count($positions); $i < $c; $i++)
        imagecopymerge($image,
          static::create($files[$i])->adaptiveResizeQuadrant($positions[$i]['width'],
          $positions[$i]['height'])->getImage(),
          $positions[$i]['left'],
          $positions[$i]['top'],
          0,
          0,
          $positions[$i]['width'],
          $positions[$i]['height'],
          100);

      isset($interlace) && imageinterlace($image, $interlace ? 1 : 0);

      switch (pathinfo($file, PATHINFO_EXTENSION)) {
        case 'jpg':          return @imagejpeg($image, $file, $jpegQuality);
        case 'gif':          return @imagegif($image, $file);
        default: case 'png': return @imagepng($image, $file);
      }
      return false;
    }
  }

  final class Imagick extends Core {
    private $options = [
      'resizeUp' => true,
    ];

    public function __construct($fileName, $options = []) {
      parent::__construct($fileName);

      $this->options = array_merge($this->options, array_intersect_key($options, $this->options));
    }

    protected function allows() {
      return ['gif', 'jpg', 'png'];
    }

    public function getDimension($image = null) {
      $image || $image = clone $this->image;
      $imagePage = $image->getImagePage();

      if (!($imagePage && isset($imagePage['width'], $imagePage['height']) && $imagePage['width'] > 0 && $imagePage['height'] > 0)) {
        $imagePage = $image->getImageGeometry();

        if (!(($imagePage && isset($imagePage['width'], $imagePage['height']) && $imagePage['width'] > 0 && $imagePage['height'] > 0)))
          static::error('無法圖片取得尺寸！');
      }

      return [$imagePage['width'], $imagePage['height']];
    }

    private function _machiningImageResize($newDimension) {
      $newImage = clone $this->image;
      $newImage = $newImage->coalesceImages();

      if ($this->format == 'gif')
        do {
          $newImage->thumbnailImage($newDimension[0], $newDimension[1], false);
        } while ($newImage->nextImage() || !$newImage = $newImage->deconstructImages());
      else
        $newImage->thumbnailImage($newDimension[0], $newDimension[1], false);

      return $newImage;
    }

    private function _machiningImageCrop($cropX, $cropY, $width, $height, $color = 'transparent') {
      $newImage = new Imagick();
      $newImage->setFormat($this->format);

      if ($this->format == 'gif') {
        $imagick = clone $this->image;
        $imagick = $imagick->coalesceImages();
        
        do {
          $temp = new Imagick();
          $temp->newImage($width, $height, new ImagickPixel($color));
          $imagick->chopImage($cropX, $cropY, 0, 0);
          $temp->compositeImage($imagick, Imagick::COMPOSITE_DEFAULT, 0, 0);

          $newImage->addImage($temp);
          $newImage->setImageDelay($imagick->getImageDelay ());
        } while ($imagick->nextImage());
      } else {
        $imagick = clone $this->image;
        $imagick->chopImage($cropX, $cropY, 0, 0);
        $newImage->newImage($width, $height, new ImagickPixel($color));
        $newImage->compositeImage($imagick, Imagick::COMPOSITE_DEFAULT, 0, 0);
      }
      return $newImage;
    }

    private function _machiningImageRotate($degree, $color = 'transparent') {
      $newImage = new Imagick();
      $newImage->setFormat($this->format);
      $imagick = clone $this->image;

      if ($this->format == 'gif') {
        $imagick->coalesceImages();
        
        do {
          $temp = new Imagick();
          $imagick->rotateImage(new ImagickPixel($color), $degree);
          $newDimension = $this->getDimension($imagick);
          $temp->newImage($newDimension[0], $newDimension[1], new ImagickPixel($color));
          $temp->compositeImage($imagick, Imagick::COMPOSITE_DEFAULT, 0, 0);
          $newImage->addImage($temp);
          $newImage->setImageDelay($imagick->getImageDelay());
        } while ($imagick->nextImage());
      } else {
        $imagick->rotateImage(new ImagickPixel($color), $degree);
        $newDimension = $this->getDimension($imagick);
        $newImage->newImage($newDimension[0], $newDimension[1], new ImagickPixel($color));
        $newImage->compositeImage($imagick, Imagick::COMPOSITE_DEFAULT, 0, 0);
      }
      return $newImage;
    }

    private function _updateImage($image) {
      $this->image = $image;
      $this->dimension = $this->getDimension($image);
      return $this;
    }

    private function _machiningImageFilter($radius, $sigma, $channel) {
      if ($this->format == 'gif') {
        $newImage = clone $this->image;
        $newImage = $newImage->coalesceImages();

        do {
          $newImage->adaptiveBlurImage($radius, $sigma, $channel);
        } while ($newImage->nextImage() || !$newImage = $newImage->deconstructImages());
      } else {
        $newImage = clone $this->image;
        $newImage->adaptiveBlurImage($radius, $sigma, $channel);
      }
      return $newImage;
    }

    private function _createFont($font, $fontSize, $color, $alpha) {
      $draw = new ImagickDraw();
      $draw->setFont($font);
      $draw->setFontSize($fontSize);
      $draw->setFillColor($color);
      // $draw->setFillAlpha ($alpha);
      return $draw;
    }

    public function save($filename, $adjoin = true) {
      return $this->image->writeImages($filename, $adjoin);
    }

    public function pad($width, $height, $color = 'transparent') {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);

      if ($width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;

      if ($width < $this->dimension[0] || $height < $this->dimension[1])
        $this->resize($width, $height);

      $newImage = new Imagick();
      $newImage->setFormat($this->format);

      if ($this->format == 'gif') {
        $imagick = clone $this->image;
        $imagick = $imagick->coalesceImages();

        do {
          $temp = new Imagick();
          $temp->newImage($width, $height, new ImagickPixel($color));
          $temp->compositeImage($imagick, Imagick::COMPOSITE_DEFAULT, intval(($width - $this->dimension[0]) / 2), intval(($height - $this->dimension[1]) / 2));
          $newImage->addImage($temp);
          $newImage->setImageDelay($imagick->getImageDelay());
        } while ($imagick->nextImage());
      } else {
        $newImage->newImage($width, $height, new ImagickPixel($color));
        $newImage->compositeImage(clone $this->image, Imagick::COMPOSITE_DEFAULT, intval(($width - $this->dimension[0]) / 2), intval(($height - $this->dimension[1]) / 2));
      }

      return $this->_updateImage($newImage);
    }

    private function createNewDimension ($width, $height) {
      return [!$this->options['resizeUp'] && ($width > $this->dimension[0])
        ? $this->dimension[0]
        : $width, !$this->options['resizeUp'] && ($height > $this->dimension[1])
        ? $this->dimension[1]
        : $height];
    }

    public function resizeByWidth($width) {
      return $this->resize($width, $width, 'w');
    }

    public function resizeByHeight($height) {
      return $this->resize($height, $height, 'h');
    }

    public function resize($width, $height, $method = 'auto') {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);

      if ($width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;

      $newDimension = $this->createNewDimension($width, $height);
      $method = strtolower(trim($method));

      switch ($method) {
        case 'a': case 'auto': default:
          $newDimension = $this->calcImageSize($this->dimension, $newDimension);
          break;

        case 'w': case 'width':
          $newDimension = $this->calcWidth($this->dimension, $newDimension);
          break;

        case 'h': case 'height':
          $newDimension = $this->calcHeight($this->dimension, $newDimension);
          break;
      }

      $workingImage = $this->_machiningImageResize($newDimension);

      return $this->_updateImage($workingImage);
    }

    public function adaptiveResizePercent($width, $height, $percent) {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);

      if ($percent < 0 || $percent > 100)
        return $this->log('百分比例錯誤！', '百分比要在 0 ~ 100 之間！', '百分比：' . $percent);

      if ($width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;
      
      $newDimension = $this->createNewDimension($width, $height);
      $newDimension = $this->calcImageSizeStrict($this->dimension, $newDimension);
      $this->resize($newDimension[0], $newDimension[1]);
      $newDimension = $this->createNewDimension($width, $height);

      $cropX = $cropY = 0;

      if ($this->dimension[0] > $newDimension[0])
        $cropX = intval(($percent / 100) * ($this->dimension[0] - $newDimension[0]));
      else if ($this->dimension[1] > $newDimension[1])
        $cropY = intval(($percent / 100) * ($this->dimension[1] - $newDimension[1]));

      $workingImage = $this->_machiningImageCrop($cropX, $cropY, $newDimension[0], $newDimension[1]);
      return $this->_updateImage($workingImage);
    }

    public function adaptiveResize($width, $height) {
      return $this->adaptiveResizePercent($width, $height, 50);
    }

    public function resizePercent($percent = 0) {
      if ($percent < 1)
        return $this->log('縮圖比例錯誤！', '百分比要大於 1', '百分比：' . $percent);

      if ($percent == 100)
        return $this;

      $newDimension = $this->calcImageSizePercent($percent, $this->dimension);
      return $this->resize($newDimension[0], $newDimension[1]);
    }

    public function crop($startX, $startY, $width, $height) {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);

      if ($startX < 0 || $startY < 0)
        return $this->log('起始點錯誤！', '水平、垂直的起始點一定要大於 0', '水平點：' . $startX, '垂直點：' . $startY);

      if ($startX == 0 && $startY == 0 && $width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;

      $width  = $this->dimension[0] < $width ? $this->dimension[0] : $width;
      $height = $this->dimension[1] < $height ? $this->dimension[1] : $height;

      $startX + $width > $this->dimension[0] && $startX = $this->dimension[0] - $width;
      $startY + $height > $this->dimension[1] && $startY = $this->dimension[1] - $height;

      $workingImage = $this->_machiningImageCrop($startX, $startY, $width, $height);
      return $this->_updateImage($workingImage);
    }

    public function cropCenter($width, $height) {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);

      if ($width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;

      if ($width > $this->dimension[0] && $height > $this->dimension[1])
        return $this->pad($width, $height);

      $startX = intval(($this->dimension[0] - $width) / 2);
      $startY = intval(($this->dimension[1] - $height) / 2);
      $width  = $this->dimension[0] < $width ? $this->dimension[0] : $width;
      $height = $this->dimension[1] < $height ? $this->dimension[1] : $height;

      return $this->crop($startX, $startY, $width, $height);
    }

    public function rotate($degree, $color = 'transparent') {
      if (!($degree % 360))
        return $this;

      $workingImage = $this->_machiningImageRotate($degree, $color);

      return $this->_updateImage($workingImage);
    }

    public function adaptiveResizeQuadrant($width, $height, $item = 'c') {
      if ($width <= 0 || $height <= 0)
        return $this->log('新尺寸錯誤！', '尺寸寬高一定要大於 0', '寬：' . $width, '高：' . $height);

      if ($width == $this->dimension[0] && $height == $this->dimension[1])
        return $this;

      $newDimension = $this->createNewDimension($width, $height);
      $newDimension = $this->calcImageSizeStrict($this->dimension, $newDimension);
      $this->resize($newDimension[0], $newDimension[1]);
      $newDimension = $this->createNewDimension($width, $height);
      
      $cropX = $cropY = 0;
      $item = strtolower(trim($item));

      if ($this->dimension[0] > $newDimension[0]) {
        switch ($item) {
          case 'l': case 'left':
            $cropX = 0;
            break;

          case 'r': case 'right':
            $cropX = intval($this->dimension[0] - $newDimension[0]);
            break;

          case 'c': case 'center': default:
            $cropX = intval(($this->dimension[0] - $newDimension[0]) / 2);
            break;
        }
      } else if ($this->dimension[1] > $newDimension[1]) {
        switch ($item) {
          case 't': case 'top': 
            $cropY = 0;
            break;

          case 'b': case 'bottom':
            $cropY = intval($this->dimension[1] - $newDimension[1]);
            break;

          case 'c': case 'center': default:
            $cropY = intval(($this->dimension[1] - $newDimension[1]) / 2);
            break;
        }
      }

      $workingImage = $this->_machiningImageCrop($cropX, $cropY, $newDimension[0], $newDimension[1]);

      return $this->_updateImage($workingImage);
    }

    public static function block9($files, $file = null, $adjoin = true) {
      count($files) >= 9 || static::error('參數錯誤！檔案數量要大於等於 9，數量：' . count($files));
      $file              || static::error('錯誤的儲存路徑！儲存路徑：' . $file);

      $newImage = new Imagick();
      $newImage->newImage(266, 200, new ImagickPixel('white'));
      $newImage->setFormat(pathinfo($file, PATHINFO_EXTENSION));

      $positions = [
        ['left' =>   2, 'top' =>   2, 'width' => 130, 'height' => 130], ['left' => 134, 'top' =>   2, 'width' =>  64, 'height' =>  64], ['left' => 200, 'top' =>   2, 'width' =>  64, 'height' =>  64],
        ['left' => 134, 'top' =>  68, 'width' =>  64, 'height' =>  64], ['left' => 200, 'top' =>  68, 'width' =>  64, 'height' =>  64], ['left' =>   2, 'top' => 134, 'width' =>  64, 'height' =>  64],
        ['left' =>  68, 'top' => 134, 'width' =>  64, 'height' =>  64], ['left' => 134, 'top' => 134, 'width' =>  64, 'height' =>  64], ['left' => 200, 'top' => 134, 'width' =>  64, 'height' =>  64],
      ];

      for ($i = 0, $c = count($positions); $i < $c; $i++)
        $newImage->compositeImage(static::create($files[$i])->adaptiveResizeQuadrant($positions[$i]['width'], $positions[$i]['height'])->getImage(), Imagick::COMPOSITE_DEFAULT, $positions[$i]['left'], $positions[$i]['top']);

      return $newImage->writeImages($file, $adjoin);
    }

    public static function photos($files, $file = null, $adjoin = true) {
      $files || static::error('參數錯誤！檔案數量要大於等於 1，數量：' . count($files));
      $file  || static::error('錯誤的儲存路徑！儲存路徑：' . $file);
      
      $w = 1200;
      $h = 630;

      $newImage = new Imagick();
      $newImage->newImage($w, $h, new ImagickPixel('white'));
      $newImage->setFormat(pathinfo ($file, PATHINFO_EXTENSION));
      
      $spacing = 5;
      $positions = [];
      switch (count($files)) {
        case 1:          $positions = [['left' => 0, 'top' => 0, 'width' => $w, 'height' => $h]]; break;
        case 2:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h], ['left' => $w / 2 + $spacing, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h]]; break;
        case 3:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h], ['left' => $w / 2 + $spacing, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 2 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing]]; break;
        case 4:          $positions = [['left' => 0, 'top' => 0, 'width' => $w, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]]; break;
        case 5:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 2 + $spacing, 'top' => 0, 'width' => $w / 2 - $spacing, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]]; break;
        case 6:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]]; break;
        case 7:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => 0, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing]]; break;
        case 8:          $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 2 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 2 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing]]; break;
        default: case 9: $positions = [['left' => 0, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => 0, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => $w / 3 + $spacing, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => 0, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => $h / 3 + $spacing, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing], ['left' => ($w / 3 + $spacing) * 2, 'top' => ($h / 3 + $spacing) * 2, 'width' => $w / 3 - $spacing, 'height' => $h / 3 - $spacing]]; break;
      }

      for ($i = 0, $c = count($positions); $i < $c; $i++)
        $newImage->compositeImage(static::create($files[$i])->adaptiveResizeQuadrant($positions[$i]['width'], $positions[$i]['height'])->getImage(), Imagick::COMPOSITE_DEFAULT, $positions[$i]['left'], $positions[$i]['top']);

      return $newImage->writeImages($file, $adjoin);
    }

    public function filter($radius, $sigma, $channel = Imagick::CHANNEL_DEFAULT) {
      $items = [Imagick::CHANNEL_UNDEFINED, Imagick::CHANNEL_RED,     Imagick::CHANNEL_GRAY,  Imagick::CHANNEL_CYAN,
                Imagick::CHANNEL_GREEN,     Imagick::CHANNEL_MAGENTA, Imagick::CHANNEL_BLUE,  Imagick::CHANNEL_YELLOW,
                Imagick::CHANNEL_ALPHA,     Imagick::CHANNEL_OPACITY, Imagick::CHANNEL_BLACK,
                Imagick::CHANNEL_INDEX,     Imagick::CHANNEL_ALL,     Imagick::CHANNEL_DEFAULT];

      if (!in_array($channel, $items))
        return $this->log('參數錯誤！', '參數 Channel 格式不正確！', 'Channel：' . $channel);

      $workingImage = $this->_machiningImageFilter($radius, $sigma, $channel);

      return $this->_updateImage($workingImage);
    }

    public function lomography() {
      $newImage = new Imagick();
      $newImage->setFormat($this->format);

      if ($this->format == 'gif') {
        $imagick = clone $this->image;
        $imagick = $imagick->coalesceImages();
        
        do {
          $temp = new Imagick();
          $imagick->setimagebackgroundcolor('black');
          $imagick->gammaImage(0.75);
          $imagick->vignetteImage(0, max($this->dimension[0], $this->dimension[1]) * 0.2, 0 - ($this->dimension[0] * 0.05), 0 - ($this->dimension[1] * 0.05));
          $temp->newImage($this->dimension[0], $this->dimension[1], new ImagickPixel('transparent'));
          $temp->compositeImage($imagick, Imagick::COMPOSITE_DEFAULT, 0, 0);

          $newImage->addImage($temp);
          $newImage->setImageDelay($imagick->getImageDelay());
        } while ($imagick->nextImage());
      } else {
        $newImage = clone $this->image;
        $newImage->setimagebackgroundcolor('black');
        $newImage->gammaImage(0.75);
        $newImage->vignetteImage(0, max($this->dimension[0], $this->dimension[1]) * 0.2, 0 - ($this->dimension[0] * 0.05), 0 - ($this->dimension[1] * 0.05));
      }
      return $this->_updateImage($newImage);
    }

    public function getAnalysisDatas($limit = 10) {
      if ($limit <= 0 && $this->log('參數錯誤！', '分析數量一定要大於 0', '分析數量：' . $limit))
        return [];

      $temp = clone $this->image;

      $temp->quantizeImage($limit, Imagick::COLORSPACE_RGB, 0, false, false );
      $pixels = $temp->getImageHistogram();

      $datas = [];
      $index = 0;
      $pixelCount = $this->dimension[0] * $this->dimension[1];

      if ($pixels && $limit)
        foreach ($pixels as $pixel)
          if ($index++ < $limit)
            array_push($datas, array ('color' => $pixel->getColor(), 'count' => $pixel->getColorCount(), 'percent' => round($pixel->getColorCount() / $pixelCount * 100)));
          else
            break;

      return static::sort2DArr('count', $datas);
    }

    public function saveAnalysisChart($file, $font, $limit = 10, $fontSize = 14, $adjoin = true) {
      if (!$file)
        return $this->log('錯誤的儲存路徑！', '儲存路徑：' . $file);

      if (!is_readable($font))
        return $this->log('參數錯誤！', '字型檔案不存在或不可讀！', '字型：' . $font);

      $limit > 0 || $this->log('參數錯誤！', '分析數量一定要大於 0', '分析數量：' . $limit);

      if ($fontSize <= 0)
        return $this->log('參數錯誤！', '字體大小一定要大於 0', '字體大小：' . $fontSize);

      $format = pathinfo($file, PATHINFO_EXTENSION);
      if (!$format || !in_array($format, self::allows()))
        return $this->log('不支援此檔案格式！', '格式：' . $format);

      if (!$datas = $this->getAnalysisDatas($limit))
        return $this->log('圖像分析錯誤！');

      $newImage = new Imagick();

      foreach ($datas as $data) {
        $newImage->newImage(400, 20, new ImagickPixel('white'));

        $draw = new ImagickDraw();
        $draw->setFont($font);
        $draw->setFontSize($fontSize);
        $newImage->annotateImage($draw, 25, 14, 0, 'Percentage of total pixels : ' . (strlen($data['percent']) < 2 ? ' ':'') . $data['percent'] . '% (' . $data['count'] . ')');

        $tile = new Imagick();
        $tile->newImage(20, 20, new ImagickPixel('rgb(' . $data['color']['r'] . ',' . $data['color']['g'] . ',' . $data['color']['b'] . ')'));

        $newImage->compositeImage($tile, Imagick::COMPOSITE_OVER, 0, 0);
      }

      $newImage = $newImage->montageImage(new imagickdraw(), '1x' . count($datas) . '+0+0', '400x20+4+2>', Imagick::MONTAGEMODE_UNFRAME, '0x0+3+3');
      $newImage->setImageFormat($format);
      $newImage->setFormat($format);
      $newImage->writeImages($file, $adjoin);

      return $this;
    }

    public function addFont($text, $font, $startX = 0, $startY = 12, $color = 'black', $fontSize = 12, $alpha = 1, $degree = 0) {
      if ($text === '')
        return $this->log('沒有文字！', '內容：' . $text);

      if (!is_readable($font))
        return $this->log('參數錯誤！', '字型檔案不存在或不可讀！', '字型：' . $font);

      if ($startX < 0 || $startY < 0)
        return $this->log('起始點錯誤！', '水平、垂直的起始點一定要大於 0', '水平點：' . $startX, '垂直點：' . $startY);

      if ($fontSize <= 0)
        return $this->log('參數錯誤！', '字體大小一定要大於 0', '字體大小：' . $fontSize);
      
      if ($alpha < 0 || $alpha > 1)
        return $this->log('參數錯誤！', '參數 Alpha 一定要是 0 ~ 1', 'Alpha：' . $alpha);

      $degree = $degree % 360;

      if (!$draw = $this->_createFont($font, $fontSize, $color, $alpha))
        return $this->log('產生文字物件失敗！');

      if ($this->format == 'gif') {
        $newImage = new Imagick();
        $newImage->setFormat($this->format);
        $imagick = clone $this->image;
        $imagick = $imagick->coalesceImages();
        
        do {
          $temp = new Imagick();
          $temp->newImage($this->dimension[0], $this->dimension[1], new ImagickPixel('transparent'));
          $temp->compositeImage($imagick, Imagick::COMPOSITE_DEFAULT, 0, 0);
          $temp->annotateImage($draw, $startX, $startY, $degree, $text);
          $newImage->addImage($temp);
          $newImage->setImageDelay($imagick->getImageDelay());
        } while ($imagick->nextImage());
      } else {
        $newImage = clone $this->image;
        $newImage->annotateImage($draw, $startX, $startY, $degree, $text);
      }

      return $this->_updateImage($newImage);
    }
  }
}
