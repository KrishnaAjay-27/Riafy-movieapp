<?php
include 'auth_check.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = trim($_POST['current_password']);

    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        try {
            $pdo->beginTransaction();

            // Handle profile update
            if (isset($_POST['update_profile'])) {
                $username = trim($_POST['username']);
                if ($username) {
                    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
                    $stmt->execute([$username, $_SESSION['user_id']]);
                    $message = "Profile updated successfully!";
                }
            }

            // Handle password update
            if (isset($_POST['update_password'])) {
                $new_password = trim($_POST['new_password']);
                $confirm_password = trim($_POST['confirm_password']);

                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashed_password, $_SESSION['user_id']]);
                    
                    $pdo->commit();
                    session_destroy();
                    header("Location: login.php?message=Password updated successfully. Please login again.");
                    exit();
                } else {
                    throw new Exception("New passwords do not match");
                }
            }

            $pdo->commit();
            
            // Refresh user data after profile update
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    } else {
        $error = "Current password is incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - MovieFlix</title>
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

        .edit-profile-container {
            max-width: 600px;
            margin: 100px auto 0;
            padding: 2rem;
        }

        .edit-profile-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .section-title {
            font-size: 1.8rem;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, #6366F1, #A855F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ccc;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #6366F1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #10B981;
        }

        .error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #EF4444;
        }

        .save-button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(45deg, #6366F1, #A855F7);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .save-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .password-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid rgba(255, 255, 255, 0.1);
        }

        #profile-form {
            margin-bottom: 2rem;
        }

        .save-button {
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .edit-profile-container {
                padding: 1rem;
            }

            .edit-profile-card {
                padding: 1.5rem;
            }

            .section-title {
                font-size: 1.5rem;
            }
        }

        .disabled-text {
            font-size: 0.8rem;
            color: #666;
            margin-left: 0.5rem;
            font-style: italic;
        }

        .disabled-input {
            background: rgba(255, 255, 255, 0.02) !important;
            border-color: rgba(255, 255, 255, 0.05) !important;
            cursor: not-allowed;
            color: #666 !important;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .edit-profile-container {
            flex: 1;
            margin: 100px auto 0;
            padding: 2rem;
            max-width: 600px;
            width: 100%;
        }

        .footer {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem 0;
            margin-top: auto;
            width: 100%;
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

        @media (max-width: 768px) {
            .footer {
                padding: 1rem 0;
            }
            
            .edit-profile-container {
                padding: 1rem;
                margin-bottom: 2rem;
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
    <div class="page-wrapper">
        <header class="header">
            <nav class="nav">
                <a href="dashboard.php" class="logo">MovieFlix</a>
                <div class="nav-links">
                    <a href="dashboard.php">Home</a>
                    <a href="favorites.php" class="favorites-link" title="My Favorites">
                        <i class="fas fa-heart"></i>
                        <?php 
                        // Get favorites count
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorite_movies WHERE user_id = ?");
                        $stmt->execute([$_SESSION['user_id']]);
                        $favoriteCount = $stmt->fetchColumn();
                        if ($favoriteCount > 0): 
                        ?>
                            <span class="favorites-count"><?php echo $favoriteCount; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </nav>
        </header>

        <div class="edit-profile-container">
            <div class="edit-profile-card">
                <h1 class="section-title">Edit Profile</h1>
                
                <?php if ($message): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="profile-form">
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i> Username
                        </label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email
                            <span class="disabled-text">(Cannot be changed)</span>
                        </label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" 
                               disabled 
                               class="disabled-input">
                    </div>

                    <div class="form-group">
                        <label for="profile_current_password">
                            <i class="fas fa-key"></i> Current Password (required to save changes)
                        </label>
                        <input type="password" id="profile_current_password" name="current_password" required>
                    </div>

                    <button type="submit" class="save-button" name="update_profile">
                        <i class="fas fa-save"></i> Save Profile Changes
                    </button>
                </form>

                <div class="password-section">
                    <h2 class="section-title">Change Password</h2>
                    
                    <form method="POST" id="password-form">
                        <div class="form-group">
                            <label for="new_password">
                                <i class="fas fa-lock"></i> New Password
                            </label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="fas fa-lock"></i> Confirm New Password
                            </label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>

                        <div class="form-group">
                            <label for="password_current_password">
                                <i class="fas fa-key"></i> Current Password (required to save changes)
                            </label>
                            <input type="password" id="password_current_password" name="current_password" required>
                        </div>

                        <button type="submit" class="save-button" name="update_password">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> MovieFlix. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>
