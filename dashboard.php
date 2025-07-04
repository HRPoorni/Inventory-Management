<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

require 'db_connection.php'; // Include database connection

// Get user role from session
$user_role = $_SESSION['role'];

// Initialize search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Prepare and execute the inventory query
$stmt = $conn->prepare("SELECT * FROM inventory WHERE item_name LIKE ? OR description LIKE ?");
$search_term = "%" . $search_query . "%";
$stmt->bind_param("ss", $search_term, $search_term);

if (!$stmt->execute()) {
    // Handle query execution error
    die("Query failed: " . $stmt->error);
}

$inventory_items = $stmt->get_result(); // Get the result set

// Handle error messages
$error_message = "";
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/home.css" rel="stylesheet">
    
    <style>
        .hero-section {
            position: relative;
            overflow: hidden;
            padding: 80px 0;
            margin-bottom: 30px;
            border-radius: 15px;
            background-color: #063970;
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.3;
            z-index: 0;
            background-size: cover;
            background-position: center;
            transition: transform 0.5s ease;
        }
        
        .hero-section:hover .hero-bg {
            transform: scale(1.05);
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            color: white;
        }
        
        .profile-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .user-info {
            margin-left: 20px;
        }
        
        .role-badge {
            background-color: #e7f0fd;
            color: #0d6efd;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            margin-top: 5px;
        }
        
        .footer {
            background-color: rgb(5, 36, 81);
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }
        
        .footer a {
            color: white;
            text-decoration: none;
        }
        
        .footer a:hover {
            color: #f8f9fa;
            text-decoration: underline;
        }
        
        .social-links a {
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .search-section {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="hero-section">
            <div class="hero-bg" style="background-image: url('assets/images/background.jpg');"></div>
            <div class="container hero-content">
                <div class="profile-section">
                    <!-- Default profile picture, replace with actual user profile path if available -->
                    <img src="assets/images/profile.jpg" alt="Profile Picture" class="profile-picture">
                    <div class="user-info">
                        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                        <div class="role-badge">
                            <?php echo ucfirst(htmlspecialchars($user_role)); ?>
                        </div>
                    </div>
                </div>
                <p class="lead">Fisheries Inventory Management System Dashboard</p>
            </div>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="search-section">
            <form method="get" action="" class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search inventory items..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>

            <?php if ($user_role === 'admin'): ?>
                <div class="mt-3">
                    <a href="add_item.php" class="btn btn-success"><i class="bi bi-plus-circle me-2"></i>Add New Item</a>
                    <a href="export_pdf.php" class="btn btn-secondary"><i class="bi bi-file-pdf me-2"></i>Export to PDF</a>
                    <a href="export_excel.php" class="btn btn-secondary"><i class="bi bi-file-excel me-2"></i>Export to Excel</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <h4><i class="bi bi-box me-2"></i>Inventory Items</h4>
            <table class="table table-hover mt-3">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $inventory_items->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>
                                <?php if ($user_role === 'admin'): ?>
                                    <a href="edit_item.php?id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a>
                                    <a href="delete_item.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm mt-1"><i class="bi bi-trash"></i> Delete</a>
                                <?php else: ?>
                                    <span class="badge bg-secondary">View Only</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Contact Information</h5>
                    <p>New Secretariat, Maligawatta, Colombo 10, Sri Lanka</p>
                    <p>Email: info@fisheries.gov.lk</p>
                    <p>Phone: +94 112 446 183</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.html#home">Home</a></li>
                        <li><a href="index.html#about">About</a></li>
                        <li><a href="index.html#gallery">Gallery</a></li>
                        <li><a href="index.html#contact">Contact</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Follow Us</h5>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2025 Ministry of Fisheries and Aquatic Development. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>