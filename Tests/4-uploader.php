<?php

include '_.php';

\M\Model::case(\M\Model::CASE_CAMEL);

$sql = "CREATE TABLE `User` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `a` varchar(190) CHARACTER SET utf8mb4 DEFAULT NULL, `b` text COLLATE utf8mb4_unicode_ci, `c` enum('a') COLLATE utf8mb4_unicode_ci DEFAULT NULL, `d` datetime DEFAULT NULL, `e` timestamp NULL DEFAULT NULL, `f` time DEFAULT NULL, `g` date DEFAULT NULL, `h` float DEFAULT NULL, `i` double DEFAULT NULL, `j` decimal(10,2) DEFAULT NULL, `k` json DEFAULT NULL, `a1` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '', `b1` text COLLATE utf8mb4_unicode_ci NOT NULL, `c1` enum('a','b') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'a', `d1` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `e1` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `f1` time NOT NULL, `g1` date NOT NULL, `h1` float NOT NULL, `i1` double NOT NULL, `j1` decimal(10,2) NOT NULL, `k1` json NOT NULL, `avatar` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `zip` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `updateAt` datetime DEFAULT NULL, `createAt` datetime DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
if (\M\Core\Connection::instance()->query($sql)) {
  var_dump('create User table Error');
  exit(1);
}

$user = \M\User::create(['a' => '123', 'a1' => '123', 'b1' => '123', 'c1' => 'a', 'd1' => new DateTime("2020-10-22 12:12:12"), 'e1' => '2020-10-22 12:12:12', 'f1' => '12:12:12', 'g1' => '2020-10-22', 'h1' => '123', 'i1' => '123', 'j1' => '123', 'k1' => []]);

$path = PATH_SAMPLE . 'avatar2.jpg';

if (!file_exists($path) && !@copy(PATH_SAMPLE . 'avatar.jpg', $path)) {
  var_dump('copy sampel pic Error');
  exit(1);
}

$path2 = PATH_SAMPLE . 'avatar2.zip';
if (!file_exists($path2) && !@copy(PATH_SAMPLE . 'avatar.zip', $path2)) {
  var_dump('copy sampel zip Error');
  exit(1);
}

@exec('rm -rf ' . PATH . 'Storage/*');

define('D4', 'http://dev.orm.ioa.tw/404.png');
define('D42', 'https://test.test/404');

echo "avatar default url";
$user->avatar->url() == D4 || exit('- error');
echo " - ok\n";

echo "avatar put not image file";
$user->avatar->put($path2) === false || exit('- error');
echo " - ok\n";

echo "avatar put file";
$user->avatar->put($path) || exit('- error');
echo " - ok\n";

echo "avatar url";
($user->avatar->url() && is_string($user->avatar->url()) && strlen($user->avatar->url()) && $user->avatar->url() != D4) || exit('- error');
echo " - ok\n";

echo "avatar url c120x120";
($user->avatar->url('c120x120') && is_string($user->avatar->url('c120x120')) && strlen($user->avatar->url('c120x120')) && $user->avatar->url('c120x120') != $user->avatar->url() && $user->avatar->url('c120x120') != D4) || exit('- error');
echo " - ok\n";

echo "avatar url rotate";
($user->avatar->url('rotate') && is_string($user->avatar->url('rotate')) && strlen($user->avatar->url('rotate')) && $user->avatar->url('rotate') != $user->avatar->url() && $user->avatar->url('rotate') != D4) || exit('- error');
echo " - ok\n";

echo "avatar url aaa";
($user->avatar->url('aaa') && is_string($user->avatar->url('aaa')) && strlen($user->avatar->url('aaa')) && $user->avatar->url('aaa') == $user->avatar->url() && $user->avatar->url('aaa') != D4) || exit('- error');
echo " - ok\n";

echo "avatar clean files";
($user->avatar->clean() && !array_filter(@scandir(PATH . implode(DIRECTORY_SEPARATOR, $user->avatar->dirs())) ?: [], function($file) { return $file[0] !== '.'; })) || exit('- error');
echo " - ok\n";

echo "avatar put url";
$user->avatar->put('http://dev.orm.ioa.tw/Sample/cover.jpg') || exit('- error');
echo " - ok\n";

$path = PATH_SAMPLE . 'avatar2.jpg';
if (!file_exists($path) && !@copy(PATH_SAMPLE . 'avatar.jpg', $path)) {
  var_dump('copy sampel pic Error');
  exit(1);
}

echo "avatar put post";
$curl  = curl_init('http://dev.orm.ioa.tw/Tests/post.php');
curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POSTFIELDS => ['avatar' => curl_file_create($path, 'image/jpeg', 'avatar')]]);
$data  = curl_exec($curl);
$code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_errno($curl);
$msg   = curl_error($curl);
curl_close($curl);

($error || $msg) && exit('- post error');

$data = @json_decode($data, true);
$data !== null || exit('- json_decode error');
$data['result'] ?? exit('- result error');
$data['url'] ?? exit('- url error');
$data['url'] ?: exit('- url error');
is_string($data['url']) ?: exit('- url error');
strlen($data['url']) ?: exit('- url error');
echo " - ok\n";

$path2 = PATH_SAMPLE . 'avatar2.zip';
if (!file_exists($path2) && !@copy(PATH_SAMPLE . 'avatar.zip', $path2)) {
  var_dump('copy sampel zip Error');
  exit(1);
}

echo "zip default url";
$user->zip->url() == D42 || exit('- error');
echo " - ok\n";

echo "zip put file";
$user->zip->put($path2) || exit('- error');
echo " - ok\n";

echo "zip save as file";
@unlink($path2 = PATH_SAMPLE . 'a.zip');
($user->zip->saveAs($path2) && file_exists($path2)) || exit('- error');
echo " - ok\n";


echo "zip clean files";
($user->zip->clean() && !array_filter(@scandir(PATH . implode(DIRECTORY_SEPARATOR, $user->zip->dirs())) ?: [], function($file) { return $file[0] !== '.'; })) || exit('- error');
echo " - ok\n";

@unlink($path);
@unlink($path2);
@exec('rm -rf ' . PATH . 'Storage/*');
