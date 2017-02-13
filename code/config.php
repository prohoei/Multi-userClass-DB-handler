<?php

$dbHost     = "";
$dbUsername = "";
$dbPassword = "";
$dbName     = "";

// Database connection
try{
    $conn = new PDO("mysql:host=".$dbHost.";dbname=".$dbName.";charset=UTF8", $dbUsername, $dbPassword);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}catch(PDOException $e){
    die("Failed to connect with MySQL: " . $e->getMessage());
}


// Define Classes
spl_autoload_register(function ($class) {
    include_once $_SERVER['DOCUMENT_ROOT'].'/class/' . $class . '.php';
});

$db = new dbHandler($conn);
$valid = new validatorClass();
$gallery = new imgClass();