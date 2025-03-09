<?php

namespace M\Delete;

use M\Model;

class User extends Model {
  static $afterDeletes = ['func1', 'func2'];

  public function func1() {
    echo "after delete func1";
    return true;
  }

  public function func2() {
    echo "after delete func2";
    return true;
  }}
