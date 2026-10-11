<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$riverDetails = [];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form inputs
    $river = $_POST['river'] ?? null;
    $location = $_POST['location'] ?? null;
    $rating = $_POST['rating'] ?? null;

    // Database connection
    $host = 'localhost'; // Change if not using localhost
    $username = 'root';  // Your database username
    $password = '';      // Your database password
    $database = 'Tourism_Project'; // Your database name

    $conn = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Build the query dynamically based on provided inputs
    $query = "
        SELECT 
            River.R_Name, 
            Location.Location_name, 
            River.Rating, 
            River.Famous_place, 
            River.Famous_Food 
        FROM 
            River 
        INNER JOIN 
            Location 
        ON 
            River.R_Name = Location.R_Name
        WHERE 
            1=1
    ";

    $params = [];
    $types = "";

    if (!empty($river)) {
        $query .= " AND River.R_Name = ?";
        $params[] = $river;
        $types .= "s";
    }

    if (!empty($location)) {
        $query .= " AND Location.Location_name = ?";
        $params[] = $location;
        $types .= "s";
    }

    if (!empty($rating)) {
        $query .= " AND River.Rating = ?";
        $params[] = $rating;
        $types .= "i";
    }

    // Debugging: Print query to verify correctness
    // Uncomment the next line to debug the query during development
    // echo $query;

    $stmt = $conn->prepare($query);

    // Bind parameters dynamically
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch results if available
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $riverDetails[] = $row;
        }
    } else {
        $error = "No results found for the given criteria.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>River Details</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
  
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-image: url(p4.jpg);
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      min-height: 100vh;
    }
    .form-container {
      max-width: 600px;
      margin: 50px auto;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      background-color: rgba(255, 255, 255, 0.9);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .form-container h1 {
      text-align: center;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }
    th {
      background-color: #007BFF;
      color: white;
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


    /* Results container */
    .container {
      max-width: 1200px;
      margin: 50px auto;
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.7); /* Semi-transparent background */
      backdrop-filter: blur(10px); /* Apply blur effect */
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      z-index: 1;
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

  <!-- Form Container -->
  <div class="form-container">
    <h1>Search River Details</h1>
    <form method="POST" action="">
      <div class="form-group">
        <label for="river">River Name</label>
        <select id="river" name="river" class="form-control">
          <option value="" disabled selected>Select a River</option>   
          
            <option value="Padma River">Padma River</option>
            <option value="Jamuna River">Jamuna River</option>
            <option value="Meghna River">Meghna River</option>
            <option value="Ganges River">Ganges River</option>
            <option value="Karnaphuli River">Karnaphuli River</option>
            <option value="Surma River">Surma River</option>
            <option value="Madhumati River">Madhumati River</option>
            <option value="Chitra River">Chitra River</option>
            <option value="Kushiyara River">Kushiyara River</option>
            <option value="Teesta River">Teesta River</option>
            <option value="Ariyal Khan River">Ariyal Khan River</option>
            <option value="Buriganga River">Buriganga River</option>
            <option value="Turag River">Turag River</option>
            <option value="Shitalakshya River">Shitalakshya River</option>
            <option value="Dhaleshwari River">Dhaleshwari River</option>
            <option value="Matamuhuri River">Matamuhuri River</option>
            <option value="Feni River">Feni River</option>
            <option value="Gomti River">Gomti River</option>
            <option value="Ichamati River">Ichamati River</option>
            <option value="Atrai River">Atrai River</option> 
         </select>  


      </div>
      <div class="form-group">
        <label for="location">Location</label>
        <select id="location" name="location" class="form-control">
          <option value="" disabled selected>Select a Location</option>


            <option value="Kushtia">Kushtia</option>
            <option value="Kurigram">Kurigram</option>
            <option value="Bhola">Bhola</option>
            <option value="Bordia">Bordia</option>
            <option value="Chittagong">Chittagong</option>
            <option value="Sylhet">Sylhet</option>
            <option value="Narail">Narail</option>
            <option value="Rangpur">Rangpur</option>
            <option value="Madaripur">Madaripur</option>
            <option value="Dhaka">Dhaka</option>
            <option value="Narayanganj">Narayanganj</option>
            <option value="Feni">Feni</option>
            <option value="Comilla">Comilla</option>
            <option value="Khulna">Khulna</option>
            <option value="Naogaon">Naogaon</option>



        </select>
      </div>
      <div class="form-group">
        <label for="rating">Rating</label>
        <input type="number" id="rating" name="rating" class="form-control" placeholder="Enter rating (1-5)" min="1" max="5">
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Search</button>
      </div>
    </form>
  </div>

  <!-- Results Section -->
  <?php if (!empty($riverDetails)): ?>
    <div class="container">
      <h2>River Details</h2>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Location</th>
            <th>River Name</th> 
            <th>Rating</th>
            <th>Famous Place</th>
            <th>Famous Food</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($riverDetails as $detail): ?>
            <tr>
              <td><?= htmlspecialchars($detail['Location_name']) ?></td>
              <td><?= htmlspecialchars($detail['R_Name']) ?></td>
              <td><?= htmlspecialchars($detail['Rating']) ?></td>
              <td><?= htmlspecialchars($detail['Famous_place']) ?></td>
              <td><?= htmlspecialchars($detail['Famous_Food']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php elseif (isset($error)): ?>
    <div class="alert alert-danger text-center" style="max-width: 600px; margin: 20px auto;">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>
</body>
</html>
