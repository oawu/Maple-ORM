<?php

namespace M;

class User extends Model {
  // static $tableName = 'users';
  // static $primaries = ['id'];
  // static $updateAt = 'updated_at';
  // static $createAt = 'created_at';

  // static $afterCreates = ['methodName'];
  // static $afterDeletes = ['methodName'];
  // static $relations = [
  // ];

  // static $hides = ['e1'];
}

User::uploader('avatar', 'Image')
    ->version('rotate', 'rotate', [45]);

User::uploader('zip', 'File')
    ->default('https://test.test/404');
