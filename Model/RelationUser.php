<?php

namespace M;

class RelationUser extends Model {
  public function article()   { return hasOne(\M\RelationArticle::class); }
  public function articles()  { return hasMany(\M\RelationArticle::class); }

  public function article2()  { return self::hasOne(\M\RelationArticle::class, 'aId'); }
  public function article2s() { return self::hasMany(\M\RelationArticle::class, 'aId'); }

  public function article3()  { return self::hasOne(\M\RelationArticle::class, 'relationUserId', 'uId'); }
  public function article3s() { return self::hasMany(\M\RelationArticle::class, 'relationUserId', 'uId'); }

  public function article4()  { return self::hasOne(\M\RelationArticle::class, 'aId', 'uId'); }
  public function article4s() { return self::hasMany(\M\RelationArticle::class, 'aId', 'uId'); }
}
