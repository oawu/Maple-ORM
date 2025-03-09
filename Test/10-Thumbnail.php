<?php

use \Orm\Core\Thumbnail\Imagick;
use \Orm\Core\Thumbnail\Gd;

@exec('rm -rf ' . PATH_TMP . '*');
@exec('rm -rf ' . PATH_STORAGE . '*');

$src = PATH_STORAGE . 'avatar2.jpg';
$dest = PATH_STORAGE . 'imagick-';

if (!(file_exists($src) || @copy(PATH_SAMPLE . 'avatar.jpg', $src))) {
  throw new Exception();
}

echo "pad";
$img = new Imagick($src);
$img->pad(100, 100);
$img->save($dest . '01.jpg');
echo " - ok\n";

echo "resizeByWidth";
$img = new Imagick($src);
$img->resizeByWidth(100);
$img->save($dest . '02.jpg');
echo " - ok\n";

echo "resizeByHeight";
$img = new Imagick($src);
$img->resizeByHeight(100);
$img->save($dest . '03.jpg');
echo " - ok\n";

echo "resize";
$img = new Imagick($src);
$img->resize(100, 50);
$img->save($dest . '04.jpg');
echo " - ok\n";

echo "adaptiveResizePercent";
$img = new Imagick($src);
$img->adaptiveResizePercent(50, 100, 0.1);
$img->save($dest . '05.jpg');
echo " - ok\n";

echo "adaptiveResize";
$img = new Imagick($src);
$img->adaptiveResize(100, 100);
$img->save($dest . '06.jpg');
echo " - ok\n";

echo "scale";
$img = new Imagick($src);
$img->scale(0.5);
$img->save($dest . '07.jpg');
echo " - ok\n";

echo "crop";
$img = new Imagick($src);
$img->crop(500, 100, 200, 300);
$img->save($dest . '08.jpg');
echo " - ok\n";

echo "cropCenter";
$img = new Imagick($src);
$img->cropCenter(123, 456);
$img->save($dest . '09.jpg');
echo " - ok\n";

echo "rotate";
$img = new Imagick($src);
$img->rotate(45);
$img->save($dest . '10.jpg');
echo " - ok\n";

echo "adaptiveResizeQuadrant";
$img = new Imagick($src);
$img->adaptiveResizeQuadrant(200, 100);
$img->save($dest . '11.jpg');
echo " - ok\n";

echo "filter";
$img = new Imagick($src);
$img->filter(10, 3, \Imagick::CHANNEL_ALPHA);
$img->save($dest . '12.jpg');
echo " - ok\n";

echo "lomography";
$img = new Imagick($src);
$img->lomography();
$img->save($dest . '13.jpg');
echo " - ok\n";

echo "addFont";
$img = new Imagick($src);
$img->addFont('text', PATH_FONT);
$img->save($dest . '14.jpg');
echo " - ok\n";

echo "block9";
Imagick::block9([
  $src,
  $src,
  $src,
  $src,
  $src,
  $src,
  $src,
  $src,
  $src,
], $dest . '15.jpg');
echo " - ok\n";

echo "saveAnalysis";
Imagick::saveAnalysis($src, $dest . '16.jpg', PATH_FONT);
echo " - ok\n";

echo "photos";
for ($i = 0; $i < 10; $i++) {
  echo ' - ' . $i;
  $srcs = [];
  for ($j = 0; $j < $i + 1; $j++) {
    $srcs[] = $src;
  }
  Imagick::photos($srcs, $dest . (17 + $i) . '.jpg');
}

echo " - ok\n";

echo "\n";

$dest = PATH_STORAGE . 'gd-';

echo "pad";
$img = new Gd($src);
$img->pad(100, 100);
$img->save($dest . '01.jpg');
echo " - ok\n";


echo "resizeByWidth";
$img = new Gd($src);
$img->resizeByWidth(100);
$img->save($dest . '02.jpg');
echo " - ok\n";

echo "resizeByHeight";
$img = new Gd($src);
$img->resizeByHeight(100);
$img->save($dest . '03.jpg');
echo " - ok\n";


echo "resize";
$img = new Gd($src);
$img->resize(100, 50);
$img->save($dest . '04.jpg');
echo " - ok\n";


echo "adaptiveResizePercent";
$img = new Gd($src);
$img->adaptiveResizePercent(50, 100, 0.1);
$img->save($dest . '05.jpg');
echo " - ok\n";

echo "adaptiveResize";
$img = new Gd($src);
$img->adaptiveResize(100, 100);
$img->save($dest . '06.jpg');
echo " - ok\n";


echo "scale";
$img = new Gd($src);
$img->scale(0.5);
$img->save($dest . '07.jpg');
echo " - ok\n";

echo "crop";
$img = new Gd($src);
$img->crop(500, 100, 200, 300);
$img->save($dest . '08.jpg');
echo " - ok\n";

echo "cropCenter";
$img = new Gd($src);
$img->cropCenter(123, 456);
$img->save($dest . '09.jpg');
echo " - ok\n";


echo "rotate";
$img = new Gd($src);
$img->rotate(45);
$img->save($dest . '10.jpg');
echo " - ok\n";

echo "adaptiveResizeQuadrant";
$img = new Gd($src);
$img->adaptiveResizeQuadrant(200, 100);
$img->save($dest . '11.jpg');
echo " - ok\n";

echo "block9";
Gd::block9([
  $src,
  $src,
  $src,
  $src,
  $src,
  $src,
  $src,
  $src,
  $src,
], $dest . '12.jpg');
echo " - ok\n";

echo "photos";
for ($i = 0; $i < 10; $i++) {
  echo ' - ' . $i;
  $srcs = [];
  for ($j = 0; $j < $i + 1; $j++) {
    $srcs[] = $src;
  }
  Gd::photos($srcs, $dest . (12 + $i) . '.jpg');
}

echo " - ok\n";
