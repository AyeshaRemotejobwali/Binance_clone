<?php
session_start();
$host = 'localhost';
$dbname = 'db3m4m5tuqhgtc';
$username = 'uxgukysg8xcbd';
$password = '6imcip8yfmic';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
