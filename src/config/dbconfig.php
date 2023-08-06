<?php

$servername = getenv('HOST') ? getenv('HOST') : 'localhost';
$dbname = 'incidents';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname;charset=UTF8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}