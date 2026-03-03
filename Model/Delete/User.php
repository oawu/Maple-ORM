<?php

namespace Model\Delete;

class User extends \Orm\Model {
  static $afterDeletes = ['func1', 'func2'];

  public function func1() {
    return true;
  }

  public function func2() {
    return true;
  }
}
