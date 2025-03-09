<?php

namespace Model\ReadWrite;

class Book extends \Orm\Model {

  public function user1() {
    return $this->belongsTo(User::class);
  }
  public function user1s() {
    return $this->belongsToMany(User::class);
  }

  public function user2() {
    return $this->belongsTo(User::class, 'y', 'x');
  }
  public function user2s() {
    return $this->belongsToMany(User::class, 'y', 'x');
  }
}
