<?php

namespace M;

class Article extends Model {

  static $relations = [
    'comments' => 'Comment',

    'b1_01' => '=> Member',
    'b1_02' => '=> Member.id',
    'b1_03' => 'memberId => Member',
    'b1_04' => 'memberId => Member.id',
    'b1_05' => ['belongToMany' => 'Member'],
    'b1_06' => ['belongToMany' => 'Member', 'primary' => 'id'],
    'b1_07' => ['belongToMany' => 'Member', 'foreign' => 'memberId'],
    'b1_08' => ['belongToMany' => 'Member', 'primary' => 'id', 'foreign' => 'memberId'],

    'b1_09' => '-> Member',
    'b1_11' => '-> Member.id',
    'b1_12' => 'memberId -> Member',
    'b1_13' => 'memberId -> Member.id',
    'b1_14' => ['belongToOne' => 'Member'],
    'b1_15' => ['belongToOne' => 'Member', 'primary' => 'id'],
    'b1_16' => ['belongToOne' => 'Member', 'foreign' => 'memberId'],
    'b1_17' => ['belongToOne' => 'Member', 'primary' => 'id', 'foreign' => 'memberId'],

    'b1_18' => ['belongToMany' => 'Member', 'where' => ['name LIKE ?', '%a%']],
    'b1_19' => ['belongToMany' => 'Member', 'where' => 'name LIKE "%a%"'],
    'b1_21' => ['belongToMany' => 'Member', 'primary' => 'id', 'where' => ['name LIKE ?', '%a%']],
    'b1_22' => ['belongToMany' => 'Member', 'primary' => 'id', 'where' => 'name LIKE "%a%"'],
    'b1_23' => ['belongToMany' => 'Member', 'foreign' => 'memberId', 'where' => ['name LIKE ?', '%a%']],
    'b1_24' => ['belongToMany' => 'Member', 'foreign' => 'memberId', 'where' => 'name LIKE "%a%"'],
    'b1_25' => ['belongToMany' => 'Member', 'primary' => 'id', 'foreign' => 'memberId', 'where' => ['name LIKE ?', '%a%']],
    'b1_26' => ['belongToMany' => 'Member', 'primary' => 'id', 'foreign' => 'memberId', 'where' => 'name LIKE "%a%"'],

    'b1_27' => ['belongToOne' => 'Member', 'where' => ['name LIKE ?', '%a%']],
    'b1_28' => ['belongToOne' => 'Member', 'where' => 'name LIKE "%a%"'],
    'b1_29' => ['belongToOne' => 'Member', 'primary' => 'id', 'where' => ['name LIKE ?', '%a%']],
    'b1_30' => ['belongToOne' => 'Member', 'primary' => 'id', 'where' => 'name LIKE "%a%"'],
    'b1_31' => ['belongToOne' => 'Member', 'foreign' => 'memberId', 'where' => ['name LIKE ?', '%a%']],
    'b1_32' => ['belongToOne' => 'Member', 'foreign' => 'memberId', 'where' => 'name LIKE "%a%"'],
    'b1_33' => ['belongToOne' => 'Member', 'primary' => 'id', 'foreign' => 'memberId', 'where' => ['name LIKE ?', '%a%']],
    'b1_34' => ['belongToOne' => 'Member', 'primary' => 'id', 'foreign' => 'memberId', 'where' => 'name LIKE "%a%"'],

    'b1_35' => ['belongToMany' => 'Member', 'select' => 'nick'],
    'b1_36' => ['belongToMany' => 'Member', 'select' => 'nick', 'primary' => 'id'],
    'b1_37' => ['belongToMany' => 'Member', 'select' => 'nick', 'foreign' => 'memberId'],
    'b1_38' => ['belongToMany' => 'Member', 'select' => 'nick', 'primary' => 'id', 'foreign' => 'memberId'],

    'b1_39' => ['belongToOne' => 'Member', 'select' => 'nick'],
    'b1_40' => ['belongToOne' => 'Member', 'select' => 'nick', 'primary' => 'id'],
    'b1_41' => ['belongToOne' => 'Member', 'select' => 'nick', 'foreign' => 'memberId'],
    'b1_42' => ['belongToOne' => 'Member', 'select' => 'nick', 'primary' => 'id', 'foreign' => 'memberId'],

    // ----------------

    'b2_01' => '=> Member.fakeId',
    'b2_02' => 'memberId => Member.fakeId',
    'b2_03' => ['belongToMany' => 'Member', 'primary' => 'fakeId'],
    'b2_04' => ['belongToMany' => 'Member', 'primary' => 'fakeId', 'foreign' => 'memberId'],

    'b2_05' => '-> Member.fakeId',
    'b2_06' => 'memberId -> Member.fakeId',
    'b2_07' => ['belongToOne' => 'Member', 'primary' => 'fakeId'],
    'b2_08' => ['belongToOne' => 'Member', 'primary' => 'fakeId', 'foreign' => 'memberId'],

    'b2_09' => ['belongToMany' => 'Member', 'primary' => 'fakeId', 'where' => ['name LIKE ?', '%a%']],
    'b2_10' => ['belongToMany' => 'Member', 'primary' => 'fakeId', 'where' => 'name LIKE "%a%"'],
    'b2_11' => ['belongToMany' => 'Member', 'primary' => 'fakeId', 'foreign' => 'memberId', 'where' => ['name LIKE ?', '%a%']],
    'b2_12' => ['belongToMany' => 'Member', 'primary' => 'fakeId', 'foreign' => 'memberId', 'where' => 'name LIKE "%a%"'],

    'b2_13' => ['belongToOne' => 'Member', 'primary' => 'fakeId', 'where' => ['name LIKE ?', '%a%']],
    'b2_14' => ['belongToOne' => 'Member', 'primary' => 'fakeId', 'where' => 'name LIKE "%a%"'],
    'b2_15' => ['belongToOne' => 'Member', 'primary' => 'fakeId', 'foreign' => 'memberId', 'where' => ['name LIKE ?', '%a%']],
    'b2_16' => ['belongToOne' => 'Member', 'primary' => 'fakeId', 'foreign' => 'memberId', 'where' => 'name LIKE "%a%"'],

    'b2_17' => ['belongToMany' => 'Member', 'primary' => 'fakeId', 'select' => 'nick'],
    'b2_18' => ['belongToMany' => 'Member', 'primary' => 'fakeId', 'foreign' => 'memberId', 'select' => 'nick'],
    
    'b2_19' => ['belongToOne' => 'Member', 'primary' => 'fakeId', 'select' => 'nick'],
    'b2_20' => ['belongToOne' => 'Member', 'primary' => 'fakeId', 'foreign' => 'memberId', 'select' => 'nick'],

    // ----------------

    'b3_01' => 'fakeId => Member',
    'b3_02' => 'fakeId => Member.id',
    'b3_03' => ['belongToMany' => 'Member', 'foreign' => 'fakeId'],
    'b3_04' => ['belongToMany' => 'Member', 'foreign' => 'fakeId', 'primary' => 'id'],

    'b3_05' => 'fakeId -> Member',
    'b3_06' => 'fakeId -> Member.id',
    'b3_07' => ['belongToOne' => 'Member', 'foreign' => 'fakeId'],
    'b3_08' => ['belongToOne' => 'Member', 'foreign' => 'fakeId', 'primary' => 'id'],

    'b3_09' => ['belongToMany' => 'Member', 'foreign' => 'fakeId', 'where' => ['name LIKE ?', '%a%']],
    'b3_10' => ['belongToMany' => 'Member', 'foreign' => 'fakeId', 'where' => 'name LIKE "%a%"'],
    'b3_11' => ['belongToMany' => 'Member', 'foreign' => 'fakeId', 'primary' => 'id', 'where' => ['name LIKE ?', '%a%']],
    'b3_12' => ['belongToMany' => 'Member', 'foreign' => 'fakeId', 'primary' => 'id', 'where' => 'name LIKE "%a%"'],

    'b3_13' => ['belongToOne' => 'Member', 'foreign' => 'fakeId', 'where' => ['name LIKE ?', '%a%']],
    'b3_14' => ['belongToOne' => 'Member', 'foreign' => 'fakeId', 'where' => 'name LIKE "%a%"'],
    'b3_15' => ['belongToOne' => 'Member', 'foreign' => 'fakeId', 'primary' => 'id', 'where' => ['name LIKE ?', '%a%']],
    'b3_16' => ['belongToOne' => 'Member', 'foreign' => 'fakeId', 'primary' => 'id', 'where' => 'name LIKE "%a%"'],

    'b3_17' => ['belongToMany' => 'Member', 'foreign' => 'fakeId', 'select' => 'nick'],
    'b3_18' => ['belongToMany' => 'Member', 'foreign' => 'fakeId', 'primary' => 'id', 'select' => 'nick'],

    'b3_19' => ['belongToOne' => 'Member', 'foreign' => 'fakeId', 'select' => 'nick'],
    'b3_20' => ['belongToOne' => 'Member', 'foreign' => 'fakeId', 'primary' => 'id', 'select' => 'nick'],

    // ----------------

    'b4_01' => 'fakeId => Member.fakeId',
    'b4_02' => ['belongToMany' => 'Member', 'primary' => 'fakeId', 'foreign' => 'fakeId'],

    'b4_03' => 'fakeId -> Member.fakeId',
    'b4_04' => ['belongToOne' => 'Member', 'primary' => 'fakeId', 'foreign' => 'fakeId'],

    'b4_05' => ['belongToMany' => 'Member', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'where' => ['name LIKE ?', '%a%']],
    'b4_06' => ['belongToMany' => 'Member', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'where' => 'name LIKE "%a%"'],

    'b4_07' => ['belongToOne' => 'Member', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'where' => ['name LIKE ?', '%a%']],
    'b4_08' => ['belongToOne' => 'Member', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'where' => 'name LIKE "%a%"'],

    'b4_09' => ['belongToMany' => 'Member', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'select' => 'nick'],
    'b4_10' => ['belongToOne' => 'Member', 'primary' => 'fakeId', 'foreign' => 'fakeId', 'select' => 'nick'],

  ];
}
