<?php

namespace M;

class RelationArticle extends Model {
  public function user()   { return belongsTo(\M\RelationUser::class); }
  public function users()  { return belongsToMany(\M\RelationUser::class); }

  public function user2()  { return Model::belongsTo(\M\RelationUser::class, 'aId'); }
  public function user2s() { return Model::belongsToMany(\M\RelationUser::class, 'aId'); }

  public function user3()  { return Model::belongsTo(\M\RelationUser::class, 'relationUserId', 'uId'); }
  public function user3s() { return Model::belongsToMany(\M\RelationUser::class, 'relationUserId', 'uId'); }

  public function user4()  { return Model::belongsTo(\M\RelationUser::class, 'aId', 'uId'); }
  public function user4s() { return Model::belongsToMany(\M\RelationUser::class, 'aId', 'uId'); }
}
