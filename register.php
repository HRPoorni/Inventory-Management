<?php 
session_start(); 
require 'db_connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $profile_picture = '';
    
    // Check if uploads directory exists and is writable
    if (!file_exists('uploads')) {
        mkdir('uploads', 0755, true);
    }
    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
        
        // Check if file was successfully uploaded
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture = $target_file;
        } else {
            $error = "Error uploading file. Check directory permissions.";
        }
    }
    
    // Only proceed with database operation if no error occurred with file upload
    if (!isset($error)) {
        try {
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, profile_picture) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("ssss", $username, $password, $role, $profile_picture);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            header('Location: login.php');
            exit();
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | FMIS</title>
    
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome (for icons) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <style>
        /* Additional Register-specific CSS */
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #005073, #2c73d2);
        }
        
        .register-row {
            display: flex;
            width: 90%;
            max-width: 1200px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .register-left {
            width: 40%;
            background: linear-gradient(135deg, #005073, #0077b6);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .register-left-content {
            text-align: center;
        }
        
        .register-title {
            font-size: 26px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .register-subtitle {
            font-size: 16px;
        }
        
        .register-right {
            width: 60%;
            padding: 40px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h2 {
            color: #005073;
            font-weight: bold;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 25px;
        }
        
        .form-group i {
            position: absolute;
            left: 15px;
            top: 15px;
            color: #aaa;
        }
        
        .form-control {
            height: 50px;
            border-radius: 25px;
            padding-left: 45px;
            border: 1px solid #ddd;
        }
        
        .form-control-file {
            padding-left: 10px;
            border-radius: 25px;
        }
        
        .btn-register {
            width: 100%;
            height: 50px;
            border-radius: 25px;
            background: linear-gradient(135deg, #005073, #0077b6);
            color: white;
            font-weight: bold;
            border: none;
            margin-top: 15px;
        }
        
        .btn-register:hover {
            background: linear-gradient(135deg, #00405d, #006da4);
        }
        
        .register-footer {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-footer a {
            color: #005073;
            text-decoration: none;
        }
        
        .register-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-row">
        <!-- Left side -->
        <div class="register-left">
            <div class="register-left-content">
                <img src="assets/images/ministry logo.jpg" alt="Ministry Logo" height="200" class="me-2">
                <div class="register-title">Welcome to IT Inventory Management System</div>
                <div class="register-subtitle">Ministry of Fisheries & Aquatic Resources Development</div>
            </div>
        </div>

        <!-- Right side -->
        <div class="register-right">
            <div class="register-header">
                <h2>Create Account</h2>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <i class="fas fa-user"></i>
                </div>
                
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <i class="fas fa-lock"></i>
                    <span class="password-toggle" onclick="togglePassword()"><i class="fas fa-eye"></i></span>
                </div>
                
                <div class="form-group">
                    <select class="form-control" id="role" name="role">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                    </select>
                    <i class="fas fa-user-tag"></i>
                </div>
                
                <div class="form-group">
                    <label for="profile_picture" class="file-label">Profile Picture</label>
                    <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
                </div>
                
                <button type="submit" class="btn btn-register">Register Now</button>
                
                <div class="register-footer">
                    <p>Already have an account? <a href="login.php">Login</a></p>
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