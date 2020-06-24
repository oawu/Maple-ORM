<?php

namespace M;

class Owner extends Model {
  static $tableName = 'owners';
  static $createAt = 'created_at';
  static $updateAt = 'updated_at';

  static $relations = [
    'a1_01' => 'Book',
    'a1_02' => 'Book.owner_id',
    'a1_03' => '<= Book',
    'a1_04' => '<= Book.owner_id',
    'a1_05' => 'id <= Book',
    'a1_06' => 'id <= Book.owner_id',
    'a1_07' => ['hasMany' => 'Book'],
    'a1_08' => ['hasMany' => 'Book', 'foreign' => 'owner_id'],
    'a1_09' => ['hasMany' => 'Book', 'primary' => 'id'],
    'a1_10' => ['hasMany' => 'Book', 'primary' => 'id', 'foreign' => 'owner_id'],

    'a1_11' => '<- Book',
    'a1_12' => '<- Book.owner_id',
    'a1_13' => 'id <- Book',
    'a1_14' => 'id <- Book.owner_id',
    'a1_15' => ['hasOne' => 'Book'],
    'a1_16' => ['hasOne' => 'Book', 'foreign' => 'owner_id'],
    'a1_17' => ['hasOne' => 'Book', 'primary' => 'id'],
    'a1_18' => ['hasOne' => 'Book', 'primary' => 'id', 'foreign' => 'owner_id'],

    'a1_19' => ['hasMany' => 'Book', 'where' => ['score > ?', 5]],
    'a1_20' => ['hasMany' => 'Book', 'where' => 'score > 5'],
    'a1_21' => ['hasMany' => 'Book', 'foreign' => 'owner_id', 'where' => ['score > ?', 5]],
    'a1_22' => ['hasMany' => 'Book', 'foreign' => 'owner_id', 'where' => 'score > 5'],
    'a1_23' => ['hasMany' => 'Book', 'primary' => 'id', 'where' => ['score > ?', 5]],
    'a1_24' => ['hasMany' => 'Book', 'primary' => 'id', 'where' => 'score > 5'],
    'a1_25' => ['hasMany' => 'Book', 'primary' => 'id', 'foreign' => 'owner_id', 'where' => ['score > ?', 5]],
    'a1_26' => ['hasMany' => 'Book', 'primary' => 'id', 'foreign' => 'owner_id', 'where' => 'score > 5'],

    'a1_27' => ['hasOne' => 'Book', 'where' => ['score > ?', 5]],
    'a1_28' => ['hasOne' => 'Book', 'where' => 'score > 5'],
    'a1_29' => ['hasOne' => 'Book', 'foreign' => 'owner_id', 'where' => ['score > ?', 5]],
    'a1_30' => ['hasOne' => 'Book', 'foreign' => 'owner_id', 'where' => 'score > 5'],
    'a1_31' => ['hasOne' => 'Book', 'primary' => 'id', 'where' => ['score > ?', 5]],
    'a1_32' => ['hasOne' => 'Book', 'primary' => 'id', 'where' => 'score > 5'],
    'a1_33' => ['hasOne' => 'Book', 'primary' => 'id', 'foreign' => 'owner_id', 'where' => ['score > ?', 5]],
    'a1_34' => ['hasOne' => 'Book', 'primary' => 'id', 'foreign' => 'owner_id', 'where' => 'score > 5'],

    'a1_35' => ['hasMany' => 'Book', 'select' => 'content'],
    'a1_36' => ['hasMany' => 'Book', 'foreign' => 'owner_id', 'select' => 'content'],
    'a1_37' => ['hasMany' => 'Book', 'primary' => 'id', 'select' => 'content'],
    'a1_38' => ['hasMany' => 'Book', 'primary' => 'id', 'foreign' => 'owner_id', 'select' => 'content'],

    'a1_39' => ['hasOne' => 'Book', 'select' => 'content'],
    'a1_40' => ['hasOne' => 'Book', 'foreign' => 'owner_id', 'select' => 'content'],
    'a1_41' => ['hasOne' => 'Book', 'primary' => 'id', 'select' => 'content'],
    'a1_42' => ['hasOne' => 'Book', 'primary' => 'id', 'foreign' => 'owner_id', 'select' => 'content'],

    // ----------------

    'a2_01' => 'Book.fake_id',
    'a2_02' => '<= Book.fake_id',
    'a2_03' => 'id <= Book.fake_id',
    'a2_04' => ['hasMany' => 'Book', 'foreign' => 'fake_id'],
    'a2_05' => ['hasMany' => 'Book', 'primary' => 'id', 'foreign' => 'fake_id'],

    'a2_06' => '<- Book.fake_id',
    'a2_07' => ['hasOne' => 'Book', 'foreign' => 'fake_id'],
    'a2_08' => 'id <- Book.fake_id',
    'a2_09' => ['hasOne' => 'Book', 'primary' => 'id', 'foreign' => 'fake_id'],

    'a2_10' => ['hasMany' => 'Book', 'foreign' => 'fake_id', 'where' => ['score > ?', 5]],
    'a2_11' => ['hasMany' => 'Book', 'foreign' => 'fake_id', 'where' => 'score > 5'],
    'a2_12' => ['hasMany' => 'Book', 'primary' => 'id', 'foreign' => 'fake_id', 'where' => ['score > ?', 5]],
    'a2_13' => ['hasMany' => 'Book', 'primary' => 'id', 'foreign' => 'fake_id', 'where' => 'score > 5'],

    'a2_14' => ['hasOne' => 'Book', 'foreign' => 'fake_id', 'where' => ['score > ?', 5]],
    'a2_15' => ['hasOne' => 'Book', 'foreign' => 'fake_id', 'where' => 'score > 5'],
    'a2_16' => ['hasOne' => 'Book', 'primary' => 'id', 'foreign' => 'fake_id', 'where' => ['score > ?', 5]],
    'a2_17' => ['hasOne' => 'Book', 'primary' => 'id', 'foreign' => 'fake_id', 'where' => 'score > 5'],

    'a2_18' => ['hasMany' => 'Book', 'foreign' => 'fake_id', 'select' => 'content'],
    'a2_19' => ['hasMany' => 'Book', 'primary' => 'id', 'foreign' => 'fake_id', 'select' => 'content'],
    
    'a2_20' => ['hasOne' => 'Book', 'foreign' => 'fake_id', 'select' => 'content'],
    'a2_21' => ['hasOne' => 'Book', 'primary' => 'id', 'foreign' => 'fake_id', 'select' => 'content'],

    // ----------------

    'a3_01' => 'fake_id <= Book',
    'a3_02' => 'fake_id <= Book.owner_id',
    'a3_03' => ['hasMany' => 'Book', 'primary' => 'fake_id'],
    'a3_04' => ['hasMany' => 'Book', 'primary' => 'fake_id', 'foreign' => 'owner_id'],

    'a3_05' => 'fake_id <- Book',
    'a3_06' => 'fake_id <- Book.owner_id',
    'a3_07' => ['hasOne' => 'Book', 'primary' => 'fake_id'],
    'a3_08' => ['hasOne' => 'Book', 'primary' => 'fake_id', 'foreign' => 'owner_id'],


    'a3_09' => ['hasMany' => 'Book', 'primary' => 'fake_id', 'where' => ['score > ?', 5]],
    'a3_10' => ['hasMany' => 'Book', 'primary' => 'fake_id', 'where' => 'score > 5'],
    'a3_11' => ['hasMany' => 'Book', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'where' => ['score > ?', 5]],
    'a3_12' => ['hasMany' => 'Book', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'where' => 'score > 5'],

    'a3_13' => ['hasOne' => 'Book', 'primary' => 'fake_id', 'where' => ['score > ?', 5]],
    'a3_14' => ['hasOne' => 'Book', 'primary' => 'fake_id', 'where' => 'score > 5'],
    'a3_15' => ['hasOne' => 'Book', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'where' => ['score > ?', 5]],
    'a3_16' => ['hasOne' => 'Book', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'where' => 'score > 5'],

    'a3_17' => ['hasMany' => 'Book', 'primary' => 'fake_id', 'select' => 'content'],
    'a3_18' => ['hasMany' => 'Book', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'select' => 'content'],

    'a3_19' => ['hasOne' => 'Book', 'primary' => 'fake_id', 'select' => 'content'],
    'a3_20' => ['hasOne' => 'Book', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'select' => 'content'],

    // ----------------

    'a4_01' => 'fake_id <= Book.fake_id',
    'a4_02' => ['hasMany' => 'Book', 'primary' => 'fake_id', 'foreign' => 'fake_id'],

    'a4_03' => 'fake_id <- Book.fake_id',
    'a4_04' => ['hasOne' => 'Book', 'primary' => 'fake_id', 'foreign' => 'fake_id'],

    'a4_05' => ['hasMany' => 'Book', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'where' => ['score > ?', 5]],
    'a4_06' => ['hasMany' => 'Book', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'where' => 'score > 5'],

    'a4_07' => ['hasOne' => 'Book', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'where' => ['score > ?', 5]],
    'a4_08' => ['hasOne' => 'Book', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'where' => 'score > 5'],

    'a4_09' => ['hasMany' => 'Book', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'select' => 'content'],
    'a4_10' => ['hasOne' => 'Book', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'select' => 'content'],
  ];
}
