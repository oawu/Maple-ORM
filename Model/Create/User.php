<?php

namespace Model\Create;

class User extends \Orm\Model {
  static $afterCreates = ['func1', 'func2'];

  public function func1() {
    echo "after create func1";
    return true;
  }

  public function func2() {
    echo "after create func2";
    return true;
  }
}
