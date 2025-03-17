<?php
include 'auth_check.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $movie_id = $_POST['movie_id'];

    // Delete the favorite movie from the database
    $stmt = $pdo->prepare("DELETE FROM favorite_movies WHERE user_id = ? AND movie_id = ?");
    
    try {
        $stmt->execute([$user_id, $movie_id]);
        header("Location: dashboard.php?message=Movie removed from favorites");
    } catch(PDOException $e) {
        header("Location: dashboard.php?error=Failed to remove movie");
    }
    exit();
}

// If not POST request, redirect back to dashboard
header("Location: dashboard.php");
exit();
?>
