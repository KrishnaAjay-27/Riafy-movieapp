<?php
include 'auth_check.php';
include 'db.php';

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Count favorite movies
$stmt = $pdo->prepare("SELECT COUNT(*) FROM favorite_movies WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$favoriteCount = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - MovieFlix</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: #13151F;
            color: #fff;
            min-height: 100vh;
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

        .profile-container {
            max-width: 1200px;
            margin: 100px auto 0;
            padding: 2rem;
        }

        .profile-header {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #6366F1, #A855F7);
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .username {
            font-size: 2rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #6366F1, #A855F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .edit-profile-btn {
            background: linear-gradient(45deg, #6366F1, #A855F7);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .edit-profile-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .profile-stats {
            max-width: 300px;
            margin: 2rem auto 0;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.1);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #6366F1, #A855F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            color: #ccc;
            font-size: 1rem;
        }

        .profile-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row {
            display: flex;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 150px;
            color: #ccc;
            font-size: 1rem;
        }

        .info-value {
            color: #fff;
            font-size: 1rem;
        }

        @media (max-width: 768px) {
            .profile-container {
                padding: 1rem;
            }

            .profile-header {
                padding: 2rem 1rem;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
            }

            .username {
                font-size: 1.5rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .info-row {
                flex-direction: column;
                gap: 0.5rem;
            }

            .info-label {
                width: 100%;
            }
        }

        .footer {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem 0;
            margin-top: 4rem;
            position: relative;
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
        }

        @media (max-width: 768px) {
            .footer {
                margin-top: 2rem;
                padding: 1rem 0;
            }
        }

        .favorites-link {
            position: relative;
            padding: 0.5rem !important;
        }

        .favorites-link i {
            font-size: 1.2rem;
            color: #fff;
            transition: all 0.3s ease;
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
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a href="dashboard.php" class="logo">MovieFlix</a>
            <div class="nav-links">
                <a href="dashboard.php">Home</a>
                <a href="favorites.php" class="favorites-link" title="My Favorites">
                    <i class="fas fa-heart"></i>
                    <?php if ($favoriteCount > 0): ?>
                        <span class="favorites-count"><?php echo $favoriteCount; ?></span>
                    <?php endif; ?>
                </a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
            </div>
            <h1 class="username"><?php echo htmlspecialchars($user['username']); ?></h1>
            <a href="edit_profile.php" class="edit-profile-btn">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </div>

        <div class="profile-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $favoriteCount; ?></div>
                <div class="stat-label">
                    <i class="fas fa-heart"></i> Favorite Movies
                </div>
            </div>
        </div>

        <div class="profile-info">
            <div class="info-row">
                <div class="info-label">
                    <i class="fas fa-envelope"></i> Email
                </div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">
                    <i class="fas fa-calendar-alt"></i> Member Since
                </div>
                <div class="info-value"><?php echo date('F Y', strtotime($user['created_at'])); ?></div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> MovieFlix. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
