<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$item_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
$stmt->bind_param("i", $item_id);

if ($stmt->execute()) {
    header('Location: dashboard.php');
    exit();
} else {
    echo "Error: Could not delete item.";
}
?>
