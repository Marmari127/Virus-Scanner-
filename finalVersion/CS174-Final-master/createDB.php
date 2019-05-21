<?php
require_once 'login.php';
$connection = new mysqli($hn, $un, $pw, 'final');
if($connection -> connect_error) die($connection->connect_error);

$query = "CREATE TABLE userTb (username VARCHAR(32), password VARCHAR(32))";
$result = $connection->query ($query);
if(!$result) die ($connection->error);

$query = "CREATE TABLE adminvirusTb (name VARCHAR(32), sig VARCHAR(20))";
$result = $connection->query($query);
if(!$result) die($connection->error);


$query = "CREATE TABLE uservirusTb (name VARCHAR(32), sig VARCHAR(20))";
$result = $connection->query($query);
if(!$result) die($connection->error);



$salt1 = "Hacking";
$salt2 = "Duo";

$user1 = "GrappaShots";
$pass = "ILove174";
$token = hash('ripemd128', "$salt1$pass$salt2");

add_user($connection, $user1, $pass);

$user2 = "ILoveToHack";
$pass = "ILove174Too";
$token = hash('ripemd128', "$salt1$pass$salt2");

add_user($connection, $user2, $pass);

function add_user($connection, $un, $pass)
{
    $query = "INSERT INTO users VALUES('$un', '$pass')";
    $result = $connection->query($query);
    if(!$result) die($connection->error);
}
?>
