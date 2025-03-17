<?php
include 'auth_check.php';
include 'db.php';
include 'api.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT DISTINCT list_name FROM favorite_movies WHERE user_id = ? ORDER BY list_name");
$stmt->execute([$user_id]);
$lists = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - MovieFlix</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: #13151F;
            color: #fff;
            min-height: 100vh;
        }

        .favorites-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 80px 1rem 2rem;
        }

        .page-title {
            font-size: 2.5rem;
            color: #fff;
            margin: 2rem 0;
            text-align: center;
            background: linear-gradient(45deg, #6366F1, #A855F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: sticky;
            top: 0;
            padding: 1rem;
            backdrop-filter: blur(10px);
            z-index: 100;
        }

        .list-section {
            margin-bottom: 3rem;
        }

        .list-title {
            font-size: 1.8rem;
            color: #fff;
            margin: 2rem 0 1.5rem 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(99, 102, 241, 0.3);
            background: linear-gradient(45deg, #6366F1, #A855F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .movie-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            height: 400px;
        }

        .movie-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .movie-poster {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .movie-info-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.8) 70%, transparent 100%);
            padding: 1.5rem;
            transform: translateY(75%);
            transition: transform 0.3s ease;
        }

        .movie-card:hover .movie-info-overlay {
            transform: translateY(0);
        }

        .movie-card:hover .movie-poster {
            transform: scale(1.05);
        }

        .movie-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.8rem;
            line-height: 1.4;
        }

        .movie-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0;
        }

        .movie-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #fff;
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
            margin-bottom: 1.2rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .remove-button {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(45deg, #EF4444, #B91C1C);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .remove-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }

        .empty-favorites {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            margin: 2rem 0;
        }

        .empty-favorites i {
            font-size: 4rem;
            color: #6366F1;
            margin-bottom: 1.5rem;
        }

        .empty-favorites p {
            font-size: 1.2rem;
            color: #ccc;
            margin-bottom: 1.5rem;
        }

        .browse-button {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(45deg, #6366F1, #A855F7);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .browse-button:hover {
            transform: translateY(-2px);
        }

        .header {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            color: #fff;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 600;
            background: linear-gradient(45deg, #6366F1, #A855F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #6366F1;
        }

        @media (max-width: 768px) {
            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 1rem;
            }

            .movie-card {
                height: 300px;
            }

            .movie-title {
                font-size: 1rem;
            }

            .nav {
                flex-direction: column;
                padding: 0.5rem;
            }

            .nav-links {
                width: 100%;
                justify-content: center;
                margin-top: 0.5rem;
                gap: 1rem;
            }

            .nav-links a {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
        }

        .list-button {
            width: 100%;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 1.2rem;
            text-align: left;
            cursor: pointer;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .list-button:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .list-button i {
            transition: transform 0.3s ease;
        }

        .list-button.active i {
            transform: rotate(180deg);
        }

        .list-section {
            margin-bottom: 1.5rem;
        }

        .movies-grid {
            display: none; /* Hide by default */
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            padding: 1rem;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .movie-count {
            background: rgba(99, 102, 241, 0.3);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .footer {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem 0;
            width: 100%;
            margin-top: auto; /* This pushes the footer to the bottom */
        }

        .footer-bottom {
            text-align: center;
            padding: 0 2rem;
        }

        .footer-bottom p {
            color: #ccc;
            font-size: 0.9rem;
            background: linear-gradient(45deg, #6366F1, #A855F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 500;
            margin: 0;
        }

        /* Update main content to ensure footer stays at bottom */
        .main-content {
            min-height: calc(100vh - 80px - 73px); /* header height + footer height */
            padding-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .footer {
                padding: 1rem 0;
            }
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .favorites-container {
            flex: 1;
            padding: 80px 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <header class="header">
            <nav class="nav">
                <a href="dashboard.php" class="logo">MovieFlix</a>
                <div class="nav-links">
                    <a href="dashboard.php">Home</a>
                    <a href="profile.php">Profile</a>
                    
                    <a href="logout.php">Logout</a>
                </div>
            </nav>
        </header>

        <div class="favorites-container">
            <h1 class="page-title">My Favorites</h1>

            <?php if (empty($lists)): ?>
                <div class="empty-favorites">
                    <i class="fas fa-heart-broken"></i>
                    <p>You haven't added any movies to your favorites yet.</p>
                    <a href="dashboard.php" class="browse-button">Browse Movies</a>
                </div>
            <?php else: ?>
                <?php foreach ($lists as $list): 
                    $stmt = $pdo->prepare("SELECT * FROM favorite_movies WHERE user_id = ? AND list_name = ?");
                    $stmt->execute([$user_id, $list['list_name']]);
                    $movies = $stmt->fetchAll();
                    
                    if (!empty($movies)):
                        $movieCount = count($movies);
                ?>
                    <div class="list-section">
                        <button class="list-button" onclick="toggleMovies('<?php echo htmlspecialchars($list['list_name']); ?>')">
                            <span><?php echo htmlspecialchars($list['list_name']); ?></span>
                            <div class="button-right">
                                <span class="movie-count"><?php echo $movieCount; ?> movies</span>
                                <i class="fas fa-chevron-down" style="margin-left: 1rem;"></i>
                            </div>
                        </button>
                        <div class="movies-grid" id="<?php echo htmlspecialchars($list['list_name']); ?>">
                            <?php foreach ($movies as $movie): 
                                $movieDetails = fetchMovieData($movie['movie_title']);
                            ?>
                                <div class="movie-card">
                                    <img class="movie-poster" 
                                         src="<?php echo htmlspecialchars($movie['poster_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($movie['movie_title']); ?>">
                                    <div class="movie-info-overlay">
                                        <h3 class="movie-title"><?php echo htmlspecialchars($movie['movie_title']); ?></h3>
                                        <?php if ($movieDetails): ?>
                                        <div class="movie-details">
                                            <span class="movie-rating">
                                                <i class="fas fa-star"></i> <?php echo htmlspecialchars($movieDetails['imdbRating']); ?>
                                            </span>
                                            <span class="movie-year"><?php echo htmlspecialchars($movieDetails['Year']); ?></span>
                                        </div>
                                        <p class="movie-plot"><?php echo substr(htmlspecialchars($movieDetails['Plot']), 0, 100); ?>...</p>
                                        <?php endif; ?>
                                        <form method="POST" action="remove_favorite.php" class="remove-form">
                                            <input type="hidden" name="movie_id" value="<?php echo htmlspecialchars($movie['movie_id']); ?>">
                                            <button type="submit" class="remove-button">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; 
                endforeach; ?>
            <?php endif; ?>
        </div>

        <footer class="footer">
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> MovieFlix. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <script>
        function toggleMovies(listName) {
            const moviesGrid = document.getElementById(listName);
            const button = moviesGrid.previousElementSibling;
            const allGrids = document.querySelectorAll('.movies-grid');
            const allButtons = document.querySelectorAll('.list-button');

            // Close all other sections
            allGrids.forEach(grid => {
                if (grid.id !== listName) {
                    grid.style.display = 'none';
                }
            });
            allButtons.forEach(btn => {
                if (btn !== button) {
                    btn.classList.remove('active');
                }
            });

            // Toggle current section
            if (moviesGrid.style.display === 'grid') {
                moviesGrid.style.display = 'none';
                button.classList.remove('active');
            } else {
                moviesGrid.style.display = 'grid';
                button.classList.add('active');
            }
        }

        // Handle remove button with confirmation
        document.querySelectorAll('.remove-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                Swal.fire({
                    title: 'Remove from Favorites?',
                    text: "This action cannot be undone",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Yes, remove it!',
                    background: '#1a1a1a',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
