<?php
include 'auth_check.php';
include 'db.php';
include 'api.php'; // Include the API functions

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM favorite_movies WHERE user_id = ?");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();

$movies = []; // Initialize an array to hold search results
$error = ''; // Initialize an error message variable

// Add these movie titles for recommendations
$recommendedTitles = [
    'Inception',
    'The Dark Knight',
    'Avengers: Endgame',
    'Interstellar',
    'The Matrix',
    'Pulp Fiction',
    'Fight Club',
    'Forrest Gump'
];

// Fetch recommended movies
$recommendedMovies = [];
foreach ($recommendedTitles as $title) {
    $movie = fetchMovieData($title);
    if ($movie && isset($movie['Response']) && $movie['Response'] === 'True') {
        $recommendedMovies[] = $movie;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_query'])) {
    $query = $_POST['search_query'];
    $result = searchMovies($query); // Call the search function from api.php
    if (isset($result['Search'])) {
        $movies = $result['Search'];
    } else {
        $error = "No movies found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MovieFlix</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .dashboard-header {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.3rem 1rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 80px;
            display: flex;
            align-items: center;
        }

        .search-container {
            max-width: 300px;
            margin: 0.5rem auto;
            position: relative;
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 0.3rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .search-input-wrapper {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .search-bar {
            width: 100%;
            padding: 0.5rem 2.5rem 0.5rem 1rem;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .search-bar::placeholder {
            color: rgba(255, 255, 255, 0.6);
            font-weight: 300;
            font-size: 0.9rem;
        }

        .search-bar:focus {
            outline: none;
        }

        .search-controls {
            margin-left: 10px;
        }

        .voice-search-btn {
            position: absolute;
            right: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            cursor: pointer;
            padding: 6px;
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .voice-search-btn i {
            font-size: 1rem;
        }

        .voice-search-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .voice-search-btn.listening {
            animation: pulse 1.5s infinite;
            background: #EF4444;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
        }

        .search-button {
            background: linear-gradient(45deg, #6366F1, #A855F7);
            color: white;
            border: none;
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            white-space: nowrap;
        }

        .search-button i {
            font-size: 1rem;
        }

        .search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 15px rgba(239, 68, 68, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .search-container:focus-within {
            transform: scale(1.02);
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.2);
        }

        .search-container::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: linear-gradient(45deg, #6366F1, #A855F7, #6366F1);
            border-radius: 25px;
            z-index: -1;
            opacity: 0.3;
            filter: blur(10px);
        }

        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 2rem;
            padding: 0 2rem 2rem 2rem;
            position: relative;
            z-index: 1;
        }

        .movie-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            margin-bottom: 1rem;
            height: fit-content;
        }

        .movie-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .movie-poster {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .movie-info {
            padding: 1rem;
        }

        .movie-title {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            line-height: 1.4;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .action-button {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-button {
            background: linear-gradient(45deg, #6366F1, #A855F7);
            color: white;
        }

        .remove-button {
            background: linear-gradient(45deg, #EF4444, #B91C1C);
            color: white;
        }

        .action-button:hover {
            transform: translateY(-2px);
        }

        .section-title {
            font-size: 2rem;
            color: #fff;
            margin: 0 0 2rem 0;
            padding: 1rem 2rem;
            background: linear-gradient(45deg, #6366F1, #A855F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            z-index: 2;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 0.3rem; /* Adjusted padding for nav links */
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #6366F1;
        }

        .no-movies {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            padding: 2rem;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.2rem 0.5rem; /* Reduced padding for the nav */
            max-width: 1400px;
            margin: 0 auto;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .favorites-link {
            position: relative;
            color: #fff;
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .favorites-link i {
            font-size: 1.5rem;
            color: #fff;
        }

        .favorites-link:hover i {
            color: #EF4444;
            transform: scale(1.1);
        }

        .favorites-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #EF4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .movie-info-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.8) 70%, transparent 100%);
            padding: 1.5rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-height: 100%;
            overflow-y: auto;
        }

        .movie-card:hover .movie-info-overlay {
            transform: translateY(0);
        }

        .movie-card:hover .movie-poster {
            transform: scale(1.05);
        }

        .movie-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
            font-size: 0.9rem;
            margin: 0.5rem 0;
        }

        .movie-rating {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .movie-rating i {
            color: #FFD700;
        }

        .movie-year {
            color: #ccc;
        }

        .movie-plot {
            color: #ccc;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .add-button {
            background: linear-gradient(45deg, #6366F1, #A855F7);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.8rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .add-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .main-content {
            padding-top: 100px;
            min-height: calc(100vh - 100px);
        }

        @media (max-width: 768px) {
            .main-content {
                padding-top: 120px;
            }

            .section-title {
                font-size: 1.5rem;
                padding: 1rem;
                margin: 0 0 1.5rem 0;
            }

            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 1rem;
                padding: 0 1rem 1rem 1rem;
            }
        }

        .movie-info-overlay::-webkit-scrollbar {
            width: 5px;
        }

        .movie-info-overlay::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }

        .movie-info-overlay::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        /* Add List Modal Styles */
        .add-to-list-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(15, 23, 42, 0.95);
            padding: 2rem;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            z-index: 2000;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: none;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close-modal {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        .list-options {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .existing-lists {
            max-height: 200px;
            overflow-y: auto;
        }

        .list-option {
            display: flex;
            align-items: center;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .list-option:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .new-list-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .new-list-input {
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
        }

        .new-list-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .create-list-btn {
            background: linear-gradient(45deg, #6366F1, #A855F7);
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .create-list-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1999;
            display: none;
        }

        .footer {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 4rem 0 2rem 0;
            margin-top: 4rem;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            padding: 0 2rem;
        }

        .footer-section {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .footer-title {
            font-size: 2rem;
            font-weight: 600;
            background: linear-gradient(45deg, #6366F1, #A855F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .footer-description {
            color: #ccc;
            font-size: 1rem;
            line-height: 1.6;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: #ccc;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .footer-links a:hover {
            color: #6366F1;
            transform: translateX(5px);
        }

        .footer-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.1), transparent);
            margin: 2rem 0;
        }

        .footer-bottom {
            text-align: center;
            padding: 2rem 2rem 0 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .footer-bottom p {
            color: #ccc;
            font-size: 0.9rem;
        }

        .social-links {
            display: flex;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .social-icon {
            color: #ccc;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .social-icon:hover {
            color: #fff;
            transform: translateY(-5px);
            background: linear-gradient(45deg, #6366F1, #A855F7);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
        }

        .footer h4 {
            color: #fff;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background: linear-gradient(45deg, #6366F1, #A855F7);
        }

        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 2rem;
                padding: 0 1rem;
            }

            .footer h4::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .footer-links li, .footer-links a {
                justify-content: center;
            }

            .footer {
                padding: 2rem 0 1rem 0;
            }

            .social-links {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a href="dashboard.php" class="logo">MovieFlix</a>
            <form method="POST" class="search-container" id="searchForm">
                <div class="search-input-wrapper">
                    <input type="text" 
                           name="search_query" 
                           id="searchInput"
                           class="search-bar" 
                           placeholder="Discover movies, TV shows, and more..." 
                           autocomplete="off"
                           required>
                    <button type="button" 
                            class="voice-search-btn" 
                            id="voiceSearchBtn" 
                            title="Search with voice">
                        <i class="fas fa-microphone"></i>
                    </button>
                </div>
                <div class="search-controls">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                        <span>Search</span>
                    </button>
                </div>
            </form>
            <div class="nav-links">
            <a href="dashboard.php">Home</a>
                <a href="favorites.php" class="favorites-link">
                    <i class="fas fa-heart"></i>
                    <?php if (!empty($favorites)): ?>
                        <span class="favorites-count"><?php echo count($favorites); ?></span>
                    <?php endif; ?>
                </a>
                <a href="profile.php">Profile</a>
               
                <a href="logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <?php if (empty($movies)): ?>
            <h2 class="section-title">Recommended Movies</h2>
            <div class="movies-grid">
                <?php foreach ($recommendedMovies as $movie): ?>
                    <div class="movie-card">
                        <img class="movie-poster" 
                             src="<?php echo htmlspecialchars($movie['Poster']); ?>" 
                             alt="<?php echo htmlspecialchars($movie['Title']); ?>">
                        <div class="movie-info-overlay">
                            <h3 class="movie-title"><?php echo htmlspecialchars($movie['Title']); ?></h3>
                            <div class="movie-details">
                                <span class="movie-rating">
                                    <i class="fas fa-star"></i> <?php echo htmlspecialchars($movie['imdbRating']); ?>
                                </span>
                                <span class="movie-year"><?php echo htmlspecialchars($movie['Year']); ?></span>
                            </div>
                            <p class="movie-plot"><?php echo htmlspecialchars($movie['Plot']); ?></p>
                            <button type="button" class="action-button add-button" onclick="showAddToListModal('<?php echo htmlspecialchars($movie['Title']); ?>', '<?php echo htmlspecialchars($movie['imdbID']); ?>', '<?php echo htmlspecialchars($movie['Poster']); ?>')">
                                    <i class="fas fa-heart"></i> Add to Favorites
                                </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h2 class="section-title">Search Results</h2>
            <div class="movies-grid">
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card">
                        <img class="movie-poster" 
                             src="<?php echo htmlspecialchars($movie['Poster']); ?>" 
                             alt="<?php echo htmlspecialchars($movie['Title']); ?>">
                        <div class="movie-info-overlay">
                            <h3 class="movie-title"><?php echo htmlspecialchars($movie['Title']); ?></h3>
                            <div class="movie-details">
                                <span class="movie-year"><?php echo htmlspecialchars($movie['Year']); ?></span>
                            </div>
                            <button type="button" class="action-button add-button" onclick="showAddToListModal('<?php echo htmlspecialchars($movie['Title']); ?>', '<?php echo htmlspecialchars($movie['imdbID']); ?>', '<?php echo htmlspecialchars($movie['Poster']); ?>')">
                                    <i class="fas fa-heart"></i> Add to Favorites
                                </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="add-to-list-modal" id="addToListModal">
        <div class="modal-header">
            <h3 class="modal-title">Add to Favorites List</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <form class="new-list-form" id="newListForm">
            <input type="hidden" id="movieTitle" name="movie_title">
            <input type="hidden" id="movieId" name="movie_id">
            <input type="hidden" id="posterUrl" name="poster_url">
            <input type="text" 
                   class="new-list-input" 
                   placeholder="Enter list name (optional)" 
                   id="listName" 
                   name="list_name">
            <p style="color: #ccc; font-size: 0.9rem; margin-top: -0.5rem;">
                Leave empty to add to "My Favorites"
            </p>
            <button type="submit" class="create-list-btn">
                <i class="fas fa-plus"></i> Add to Favorites
            </button>
        </form>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="footer-title">MovieFlix</h3>
                <p class="footer-description">Your ultimate destination for movies and TV shows. Discover, collect, and enjoy the best of cinema.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="favorites.php"><i class="fas fa-heart"></i> My Favorites</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact Us</h4>
                <ul class="footer-links">
                    <li><i class="fas fa-envelope"></i> support@movieflix.com</li>
                    <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                    <li><i class="fas fa-map-marker-alt"></i> New York, NY 10001</li>
                </ul>
            </div>
        </div>
        <div class="footer-divider"></div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> MovieFlix. All rights reserved.</p>
            <div class="social-links">
                <a href="#" title="Facebook" class="social-icon"><i class="fab fa-facebook"></i></a>
                <a href="#" title="Twitter" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" title="Instagram" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" title="YouTube" class="social-icon"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('searchForm');
            const searchInput = document.getElementById('searchInput');
            const voiceSearchBtn = document.getElementById('voiceSearchBtn');
            
            // Check if browser supports speech recognition
            if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                const SpeechRecognition = window.webkitSpeechRecognition || window.SpeechRecognition;
                const recognition = new SpeechRecognition();
                
                recognition.continuous = false;
                recognition.interimResults = false;
                recognition.lang = 'en-US';

                recognition.onstart = function() {
                    voiceSearchBtn.classList.add('listening');
                    voiceSearchBtn.innerHTML = '<i class="fas fa-stop"></i>';
                    searchInput.placeholder = 'Listening...';
                };

                recognition.onresult = function(event) {
                    const transcript = event.results[0][0].transcript;
                    searchInput.value = transcript;
                    searchForm.submit();
                };

                recognition.onend = function() {
                    voiceSearchBtn.classList.remove('listening');
                    voiceSearchBtn.innerHTML = '<i class="fas fa-microphone"></i>';
                    searchInput.placeholder = 'Search for movies, TV shows, and more...';
                };

                recognition.onerror = function(event) {
                    console.error('Speech recognition error:', event.error);
                    voiceSearchBtn.classList.remove('listening');
                    voiceSearchBtn.innerHTML = '<i class="fas fa-microphone"></i>';
                    searchInput.placeholder = 'Search for movies, TV shows, and more...';
                };

                voiceSearchBtn.addEventListener('click', function() {
                    if (voiceSearchBtn.classList.contains('listening')) {
                        recognition.stop();
                    } else {
                        recognition.start();
                    }
                });
            } else {
                voiceSearchBtn.style.display = 'none';
                console.log('Speech recognition not supported');
            }
        });

        // Add this after your existing script
        document.addEventListener('DOMContentLoaded', function() {
            // Handle add to favorites
            const addFavoriteForms = document.querySelectorAll('form[action="add_favorite.php"]');
            addFavoriteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    fetch('add_favorite.php', {
                        method: 'POST',
                        body: new FormData(this)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Movie added to favorites',
                                showConfirmButton: false,
                                timer: 1500,
                                background: '#1a1a1a',
                                color: '#fff'
                            }).then(() => {
                                // Update favorites count
                                const favCount = document.querySelector('.favorites-count');
                                const currentCount = favCount ? parseInt(favCount.textContent) : 0;
                                if (favCount) {
                                    favCount.textContent = currentCount + 1;
                                } else {
                                    const favLink = document.querySelector('.favorites-link');
                                    const newCount = document.createElement('span');
                                    newCount.className = 'favorites-count';
                                    newCount.textContent = '1';
                                    favLink.appendChild(newCount);
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message || 'Failed to add to favorites',
                                background: '#1a1a1a',
                                color: '#fff'
                            });
                        }
                    });
                });
            });
        });

        let currentMovie = null;

        function showAddToListModal(title, movieId, posterUrl) {
            currentMovie = { title, movieId, posterUrl };
            document.getElementById('modalOverlay').style.display = 'block';
            document.getElementById('addToListModal').style.display = 'block';
            document.getElementById('movieTitle').value = title;
            document.getElementById('movieId').value = movieId;
            document.getElementById('posterUrl').value = posterUrl;
        }

        function closeModal() {
            document.getElementById('modalOverlay').style.display = 'none';
            document.getElementById('addToListModal').style.display = 'none';
            document.getElementById('newListForm').reset();
            currentMovie = null;
        }

        document.getElementById('newListForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const listName = formData.get('list_name').trim();
            
            // If list name is empty, set it to "My Favorites"
            if (!listName) {
                formData.set('list_name', 'My Favorites');
            }
            
            fetch('add_favorite.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: `Movie added to ${listName || 'My Favorites'}`,
                        showConfirmButton: false,
                        timer: 1500,
                        background: '#1a1a1a',
                        color: '#fff'
                    }).then(() => {
                        // Update favorites count
                        const favCount = document.querySelector('.favorites-count');
                        const currentCount = favCount ? parseInt(favCount.textContent) : 0;
                        if (favCount) {
                            favCount.textContent = currentCount + 1;
                        } else {
                            const favLink = document.querySelector('.favorites-link');
                            const newCount = document.createElement('span');
                            newCount.className = 'favorites-count';
                            newCount.textContent = '1';
                            favLink.appendChild(newCount);
                        }
                    });
                    closeModal();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message || 'Failed to add to favorites',
                        background: '#1a1a1a',
                        color: '#fff'
                    });
                }
            });
        });
    </script>
</body>
</html>
