<?php
$host="localhost";
$user="scac8254_gspdi";
$pass="scac8254_gspdi";
$db="scac8254_gspdi";

$conn=new mysqli($host,$user,$pass,$db);
if($conn->connect_error) die("DB Error");
$conn->set_charset("utf8mb4");