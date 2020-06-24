<?php

namespace M;

class Member extends Model {

  static $relations = [
    'a1_01' => 'Article',
    'a1_02' => 'Article.memberId',
    'a1_03' => '<= Article',
    'a1_04' => '<= Article.memberId',
    'a1_05' => 'id <= Article',
    'a1_06' => 'id <= Article.memberId',
    'a1_07' => ['hasMany' => 'Article'],
    'a1_08' => ['hasMany' => 'Article', 'foreign' => 'memberId'],
    'a1_09' => ['hasMany' => 'Article', 'primary' => 'id'],
    'a1_10' => ['hasMany' => 'Article', 'primary' => 'id', 'foreign' => 'memberId'],

    'a1_11' => '<- Article',
    'a1_12' => '<- Article.memberId',
    'a1_13' => 'id <- Article',
    'a1_14' => 'id <- Article.memberId',
    'a1_15' => ['hasOne' => 'Article'],
    'a1_16' => ['hasOne' => 'Article', 'foreign' => 'memberId'],
    'a1_17' => ['hasOne' => 'Article', 'primary' => 'id'],
    'a1_18' => ['hasOne' => 'Article', 'primary' => 'id', 'foreign' => 'memberId'],

    'a1_19' => ['hasMany' => 'Article', 'where' => ['score > ?', 5]],
    'a1_20' => ['hasMany' => 'Article', 'where' => 'score > 5'],
    'a1_21' => ['hasMany' => 'Article', 'foreign' => 'memberId', 'where' => ['score > ?', 5]],
    'a1_22' => ['hasMany' => 'Article', 'foreign' => 'memberId', 'where' => 'score > 5'],
    'a1_23' => ['hasMany' => 'Article', 'primary' => 'id', 'where' => ['score > ?', 5]],
    'a1_24' => ['hasMany' => 'Article', 'primary' => 'id', 'where' => 'score > 5'],
    'a1_25' => ['hasMany' => 'Article', 'primary' => 'id', 'foreign' => 'memberId', 'where' => ['score > ?', 5]],
    'a1_26' => ['hasMany' => 'Article', 'primary' => 'id', 'foreign' => 'memberId', 'where' => 'score > 5'],

    'a1_27' => ['hasOne' => 'Article', 'where' => ['score > ?', 5]],
    'a1_28' => ['hasOne' => 'Article', 'where' => 'score > 5'],
    'a1_29' => ['hasOne' => 'Article', 'foreign' => 'memberId', 'where' => ['score > ?', 5]],
    'a1_30' => ['hasOne' => 'Article', 'foreign' => 'memberId', 'where' => 'score > 5'],
    'a1_31' => ['hasOne' => 'Article', 'primary' => 'id', 'where' => ['score > ?', 5]],
    'a1_32' => ['hasOne' => 'Article', 'primary' => 'id', 'where' => 'score > 5'],
    'a1_33' => ['hasOne' => 'Article', 'primary' => 'id', 'foreign' => 'memberId', 'where' => ['score > ?', 5]],
    'a1_34' => ['hasOne' => 'Article', 'primary' => 'id', 'foreign' => 'memberId', 'where' => 'score > 5'],

    'a1_35' => ['hasMany' => 'Article', 'select' => 'content'],
    'a1_36' => ['hasMany' => 'Article', 'foreign' => 'memberId', 'select' => 'content'],
    'a1_37' => ['hasMany' => 'Article', 'primary' => 'id', 'select' => 'content'],
    'a1_38' => ['hasMany' => 'Article', 'primary' => 'id', 'foreign' => 'memberId', 'select' => 'content'],

    'a1_39' => ['hasOne' => 'Article', 'select' => 'content'],
    'a1_40' => ['hasOne' => 'Article', 'foreign' => 'memberId', 'select' => 'content'],
    'a1_41' => ['hasOne' => 'Article', 'primary' => 'id', 'select' => 'content'],
    'a1_42' => ['hasOne' => 'Article', 'primary' => 'id', 'foreign' => 'memberId', 'select' => 'content'],

    // ----------------

    'a2_01' => 'Article.fakeId',
    'a2_02' => '<= Article.fakeId',
    'a2_03' => 'id <= Article.fakeId',
    'a2_04' => ['hasMany' => 'Article', 'foreign' => 'fakeId'],
    'a2_05' => ['hasMany' => 'Article', 'primary' => 'id', 'foreign' => 'fakeId'],

    'a2_06' => '<- Article.fakeId',
    'a2_07' => ['hasOne' => 'Article', 'foreign' => 'fakeId'],
    'a2_08' => 'id <- Article.fakeId',
    'a2_09' => ['hasOne' => 'Article', 'primary' => 'id', 'foreign' => 'fakeId'],

    'a2_10' => ['hasMany' => 'Article', 'foreign' => 'fakeId', 'where' => ['score > ?', 5]],
    'a2_11' => ['hasMany' => 'Article', 'foreign' => 'fakeId', 'where' => 'score > 5'],
    'a2_12' => ['hasMany' => 'Article', 'primary' => 'id', 'foreign' => 'fakeId', 'where' => ['score > ?', 5]],
    'a2_13' => ['hasMany' => 'Article', 'primary' => 'id', 'foreign' => 'fakeId', 'where' => 'score > 5'],

    'a2_14' => ['hasOne' => 'Article', 'foreign' => 'fakeId', 'where' => ['score > ?', 5]],
    'a2_15' => ['hasOne' => 'Article', 'foreign' => 'fakeId', 'where' => 'score > 5'],
    'a2_16' => ['hasOne' => 'Article', 'primary' => 'id', 'foreign' => 'fakeId', 'where' => ['score > ?', 5]],
    'a2_17' => ['hasOne' => 'Article', 'primary' => 'id', 'foreign' => 'fakeId', 'where' => 'score > 5'],

    'a2_18' => ['hasMany' => 'Article', 'foreign' => 'fakeId', 'select' => 'content'],
    'a2_19' => ['hasMany' => 'Article', 'primary' => 'id', 'foreign' => 'fakeId', 'select' => 'content'],
    
    'a2_20' => ['hasOne' => 'Article', 'foreign' => 'fakeId', 'select' => 'content'],
    'a2_21' => ['hasOne' => 'Article', 'primary' => 'id', 'foreign' => 'fakeId', 'select' => 'content'],

    // ----------------

    'a3_01' => 'fakeId <= Article',
    'a3_02' => 'fakeId <= Article.memberId',
    'a3_03' => ['hasMany' => 'Article', 'primary' => 'fakeId'],
    'a3_04' => ['hasMany' => 'Article', 'primary' => 'fakeId', 'foreign' => 'memberId'],

    'a3_05' => 'fakeId <- Article',
    'a3_06' => 'fakeId <- Article.memberId',
    'a3_07' => ['hasOne' => 'Article', 'primary' => 'fakeId'],
    'a3_08' => ['hasOne' => 'Article', 'primary' => 'fakeId', 'foreign' => 'memberId'],


    'a3_09' => ['hasMany' => 'Article', 'primary' => 'fakeId', 'where' => ['score > ?', 5]],
    'a3_10' => ['hasMany' => 'Article', 'primary' => 'fakeId', 'where' => 'score > 5'],
    'a3_11' => ['hasMany' => 'Article', 'primary' => 'fakeId', 'foreign' => 'memberId', 'where' => ['score > ?', 5]],
    'a3_12' => ['hasMany' => 'Article', 'primary' => 'fakeId', 'foreign' => 'memberId', 'where' => 'score > 5'],

    'a3_13' => ['hasOne' => 'Article', 'primary' => 'fakeId', 'where' => ['score > ?', 5]],
    'a3_14' => ['hasOne' => 'Article', 'primary' => 'fakeId', 'where' => 'score > 5'],
    'a3_15' => ['hasOne' => 'Article', 'primary' => 'fakeId', 'foreign' => 'memberId', 'where' => ['score > ?', 5]],
    'a3_16' => ['hasOne' => 'Article', 'primary' => 'fakeId', 'foreign' => 'memberId', 'where' => 'score > 5'],

    'a3_17' => ['hasMany' => 'Article', 'primary' => 'fakeId', 'select' => 'content'],
    'a3_18' => ['hasMany' => 'Article', 'primary' => 'fakeId', 'foreign' => 'memberId', 'select' => 'content'],

    'a3_19' => ['hasOne' => 'Article', 'primary' => 'fakeId', 'select' => 'content'],
    'a3_20' => ['hasOne' => 'Article', 'primary' => 'fakeId', 'foreign' => 'memberId', 'select' => 'content'],

    // ----------------

    'a4_01' => 'fakeId <= Article.fakeId',
    'a4_02' => ['hasMany' => 'Article', 'primary' => 'fakeId', 'foreign' => 'fakeId'],

    'a4_03' => 'fakeId <- Article.fakeId',
    'a4_04' => ['hasOne' => 'Article', 'primary' => 'fakeId', 'foreign' => 'fakeId'],

    'a4_05' => ['hasMany' => 'Article', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'where' => ['score > ?', 5]],
    'a4_06' => ['hasMany' => 'Article', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'where' => 'score > 5'],

    'a4_07' => ['hasOne' => 'Article', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'where' => ['score > ?', 5]],
    'a4_08' => ['hasOne' => 'Article', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'where' => 'score > 5'],

    'a4_09' => ['hasMany' => 'Article', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'select' => 'content'],
    'a4_10' => ['hasOne' => 'Article', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'select' => 'content'],
  ];
}
