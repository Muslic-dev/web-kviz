<?php
session_start();
$dbname = "kvizzz";
$server = "localhost:3306";
$user = "root";
$pass = "";

try
{
    $conn = new PDO("mysql:host=$server;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: ".$e->getMessage();
}
?>