<?php
$conn = new mysqli("localhost", "root", "", "Tourism_Project");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <style>
        body { font-family: 'Arial', sans-serif; background: while); margin: 0; padding: 0; }
        header { background-color: #34495e; color: white; padding: 20px; text-align: center; }
        table { margin: 20px auto; width: 80%; border-collapse: collapse; background: rgb(255, 255, 224);; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        table th, table td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        table th { background-color: #34495e; color: white; }
        table tr:nth-child(even) { background-color: #f2f2f2; }
        form button { padding: 5px 10px; background-color: #2ecc71; color: white; border: none; cursor: pointer; border-radius: 4px; }
        form button:hover { background-color: #27ae60; }

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
    <h1>Search Results</h1>
</header>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gender = $_POST['gender'];
    $experience = $_POST['experience'];

    $query = "SELECT * FROM Guide WHERE 1=1";
    if (!empty($gender)) $query .= " AND Gender = '$gender'";
    if (!empty($experience)) $query .= " AND Year_of_Experience_ >= $experience";

    $result = $conn->query($query);

    echo "<table>";
    echo "<tr><th>Guide ID</th><th>Name</th><th>Gender</th><th>Experience</th><th>Action</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['Guide_ID']}</td>
            <td>{$row['Guide_Name']}</td>
            <td>{$row['Gender']}</td>
            <td>{$row['Year_of_Experience_']}</td>
            <td>
                <form method='POST' action='book_guide.php'>
                    <input type='hidden' name='Guide_ID' value='{$row['Guide_ID']}'>
                    <input type='date' name='Booking_Date' required>
                    <button type='submit'>Book</button>
                </form>
            </td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No search performed!</p>";
}
?>
</body>
</html>
