<?php
ob_start(); // Start output buffering
session_start();

// Include database connection
require 'db_connection.php';

// Initialize error message
$error = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Prepare SQL statement
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if user exists
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['user_id'] = $user['id'];

                    // Redirect to dashboard
                    header('Location: /FMIS/dashboard.php');
                    exit();
                } else {
                    $error = "Invalid password. Please try again.";
                }
            } else {
                $error = "Username not found. Please register or try again.";
            }

            $stmt->close();
        } else {
            $error = "Database error. Please try again later.";
        }
    } else {
        $error = "Both fields are required.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | FMIS</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/login.css">

    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome (for icons) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="login-container">
    <div class="login-row">
        <!-- Left side -->
        <div class="login-left">
            <div class="login-left-content">
            <img src="assets/images/ministry logo.jpg" alt="Ministry Logo" height="200" class="me-2">
                <div class="login-title">Welcome to IT Inventory Management System</div>
                <div class="login-subtitle">Ministry of Fisheries & Aquatic Resources Development</div>
            </div>
        </div>

        <!-- Right side -->
        <div class="login-right">
            <div class="login-header">
                <h2>Login</h2>
            </div>
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <i class="fas fa-user"></i>
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <i class="fas fa-lock"></i>
                    <span class="password-toggle" onclick="togglePassword()"><i class="fas fa-eye"></i></span>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>

                <button type="submit" class="btn btn-login">Login Now</button>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mt-3">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="login-footer">
                    <a href="forgot_password.php">Forgot password?</a> | <a href="register.php">Create account</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const pwd = document.getElementById("password");
        const toggle = document.querySelector(".password-toggle i");
        if (pwd.type === "password") {
            pwd.type = "text";
            toggle.classList.remove("fa-eye");
            toggle.classList.add("fa-eye-slash");
        } else {
            pwd.type = "password";
            toggle.classList.remove("fa-eye-slash");
            toggle.classList.add("fa-eye");
        }
    }
</script>

</body>
</html>

<?php ob_end_flush(); ?>