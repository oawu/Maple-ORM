<?php

namespace Model\Relation;

class Article extends \Orm\Model {
  public function user() {
    return $this->belongsTo(\Model\Relation\User::class);
  }
  public function users() {
    return $this->belongsToMany(\Model\Relation\User::class);
  }

  public function user2() {
    return $this->belongsTo(\Model\Relation\User::class, 'aId');
  }
  public function user2s() {
    return $this->belongsToMany(\Model\Relation\User::class, 'aId');
  }

  public function user3() {
    return $this->belongsTo(\Model\Relation\User::class, 'relationUserId', 'uId');
  }
  public function user3s() {
    return $this->belongsToMany(\Model\Relation\User::class, 'relationUserId', 'uId');
  }

  public function user4() {
    return $this->belongsTo(\Model\Relation\User::class, 'aId', 'uId');
  }
  public function user4s() {
    return $this->belongsToMany(\Model\Relation\User::class, 'aId', 'uId');
  }
}
