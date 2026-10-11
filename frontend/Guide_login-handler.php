<?php
// Start the session
session_start();

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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // Query to fetch guide data by username
    $sql = "SELECT * FROM Guide WHERE username = '$input_username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Guide exists, now verify password
        $row = $result->fetch_assoc();
        
        if ($input_password === $row['password']) {
            // Password is correct, start session and log guide in
            $_SESSION['guide_id'] = $row['Guide_ID']; // Store Guide_ID in session
            $_SESSION['guide_name'] = $row['Guide_Name']; // Store Guide Name in session

            // Redirect to a protected page (e.g., guide dashboard)
            header("Location: Booking_check.php");
            exit();
        } else {
            // Invalid password
            $_SESSION['error_message'] = "Invalid password.";
        }
    } else {
        // Guide not found
        $_SESSION['error_message'] = "No guide found with that username.";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Login</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="shortcut icon" href="logo.png">
    <style>
        body{
        background-image: url(Guide.webp);
    }
    .wrapper2{
    width: 420px;
    background: transparent;
    background-color: rgb(47, 81, 70);
    border: 2px solid rgba(255, 255, 255, .2);
    backdrop-filter: blur(9px);
    color: #fff;
    border-radius: 12px;
    padding: 30px 40px;
  }
  .wrapper2 h1{
    font-size: 36px;
    text-align: center;
  }
  .wrapper2 .input-box{
    position: relative;
    width: 100%;
    height: 50px;
    
    margin: 30px 0;
  }
 

  .wrapper2 .remember-forgot{
    display: flex;
    justify-content: space-between;
    font-size: 14.5px;
    margin: -15px 0 15px;
  }



  .wrapper2 .btn{
    width: 100%;
    height: 45px;
    background: #fff;
    border: none;
    outline: none;
    border-radius: 40px;
    box-shadow: 0 0 10px rgba(0, 0, 0, .1);
    cursor: pointer;
    font-size: 16px;
    color: #333;
    font-weight: 600;
  }
  .wrapper2 .register-link{
    font-size: 14.5px;
    text-align: center;
    margin: 20px 0 15px;
  
  }
    </style>
</head>
<body>
  <div class="wrapper2">
    <form action="Guide_login_handler.php" method="POST">
      <h1>Guide Login</h1>
      <div class="input-box">
        <input type="text" name="username" placeholder="Username" required>
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <button type="submit" class="btn">Login</button>
      <div class="register-link">
        <p>Don't have an account? <a href="guide_register.html">Register</a></p>
      </div>
    </form>
  </div>

  <?php if (isset($_SESSION['error_message'])): ?>
      <div class="error-message">
          <?php
          echo $_SESSION['error_message'];
          unset($_SESSION['error_message']); // Clear error message after displaying
          ?>
      </div>
  <?php endif; ?>
</body>
</html>
