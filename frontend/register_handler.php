<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // Default username for XAMPP
$password = "";     // Default password for XAMPP
$dbname = "Tourism_Project"; // Name of your database

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the form data
$tourist_name = $_POST['tourist_name'];
$country = $_POST['country'];
$gender = $_POST['gender'];
$phone_number = $_POST['phone_number'];
$age = $_POST['age'];
$username = $_POST['username'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Validate password match
if ($password !== $confirm_password) {
    die("Passwords do not match.");
}


// Get the current maximum Tourist_ID from the database
$result = $conn->query("SELECT MAX(Tourist_ID) AS max_value FROM Tourist");
$row = $result->fetch_assoc();

// Increment the Tourist_ID by 1
$incremented_id = $row['max_value'] + 1;

// If there are no records yet, start from 22
if ($incremented_id == NULL) {
    $incremented_id = 22;
}
// Insert data into the database
$sql = "INSERT INTO Tourist (Tourist_ID,Tourist_Name, Country, Gender_, Phone_Number, Age, username, password)
        VALUES ('$incremented_id','$tourist_name', '$country', '$gender', '$phone_number', '$age', '$username', '$password')";

if ($conn->query($sql) === TRUE) {
    header("Location: index.html");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the connection
$conn->close();
?>
