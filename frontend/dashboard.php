<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['tourist_id'])) {
    header("Location: login.html"); // Redirect to login if not logged in
    exit();
}

echo "Welcome, " . $_SESSION['tourist_name']. ".<br>  Tourist ID:   " . $_SESSION['tourist_id']  . "<br>";
?>
<a href="logout.php">Logout</a>
