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

    // Query to fetch user data by username
    $sql = "SELECT * FROM Tourist WHERE username = '$input_username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User exists, now verify password
        $row = $result->fetch_assoc();
        
        if ($input_password === $row['password']) {
            // Password is correct, start session and log user in
            $_SESSION['tourist_id'] = $row['Tourist_ID']; // Store Tourist_ID in session
            $_SESSION['tourist_name'] = $row['Tourist_Name']; // Store Tourist Name in session

            // Redirect to a protected page (e.g., dashboard)
            header("Location: dashboard.html");
            exit();
        } else {
            // Invalid password
            $_SESSION['error_message'] = "Invalid password.";
        }
    } else {
        // User not found
        $_SESSION['error_message'] = "No user found with that username.";
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
    <title>Login</title>
    <style>
        
       
      
       
    
        .error-message {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: rgb(255, 69, 58);
            color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out, fadeOut 5s ease-in-out 3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(20px); }
        }
    </style>
</head>
<body>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form | Dan Aleko</title>
  <link rel="stylesheet" href="style/style.css">
  <link rel="shortcut icon" href="logo.png">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
  <div class="wrapper">
    <form action="login-handler.php" method="POST">
      <h1>Login</h1>
      <div class="input-box">
        <input type="text" name="username" placeholder="Username" required>
        <i class='bx bxs-user'></i>
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Password" required>
        <i class='bx bxs-lock-alt' ></i>
      </div>
      <div class="remember-forgot">
        <label><input type="checkbox">Remember Me</label>
        <a href="#">Forgot Password</a>
      </div>
      <button type="submit" class="btn">Login</button>
      <div class="register-link">
        <p>Dont have an account? <a href="register.html">Register</a></p>
      </div>
    </form>
  </div>



 
</body>
</html>

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
