<?php

$servername = "localhost";  // "localhost"
$username = "root";      //  database username
$password = "";      //  database password
$dbname = "fmis";          // The name of database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
  
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debug statement to verify connection
//echo "Connected successfully";

// Optional: Set character set (recommended)
$conn->set_charset("utf8");
?>


