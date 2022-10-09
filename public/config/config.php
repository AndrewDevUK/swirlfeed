<?php
ob_start();
session_start();

$timezone = date_default_timezone_set("Europe/London");

$server = 'mysql';
$db = 'swirlfeed';
$db_username = 'andrew';
$db_password = 'password';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO("mysql:dbname=$db;host=$server", $db_username, $db_password, $options);

?>