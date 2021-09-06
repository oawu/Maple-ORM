<?php

namespace M;

class ImageUser extends Model {}

ImageUser::uploader('avatar1', 'Image')
         ->version('rotate', 'rotate', [45]);

ImageUser::uploader('avatar2', 'Image');
