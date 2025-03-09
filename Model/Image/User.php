<?php

namespace Model\Image;

use \Orm\Core\Plugin\Uploader\Image;

class User extends \Orm\Model {}

User::bindImage('avatar1', function (Image $image) {
  $image
    ->setNamingSort(
      'md5',
      'random',
      'origin',
    );

  $image
    ->addVersion('rotate')
    ->setMethod('rotate')
    ->setArgs(45);
});

User::bindImage('avatar2');
