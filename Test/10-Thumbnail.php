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

title('Imagick pad');
$img = new Imagick($src);
$img->pad(100, 100);
$img->save($dest . '01.jpg');

title('Imagick resizeByWidth');
$img = new Imagick($src);
$img->resizeByWidth(100);
$img->save($dest . '02.jpg');

title('Imagick resizeByHeight');
$img = new Imagick($src);
$img->resizeByHeight(100);
$img->save($dest . '03.jpg');

title('Imagick resize');
$img = new Imagick($src);
$img->resize(100, 50);
$img->save($dest . '04.jpg');

title('Imagick adaptiveResizePercent');
$img = new Imagick($src);
$img->adaptiveResizePercent(50, 100, 0.1);
$img->save($dest . '05.jpg');

title('Imagick adaptiveResize');
$img = new Imagick($src);
$img->adaptiveResize(100, 100);
$img->save($dest . '06.jpg');

title('Imagick scale');
$img = new Imagick($src);
$img->scale(0.5);
$img->save($dest . '07.jpg');

title('Imagick crop');
$img = new Imagick($src);
$img->crop(500, 100, 200, 300);
$img->save($dest . '08.jpg');

title('Imagick cropCenter');
$img = new Imagick($src);
$img->cropCenter(123, 456);
$img->save($dest . '09.jpg');

title('Imagick rotate');
$img = new Imagick($src);
$img->rotate(45);
$img->save($dest . '10.jpg');

title('Imagick adaptiveResizeQuadrant');
$img = new Imagick($src);
$img->adaptiveResizeQuadrant(200, 100);
$img->save($dest . '11.jpg');

title('Imagick filter');
$img = new Imagick($src);
$img->filter(10, 3, \Imagick::CHANNEL_ALPHA);
$img->save($dest . '12.jpg');

title('Imagick lomography');
$img = new Imagick($src);
$img->lomography();
$img->save($dest . '13.jpg');

title('Imagick addFont');
$img = new Imagick($src);
$img->addFont('text', PATH_FONT);
$img->save($dest . '14.jpg');

title('Imagick block9');
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

title('Imagick saveAnalysis');
Imagick::saveAnalysis($src, $dest . '16.jpg', PATH_FONT);

title('Imagick photos');
for ($i = 0; $i < 10; $i++) {
  $srcs = [];
  for ($j = 0; $j < $i + 1; $j++) {
    $srcs[] = $src;
  }
  Imagick::photos($srcs, $dest . (17 + $i) . '.jpg');
}

$dest = PATH_STORAGE . 'gd-';

title('Gd pad');
$img = new Gd($src);
$img->pad(100, 100);
$img->save($dest . '01.jpg');

title('Gd resizeByWidth');
$img = new Gd($src);
$img->resizeByWidth(100);
$img->save($dest . '02.jpg');

title('Gd resizeByHeight');
$img = new Gd($src);
$img->resizeByHeight(100);
$img->save($dest . '03.jpg');

title('Gd resize');
$img = new Gd($src);
$img->resize(100, 50);
$img->save($dest . '04.jpg');

title('Gd adaptiveResizePercent');
$img = new Gd($src);
$img->adaptiveResizePercent(50, 100, 0.1);
$img->save($dest . '05.jpg');

title('Gd adaptiveResize');
$img = new Gd($src);
$img->adaptiveResize(100, 100);
$img->save($dest . '06.jpg');

title('Gd scale');
$img = new Gd($src);
$img->scale(0.5);
$img->save($dest . '07.jpg');

title('Gd crop');
$img = new Gd($src);
$img->crop(500, 100, 200, 300);
$img->save($dest . '08.jpg');

title('Gd cropCenter');
$img = new Gd($src);
$img->cropCenter(123, 456);
$img->save($dest . '09.jpg');

title('Gd rotate');
$img = new Gd($src);
$img->rotate(45);
$img->save($dest . '10.jpg');

title('Gd adaptiveResizeQuadrant');
$img = new Gd($src);
$img->adaptiveResizeQuadrant(200, 100);
$img->save($dest . '11.jpg');

title('Gd block9');
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

title('Gd photos');
for ($i = 0; $i < 10; $i++) {
  $srcs = [];
  for ($j = 0; $j < $i + 1; $j++) {
    $srcs[] = $src;
  }
  Gd::photos($srcs, $dest . (12 + $i) . '.jpg');
}
