<?php

namespace M {
  use \M\Core\Connection;

  class User2 extends Model {
    static $tableName = 'User';
  }

  class User3 extends Model {
    static $tableName = 'User';
    static $afterCreates = ['addSuffix'];
    static $afterDeletes = ['clean'];

    public function addSuffix() {
      $this->name = 'Suffix_' . $this->name;
      $this->save();
      return true;
    }
    public function clean() {
      User3::deleteAll();
      return true;
    }
  }
  
  Model::case(\M\Model::CASE_CAMEL);

  include '_.php';

  $sql = "CREATE TABLE `User` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `name` varchar(190) CHARACTER SET utf8mb4 DEFAULT NULL, `age` int(10) DEFAULT NULL, `updateAt` datetime DEFAULT NULL, `createAt` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
  if (Connection::instance()->query($sql)) {
    var_dump('create User table Error');
    exit(1);
  }
}
namespace {
  

  
  echo "CRUD guide Test";


  // Create Test Sample
  \M\User2::truncate();

  $user = \M\User2::create([
    'name' => 'OA'
  ]);

  $user->id == 1 || exit('Err');
  $user->name == 'OA' || exit('Err');

  \M\User2::truncate();

  $datas = [];
  array_push($datas, [
    'name' => 'OA'
  ]);
  array_push($datas, [
    'name' => 'OB'
  ]);

  $result = \M\User2::creates($datas);

  $result == true || exit('Err');

  \M\User2::truncate();
  $user = \M\User3::create([
    'name' => 'OA'
  ]);
  $user->name == 'Suffix_OA' || exit('Err');

  $reset = function() {
    \M\User2::truncate();

    \M\User2::creates([
      ['name' => 'OA', 'age' => 18],
      ['name' => 'OB', 'age' => 28],
      ['name' => 'OC', 'age' => 15],
    ]);
  };

  // Select Test Sample
  $reset();

  $user = \M\User2::one();
  $user->name == 'OA' || exit('Err');

  $user = \M\User2::first();
  $user->name == 'OA' || exit('Err');

  $user = \M\User2::last();
  $user->name == 'OC' || exit('Err');

  $users = \M\User2::all();
  $ans = ['OA', 'OB', 'OC'];

  foreach ($users as $i => $user)
    $user->name == $ans[$i] || exit('Err');

  $user = \M\User2::one(['where' => ['age < ? AND id > ?', 20, 1]]);
  $user->name == 'OC' || exit('Err');

  $user = \M\User2::where('age < ?', 20)->and('id > ?', 1)->one();
  $user->name == 'OC' || exit('Err');

  $user = \M\User2::one('age < ? AND id > ?', 20, 1);
  $user->name == 'OC' || exit('Err');

  $user = \M\User2::one(2);
  $user->name == 'OB' || exit('Err');

  $user = \M\User2::one(['where' => 'age < 20 AND id > 1']);
  $user->name == 'OC' || exit('Err');

  $user = \M\User2::one('age < 20 AND id > 1');
  $user->name == 'OC' || exit('Err');

  $user = \M\User2::where('age < 20 AND id > 1')->one();
  $user->name == 'OC' || exit('Err');
  
  $user = \M\User2::one([
    'limit' => 10,
    'offset' => 1,
    'where' => ['id > ?', 0]]);
  $user->name == 'OB' || exit('Err');

  $user = \M\User2::where('id > ?', 0)->one([
    'limit' => 10,
    'offset' => 1]);
  $user->name == 'OB' || exit('Err');

  $users = \M\User2::all('id > ?', 1);
  $ans = ['OB', 'OC'];
  foreach ($users as $i => $user)
    $user->name == $ans[$i] || exit('Err');
  
  $users = \M\User2::where('id > ?', 1)->all();
  foreach ($users as $i => $user)
    $user->name == $ans[$i] || exit('Err');


  $users = \M\User2::all('id IN (?)', [1, 3]);
  $ans = ['OA', 'OC'];
  foreach ($users as $i => $user)
    $user->name == $ans[$i] || exit('Err');
  
  $users = \M\User2::where('id IN (?)', [1, 3])->all();
  foreach ($users as $i => $user)
    $user->name == $ans[$i] || exit('Err');

  $total = \M\User2::count();
  $total == 3 || exit('Err');

  $total = \M\User2::count('id > 1');
  $total == 2 || exit('Err');

  $total = \M\User2::where('id > ?', 1)->count();
  $total == 2 || exit('Err');

  $user = \M\User2::one(4);
  $user === null || exit('Err');
  
  $user = \M\User2::one(3);
  $user->name == 'OC' || exit('Err');

  $users = \M\User2::all();
  count($users) == 3 || exit('Err');
  $users[1]->name == 'OB' || exit('Err');

  $users = \M\User2::all('id IN (?)', [4, 5, 6]);
  count($users) == 0 || exit('Err');


  // Update Test Sample
  $reset();

  $user = \M\User2::one(2);
  $user->name == 'OB' || exit('Err');
  $user->name = 'OD';
  $user->save() || exit('Err');

  \M\User2::truncate();
  \M\User2::creates([
    ['name' => 'OA', 'age' => 18],
    ['name' => 'OB', 'age' => 28],
    ['name' => 'OC', 'age' => 15],
  ]);
  $result = \M\User2::updateAll([
    'name' => 'OD'
  ], 'id > ?', 1);
  $result || exit('Err');

  \M\User2::truncate();
  \M\User2::creates([
    ['name' => 'OA', 'age' => 18],
    ['name' => 'OB', 'age' => 28],
    ['name' => 'OC', 'age' => 15],
  ]);
  $result = \M\User2::where('id > ?', 1)->update([
    'name' => 'OD'
  ]);
  $result || exit('Err');


  // Delete Test Sample
  $reset();

  $user = \M\User2::one(2);
  $result = $user->delete();
  $result || exit('Err');
  $user = \M\User2::one(2);
  $user && exit('Err');


  $reset();

  $result = \M\User2::deleteAll('id > ?', 1);
  $result || exit('Err');
  $total = \M\User2::count();
  $total == 1 || exit('Err');


  $reset();

  $result = \M\User2::where('id > ?', 1)->delete();
  $result || exit('Err');
  $total = \M\User2::count();
  $total == 1 || exit('Err');



  $reset();

  $user = \M\User3::one(2);
  $user->delete();
  \M\User2::count() == 0 || exit('Err');

  // transaction Test Sample
  \M\User2::truncate();
  $result = \M\transaction(function() {
    $user = \M\User2::create([
      'name' => 'OA'
    ]);
    return false;
  });
  \M\User::count() == 0 || exit('Err');

  echo " - ok\n";
}