<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Make sure db.php is in the correct path
require_once 'db.php';  // Changed from include to require_once

// Check if $pdo exists
if (!isset($pdo)) {
    die("Database connection not established");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Password validation
    $password_length = strlen($password);
    $has_symbol = preg_match('/[^a-zA-Z0-9]/', $password);
    $number_count = preg_match_all('/[0-9]/', $password);
    
    if ($password_length < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (!$has_symbol) {
        $error = "Password must contain at least 1 special character.";
    } elseif ($number_count < 2) {
        $error = "Password must contain at least 2 numbers.";
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password]);
            header("Location: login.php?registered=true");
            exit();
        } catch (PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MovieFlix</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .password-requirements {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #ccc;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .requirement i {
            margin-right: 0.5rem;
            font-size: 0.8rem;
        }
        
        .valid {
            color: #10B981;
        }
        
        .invalid {
            color: #ccc;
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #EF4444;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
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
                <h1>Create Account</h1>
                <p>Join MovieFlix to access unlimited entertainment</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    
                    <div class="password-requirements">
                        <div class="requirement" id="length-req">
                            <i class="fas fa-circle"></i> At least 10 characters long
                        </div>
                        <div class="requirement" id="symbol-req">
                            <i class="fas fa-circle"></i> At least 1 special character
                        </div>
                        <div class="requirement" id="number-req">
                            <i class="fas fa-circle"></i> At least 2 numbers
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary auth-button" id="submit-btn" disabled>Create Account</button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign In</a></p>
            </div>
        </div>
    </div>

    <div class="auth-background">
        <div class="auth-overlay"></div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const lengthReq = document.getElementById('length-req');
            const symbolReq = document.getElementById('symbol-req');
            const numberReq = document.getElementById('number-req');
            const submitBtn = document.getElementById('submit-btn');
            
            function validatePassword() {
                const password = passwordInput.value;
                let isValid = true;
                
                // Check length
                if (password.length >= 10) {
                    lengthReq.innerHTML = '<i class="fas fa-check-circle valid"></i> At least 10 characters long';
                } else {
                    lengthReq.innerHTML = '<i class="fas fa-circle invalid"></i> At least 10 characters long';
                    isValid = false;
                }
                
                // Check for symbol
                if (/[^a-zA-Z0-9]/.test(password)) {
                    symbolReq.innerHTML = '<i class="fas fa-check-circle valid"></i> At least 1 special character';
                } else {
                    symbolReq.innerHTML = '<i class="fas fa-circle invalid"></i> At least 1 special character';
                    isValid = false;
                }
                
                // Check for numbers
                const numberCount = (password.match(/[0-9]/g) || []).length;
                if (numberCount >= 2) {
                    numberReq.innerHTML = '<i class="fas fa-check-circle valid"></i> At least 2 numbers';
                } else {
                    numberReq.innerHTML = '<i class="fas fa-circle invalid"></i> At least 2 numbers';
                    isValid = false;
                }
                
                // Enable/disable submit button
                submitBtn.disabled = !isValid;
            }
            
            passwordInput.addEventListener('input', validatePassword);
        });
    </script>
</body>
</html>