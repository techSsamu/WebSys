<?php
$host = 'localhost';
$db   = 'thesis_system';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Better security for prepared statements
    PDO::ATTR_PERSISTENT         => true,  // Keeps connection open for better performance
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In a real system, don't show the full error to users for security
    die('Database connection failed. Please check if MySQL is running in XAMPP.');
}
?>