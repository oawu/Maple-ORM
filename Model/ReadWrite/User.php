<?php

namespace Model\ReadWrite;

class User extends \Orm\Model {
  public function book1s() {
    return $this->hasMany(Book::class);
  }
  public function book1() {
    return $this->hasOne(Book::class);
  }
  public function book2s() {
    return $this->hasMany(Book::class, 'y', 'x');
  }
  public function book2() {
    return $this->hasOne(Book::class, 'y', 'x');
  }
}
