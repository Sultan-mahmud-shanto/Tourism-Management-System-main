<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Tourism_Project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the tourist is logged in
if (!isset($_SESSION['tourist_id'])) {
    die("Access Denied. Please log in as a tourist.");
}

$tourist_id = $_SESSION['tourist_id']; // Get the logged-in tourist's ID

// Handle booking cancellation
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    $delete_sql = "DELETE FROM Booked_Guide WHERE Booking_ID = ? AND Tourist_ID = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("ii", $booking_id, $tourist_id);
    $stmt->execute();
    header("Location: booked_info.php"); // Refresh the page after cancellation
    exit();
}

// Query to get booking details along with guide information
$sql = "SELECT 
            bg.Booking_ID, 
            bg.Booking_Date, 
            bg.Booking_Status,
            g.Guide_ID, 
            g.Guide_Name, 
            g.Phone_Number, 
            g.Year_of_Experience_ 
        FROM Booked_Guide bg
        JOIN Guide g ON bg.Guide_ID = g.Guide_ID
        WHERE bg.Tourist_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tourist_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booked Guides</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: while);
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #34495e;
            color: white;
            padding: 20px;
            text-align: center;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: rgb(255, 255, 224);;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2ecc71;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .cancel-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .cancel-btn:hover {
            background-color: #c0392b;
        }
        .navbar {
  background-color: rgba(255, 255, 255, 0.8);
  padding: 10px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
}

body {
  margin-top: 60px;
}

.nav-links {
  list-style: none;
  display: flex;
  margin: 0;
  padding: 20px;
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
  height: 50px;
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
<header>
    <h1>My Booked Guides</h1>
</header>

<table>
    <tr>
        <th>Guide Name</th>
        <th>Phone Number</th>
        <th>Booking Date</th>
        <th>Years of Experience</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['Guide_Name']); ?></td>
            <td><?php echo htmlspecialchars($row['Phone_Number']); ?></td>
            <td><?php echo htmlspecialchars($row['Booking_Date']); ?></td>
            <td><?php echo htmlspecialchars($row['Year_of_Experience_']); ?> years</td>
            <td>
                <?php 
                    // Display booking status
                    if ($row['Booking_Status'] == 'Confirmed') {
                        echo '<span style="color: green;">Confirmed</span>';
                    } else {
                        echo '<span style="color: orange;">Pending</span>';
                    }
                ?>
            </td>
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="booking_id" value="<?php echo $row['Booking_ID']; ?>">
                    <button type="submit" name="cancel_booking" class="cancel-btn">Cancel</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
