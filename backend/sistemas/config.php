<?php

$host ='localhost';
$user ='root';
$password ='';
$dbname ='app_sistema';

define('ROOT_PATH', dirname(__DIR__));
$conn = mysqli_connect($host,$user,$password,$dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}