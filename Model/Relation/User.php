<?php

namespace Model\Relation;

class User extends \Orm\Model {
  public function article() {
    return $this->hasOne(\Model\Relation\Article::class);
  }
  public function articles() {
    return $this->hasMany(\Model\Relation\Article::class);
  }

  public function article2() {
    return $this->hasOne(\Model\Relation\Article::class, 'aId');
  }
  public function article2s() {
    return $this->hasMany(\Model\Relation\Article::class, 'aId');
  }

  public function article3() {
    return $this->hasOne(\Model\Relation\Article::class, 'relationUserId', 'uId');
  }
  public function article3s() {
    return $this->hasMany(\Model\Relation\Article::class, 'relationUserId', 'uId');
  }

  public function article4() {
    return $this->hasOne(\Model\Relation\Article::class, 'aId', 'uId');
  }
  public function article4s() {
    return $this->hasMany(\Model\Relation\Article::class, 'aId', 'uId');
  }
}
