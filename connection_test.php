<?php
$host = 'sql12.freesqldatabase.com';
$db = 'sql12767636';
$user = 'sql12767636';
$pass = 'F54gcFTLQZ';

try {
    $testPdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "Connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    // Print more details
    echo "<br>Host: " . $host;
    echo "<br>Database: " . $db;
    echo "<br>Username: " . $user;
}
?>
