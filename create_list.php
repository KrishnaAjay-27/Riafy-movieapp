<?php
include 'auth_check.php';
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$list_name = $data['listName'];
$movie = $data['movie'];

try {
    // First create the list
    $stmt = $pdo->prepare("INSERT INTO movie_lists (user_id, name) VALUES (?, ?)");
    $stmt->execute([$user_id, $list_name]);
    $list_id = $pdo->lastInsertId();

    // Then add the movie to the list
    $stmt = $pdo->prepare("INSERT INTO list_movies (list_id, movie_id, title, poster_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$list_id, $movie['movieId'], $movie['title'], $movie['posterUrl']]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
