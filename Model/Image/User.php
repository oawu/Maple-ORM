<?php

namespace M\Image;

use M\Model;

use M\Core\Plugin\Uploader\Image;

class User extends Model {}

User::bindImage('avatar1', function(Image $image) {
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

