<?php
include 'auth_check.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM movie_lists WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($lists);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
