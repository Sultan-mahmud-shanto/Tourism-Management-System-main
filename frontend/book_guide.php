<?php
session_start();  // Start the session to access Tourist_ID

// Check if Tourist_ID is set in the session
if (!isset($_SESSION['tourist_id'])) {
    die("You must be logged in to book a guide.");
}

$tourist_id = $_SESSION['tourist_id'];  // Get the Tourist_ID from the session

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
    $guide_id = $_POST['Guide_ID'];  // Selected guide ID
    $booking_date = $_POST['Booking_Date'];  // Selected booking date

    // Insert booking data into the Booked_Guide table
    $query = "INSERT INTO Booked_Guide (Guide_ID, Booking_Date, Tourist_ID) 
              VALUES ('$guide_id', '$booking_date', '$tourist_id')";
    
    if ($conn->query($query)) {
        $message = "Guide booked successfully!";
        
        // Fetch guide details
        $guide_query = "SELECT Guide_Name, Phone_number, Year_of_Experience_ FROM Guide WHERE Guide_ID = '$guide_id'";
        $guide_result = $conn->query($guide_query);
        if ($guide_result && $guide_result->num_rows > 0) {
            $guide_details = $guide_result->fetch_assoc();
        }
    } else {
        $message = "Error: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Guide</title>
    <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: white;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

       

        .message {
            margin: 20px 0;
            padding: 20px;
            background:rgb(255, 255, 224);;
            border: 1px solid #bdc3c7;
            border-radius: 8px;
            text-align: center;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .message h2 {
            margin-bottom: 10px;
        }

        .message a {
            text-decoration: none;
            color: white;
            background-color: #2ecc71;
            padding: 10px 20px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .message a:hover {
            background-color: #27ae60;
        }

        .guide-details {
            margin: 20px 0;
            padding: 20px;
            background:rgb(255, 255, 224);;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .guide-details h3 {
            margin-bottom: 10px;
        }

        .booking-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .booking-form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .booking-form select, .booking-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 16px;
        }

        .booking-form input[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .booking-form input[type="submit"]:hover {
            background-color: #2980b9;
        }
        .navbar {
  background-color: rgba(255, 255, 255, 0.8);
  padding: 10px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: fixed;  /* Fixes the navbar at the top */
  top: 0;           /* Positions it at the top of the screen */
  left: 0;          /* Aligns the navbar to the left */
  width: 100%;      /* Makes the navbar span across the full width */
  z-index: 1000;    /* Ensures the navbar stays on top of other elements */
}

body {
  margin-top: 60px; /* Adjusts the body content to avoid being hidden behind the fixed navbar */
}

.nav-links {
  list-style: none;
  display: flex;
  margin: 0;
  padding: 0;
}

.nav-links li {
  margin: 0 10px;
}

.nav-links a {
  text-decoration: none;
  color: #007BFF;
  font-weight: bold;
}

.nav-links a:hover {
  color: #0056b3;
}

.logo {
  height: 40px;
}
    </style>
</head>
<body>
 
  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo-container">
      <img src="logo1.png" alt="Logo" class="logo">
    </div>
    <ul class="nav-links">
      <li><a href="dashboard.html">Home</a></li>
      <li><a href="transport_details.php">Transport</a></li>
      <li><a href="search_guide.php">Booking Guide</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="logout.php">Log Out</a></li>
    </ul>
  </nav>



<?php if (isset($message)) : ?>
    <div class="message">
        <h2><?php echo $message; ?></h2>
        <a href="search_guide.php">Back to Search</a>
    </div>
    <?php if (isset($guide_details)) : ?>
        <div class="guide-details">
            <h3>Guide Details</h3><br>
            <p><strong>Name:</strong> <?php echo $guide_details['Guide_Name']; ?></p><br>
            <p><strong>Contact:</strong> <?php echo $guide_details['Phone_number']; ?></p><br>
            <p><strong>Experience:</strong> <?php echo $guide_details['Year_of_Experience_']; ?> years</p><br>
            <p><strong>Booking Date:</strong> <?php echo $booking_date; ?></p>
        </div>
    <?php endif; ?>
<?php endif; ?>



</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
