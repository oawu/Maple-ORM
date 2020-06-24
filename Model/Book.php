<?php

namespace M;

class Book extends Model {
  static $tableName = 'books';
  static $createAt = 'created_at';
  static $updateAt = 'updated_at';
    
  static $relations = [
    'b1_01' => '=> Owner',
    'b1_02' => '=> Owner.id',
    'b1_03' => 'owner_id => Owner',
    'b1_04' => 'owner_id => Owner.id',
    'b1_05' => ['belongToMany' => 'Owner'],
    'b1_06' => ['belongToMany' => 'Owner', 'primary' => 'id'],
    'b1_07' => ['belongToMany' => 'Owner', 'foreign' => 'owner_id'],
    'b1_08' => ['belongToMany' => 'Owner', 'primary' => 'id', 'foreign' => 'owner_id'],

    'b1_09' => '-> Owner',
    'b1_11' => '-> Owner.id',
    'b1_12' => 'owner_id -> Owner',
    'b1_13' => 'owner_id -> Owner.id',
    'b1_14' => ['belongToOne' => 'Owner'],
    'b1_15' => ['belongToOne' => 'Owner', 'primary' => 'id'],
    'b1_16' => ['belongToOne' => 'Owner', 'foreign' => 'owner_id'],
    'b1_17' => ['belongToOne' => 'Owner', 'primary' => 'id', 'foreign' => 'owner_id'],

    'b1_18' => ['belongToMany' => 'Owner', 'where' => ['name LIKE ?', '%a%']],
    'b1_19' => ['belongToMany' => 'Owner', 'where' => 'name LIKE "%a%"'],
    'b1_21' => ['belongToMany' => 'Owner', 'primary' => 'id', 'where' => ['name LIKE ?', '%a%']],
    'b1_22' => ['belongToMany' => 'Owner', 'primary' => 'id', 'where' => 'name LIKE "%a%"'],
    'b1_23' => ['belongToMany' => 'Owner', 'foreign' => 'owner_id', 'where' => ['name LIKE ?', '%a%']],
    'b1_24' => ['belongToMany' => 'Owner', 'foreign' => 'owner_id', 'where' => 'name LIKE "%a%"'],
    'b1_25' => ['belongToMany' => 'Owner', 'primary' => 'id', 'foreign' => 'owner_id', 'where' => ['name LIKE ?', '%a%']],
    'b1_26' => ['belongToMany' => 'Owner', 'primary' => 'id', 'foreign' => 'owner_id', 'where' => 'name LIKE "%a%"'],

    'b1_27' => ['belongToOne' => 'Owner', 'where' => ['name LIKE ?', '%a%']],
    'b1_28' => ['belongToOne' => 'Owner', 'where' => 'name LIKE "%a%"'],
    'b1_29' => ['belongToOne' => 'Owner', 'primary' => 'id', 'where' => ['name LIKE ?', '%a%']],
    'b1_30' => ['belongToOne' => 'Owner', 'primary' => 'id', 'where' => 'name LIKE "%a%"'],
    'b1_31' => ['belongToOne' => 'Owner', 'foreign' => 'owner_id', 'where' => ['name LIKE ?', '%a%']],
    'b1_32' => ['belongToOne' => 'Owner', 'foreign' => 'owner_id', 'where' => 'name LIKE "%a%"'],
    'b1_33' => ['belongToOne' => 'Owner', 'primary' => 'id', 'foreign' => 'owner_id', 'where' => ['name LIKE ?', '%a%']],
    'b1_34' => ['belongToOne' => 'Owner', 'primary' => 'id', 'foreign' => 'owner_id', 'where' => 'name LIKE "%a%"'],

    'b1_35' => ['belongToMany' => 'Owner', 'select' => 'nick'],
    'b1_36' => ['belongToMany' => 'Owner', 'select' => 'nick', 'primary' => 'id'],
    'b1_37' => ['belongToMany' => 'Owner', 'select' => 'nick', 'foreign' => 'owner_id'],
    'b1_38' => ['belongToMany' => 'Owner', 'select' => 'nick', 'primary' => 'id', 'foreign' => 'owner_id'],

    'b1_39' => ['belongToOne' => 'Owner', 'select' => 'nick'],
    'b1_40' => ['belongToOne' => 'Owner', 'select' => 'nick', 'primary' => 'id'],
    'b1_41' => ['belongToOne' => 'Owner', 'select' => 'nick', 'foreign' => 'owner_id'],
    'b1_42' => ['belongToOne' => 'Owner', 'select' => 'nick', 'primary' => 'id', 'foreign' => 'owner_id'],

    // ----------------

    'b2_01' => '=> Owner.fake_id',
    'b2_02' => 'owner_id => Owner.fake_id',
    'b2_03' => ['belongToMany' => 'Owner', 'primary' => 'fake_id'],
    'b2_04' => ['belongToMany' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'owner_id'],

    'b2_05' => '-> Owner.fake_id',
    'b2_06' => 'owner_id -> Owner.fake_id',
    'b2_07' => ['belongToOne' => 'Owner', 'primary' => 'fake_id'],
    'b2_08' => ['belongToOne' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'owner_id'],

    'b2_09' => ['belongToMany' => 'Owner', 'primary' => 'fake_id', 'where' => ['name LIKE ?', '%a%']],
    'b2_10' => ['belongToMany' => 'Owner', 'primary' => 'fake_id', 'where' => 'name LIKE "%a%"'],
    'b2_11' => ['belongToMany' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'where' => ['name LIKE ?', '%a%']],
    'b2_12' => ['belongToMany' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'where' => 'name LIKE "%a%"'],

    'b2_13' => ['belongToOne' => 'Owner', 'primary' => 'fake_id', 'where' => ['name LIKE ?', '%a%']],
    'b2_14' => ['belongToOne' => 'Owner', 'primary' => 'fake_id', 'where' => 'name LIKE "%a%"'],
    'b2_15' => ['belongToOne' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'where' => ['name LIKE ?', '%a%']],
    'b2_16' => ['belongToOne' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'where' => 'name LIKE "%a%"'],

    'b2_17' => ['belongToMany' => 'Owner', 'primary' => 'fake_id', 'select' => 'nick'],
    'b2_18' => ['belongToMany' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'select' => 'nick'],
    
    'b2_19' => ['belongToOne' => 'Owner', 'primary' => 'fake_id', 'select' => 'nick'],
    'b2_20' => ['belongToOne' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'owner_id', 'select' => 'nick'],

    // ----------------

    'b3_01' => 'fake_id => Owner',
    'b3_02' => 'fake_id => Owner.id',
    'b3_03' => ['belongToMany' => 'Owner', 'foreign' => 'fake_id'],
    'b3_04' => ['belongToMany' => 'Owner', 'foreign' => 'fake_id', 'primary' => 'id'],

    'b3_05' => 'fake_id -> Owner',
    'b3_06' => 'fake_id -> Owner.id',
    'b3_07' => ['belongToOne' => 'Owner', 'foreign' => 'fake_id'],
    'b3_08' => ['belongToOne' => 'Owner', 'foreign' => 'fake_id', 'primary' => 'id'],

    'b3_09' => ['belongToMany' => 'Owner', 'foreign' => 'fake_id', 'where' => ['name LIKE ?', '%a%']],
    'b3_10' => ['belongToMany' => 'Owner', 'foreign' => 'fake_id', 'where' => 'name LIKE "%a%"'],
    'b3_11' => ['belongToMany' => 'Owner', 'foreign' => 'fake_id', 'primary' => 'id', 'where' => ['name LIKE ?', '%a%']],
    'b3_12' => ['belongToMany' => 'Owner', 'foreign' => 'fake_id', 'primary' => 'id', 'where' => 'name LIKE "%a%"'],

    'b3_13' => ['belongToOne' => 'Owner', 'foreign' => 'fake_id', 'where' => ['name LIKE ?', '%a%']],
    'b3_14' => ['belongToOne' => 'Owner', 'foreign' => 'fake_id', 'where' => 'name LIKE "%a%"'],
    'b3_15' => ['belongToOne' => 'Owner', 'foreign' => 'fake_id', 'primary' => 'id', 'where' => ['name LIKE ?', '%a%']],
    'b3_16' => ['belongToOne' => 'Owner', 'foreign' => 'fake_id', 'primary' => 'id', 'where' => 'name LIKE "%a%"'],

    'b3_17' => ['belongToMany' => 'Owner', 'foreign' => 'fake_id', 'select' => 'nick'],
    'b3_18' => ['belongToMany' => 'Owner', 'foreign' => 'fake_id', 'primary' => 'id', 'select' => 'nick'],

    'b3_19' => ['belongToOne' => 'Owner', 'foreign' => 'fake_id', 'select' => 'nick'],
    'b3_20' => ['belongToOne' => 'Owner', 'foreign' => 'fake_id', 'primary' => 'id', 'select' => 'nick'],

    // ----------------

    'b4_01' => 'fake_id => Owner.fake_id',
    'b4_02' => ['belongToMany' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'fake_id'],

    'b4_03' => 'fake_id -> Owner.fake_id',
    'b4_04' => ['belongToOne' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'fake_id'],

    'b4_05' => ['belongToMany' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'where' => ['name LIKE ?', '%a%']],
    'b4_06' => ['belongToMany' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'where' => 'name LIKE "%a%"'],

    'b4_07' => ['belongToOne' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'where' => ['name LIKE ?', '%a%']],
    'b4_08' => ['belongToOne' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'where' => 'name LIKE "%a%"'],

    'b4_09' => ['belongToMany' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'select' => 'nick'],
    'b4_10' => ['belongToOne' => 'Owner', 'primary' => 'fake_id', 'foreign' => 'fake_id', 'select' => 'nick'],

  ];
}
