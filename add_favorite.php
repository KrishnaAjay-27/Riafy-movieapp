<?php
include 'auth_check.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $movie_title = $_POST['movie_title'];
    $movie_id = $_POST['movie_id'];
    $poster_url = $_POST['poster_url'];
    $list_name = $_POST['list_name'] ?? 'My Favorites'; // Default to 'My Favorites' if no list name provided

    try {
        // Check if movie already exists in any of user's lists
        $check_stmt = $pdo->prepare("SELECT * FROM favorite_movies WHERE user_id = ? AND movie_id = ?");
        $check_stmt->execute([$user_id, $movie_id]);
        
        if ($check_stmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Movie already in favorites']);
            exit;
        }

        // Add movie to favorites with list name
        $stmt = $pdo->prepare("INSERT INTO favorite_movies (user_id, movie_id, movie_title, poster_url, list_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $movie_id, $movie_title, $poster_url, $list_name]);
        
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Favorite Movie</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #121212;
            color: #ffffff;
            text-align: center;
            padding: 50px;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: rgba(30, 30, 30, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #ff6347;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #e5533d;
        }
    </style>
</head>
<body>
    <form method="POST">
        <input type="text" name="movie_title" placeholder="Movie Title" required>
        <input type="text" name="movie_id" placeholder="Movie ID" required>
        <input type="text" name="poster_url" placeholder="Poster URL" required>
        <button type="submit">Add to Favorites</button>
    </form>
</body>
</html>
