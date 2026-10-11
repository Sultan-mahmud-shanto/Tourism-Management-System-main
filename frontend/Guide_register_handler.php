<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Tourism_Project";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the form data
$guide_name = $_POST['Guide_name'];
$gender = $_POST['gender'];
$phone_number = $_POST['phone_number'];
$experience = $_POST['year_of_experience'];
$username = $_POST['username'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Validate passwords match
if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Get the current maximum Guide_ID from the database
$result = $conn->query("SELECT MAX(Guide_ID) AS max_value FROM Guide");
if (!$result) {
    die("Query failed: " . $conn->error);
}
$row = $result->fetch_assoc();
$incremented_id = $row['max_value'] + 1;

// Start from 2525 if no records exist
if ($incremented_id === NULL) {
    $incremented_id = 2525;
}

// Use a prepared statement to insert data
$sql = "INSERT INTO Guide (Phone_number, Guide_ID, Guide_Name, Gender, Year_of_Experience_, username, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepared statement failed: " . $conn->error);
}
$stmt->bind_param("sisssss", $phone_number, $incremented_id, $guide_name, $gender, $experience, $username, $password);

if ($stmt->execute()) {
    header("Location: Guide_index.html");
    exit; // Stop further execution
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
