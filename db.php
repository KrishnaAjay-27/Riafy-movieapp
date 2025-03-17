<?php
$host = 'sql12.freesqldatabase.com';
$db = 'sql12768077';  // Your database name
$user = 'sql12768077'; // Your username
$pass = 'FvS4Fkq5iX'; // Password from FreeSQLDatabasesql12768077

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
