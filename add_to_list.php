<?php
include 'auth_check.php';
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$list_id = $data['listId'];
$movie = $data['movie'];

try {
    $stmt = $pdo->prepare("INSERT INTO list_movies (list_id, movie_id, title, poster_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$list_id, $movie['movieId'], $movie['title'], $movie['posterUrl']]);
    
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
