<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
    } else {
        $error = "Invalid email or password";
    }
}

if (isset($_GET['message'])):
    $message = htmlspecialchars($_GET['message']);
?>
    <div class="message success">
        <i class="fas fa-check-circle"></i>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MovieFlix</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a href="index.php" class="logo">MovieFlix</a>
        </nav>
    </header>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to continue your entertainment journey</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="auth-error">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <div class="password-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                    
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary auth-button">Sign In</button>
            </form>
            
            <div class="auth-footer">
                <p>New to MovieFlix? <a href="register.php">Create Account</a></p>
            </div>
        </div>
    </div>

    <div class="auth-background">
        <div class="auth-overlay"></div>
    </div>
</body>
</html>
