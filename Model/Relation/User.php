<?php

namespace M\Relation;

use M\Model;

class User extends Model {
  public function article()   { return $this->hasOne(\M\Relation\Article::class); }
  public function articles()  { return $this->hasMany(\M\Relation\Article::class); }

  public function article2()  { return $this->hasOne(\M\Relation\Article::class, 'aId'); }
  public function article2s() { return $this->hasMany(\M\Relation\Article::class, 'aId'); }

  public function article3()  { return $this->hasOne(\M\Relation\Article::class, 'relationUserId', 'uId'); }
  public function article3s() { return $this->hasMany(\M\Relation\Article::class, 'relationUserId', 'uId'); }

  public function article4()  { return $this->hasOne(\M\Relation\Article::class, 'aId', 'uId'); }
  public function article4s() { return $this->hasMany(\M\Relation\Article::class, 'aId', 'uId'); }
}
