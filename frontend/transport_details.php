<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Tourism_Project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handling search and filter inputs
$startFrom = isset($_GET['startFrom']) ? $_GET['startFrom'] : '';
$destination = isset($_GET['destination']) ? $_GET['destination'] : '';
$acNonAC = isset($_GET['acNonAC']) ? $_GET['acNonAC'] : '';
$routeType = isset($_GET['routeType']) ? $_GET['routeType'] : '';
$minFare = isset($_GET['minFare']) ? (int)$_GET['minFare'] : '';
$maxFare = isset($_GET['maxFare']) ? (int)$_GET['maxFare'] : '';
$rating = isset($_GET['rating']) ? (float)$_GET['rating'] : '';

// Constructing the query dynamically
$query = "SELECT * FROM Transport WHERE 1=1";
if ($startFrom) {
    $query .= " AND StartFrom = '" . $conn->real_escape_string($startFrom) . "'";
}
if ($destination) {
    $query .= " AND Destination = '" . $conn->real_escape_string($destination) . "'";
}
if ($acNonAC) {
    $query .= " AND AC_Non_AC = '" . $conn->real_escape_string($acNonAC) . "'";
}
if ($routeType) {
    $query .= " AND Route_Type = '" . $conn->real_escape_string($routeType) . "'";
}
if ($minFare) {
    $query .= " AND Fare >= $minFare";
}
if ($maxFare) {
    $query .= " AND Fare <= $maxFare";
}
if ($rating) {
    $query .= " AND Rating >= $rating";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Search</title>
    <style>
        header { background-color: #34495e; color: white; padding: 20px; text-align: center; }
        
        body {
            font-family: Arial, sans-serif;
            background-color: while;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: rgb(255, 255, 224);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: green;
        }
        form {
            margin-bottom: 20px;
        }
        form div {
            margin-bottom: 10px;
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input, form select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: green;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: darkgreen;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table th {
            background-color: green;
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
h1 {
  color: white;
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
    <h1 >Transport Search</h1>
</header>
    <div class="container">
        
        <form method="GET">
            <div>
                <label for="startFrom">Start From:</label>
                <select id="startFrom" name="startFrom">
                    <option value="">--Select--</option>
                    <?php
                    $startFromOptions = ["Dhaka", "Jessore", "Khulna", "Chittagong", "Sylhet"];
                    foreach ($startFromOptions as $option) {
                        $selected = $startFrom === $option ? 'selected' : '';
                        echo "<option value='$option' $selected>$option</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="destination">Destination:</label>
                <select id="destination" name="destination">
                    <option value="">--Select--</option>
                    <?php
                    $destinationOptions = ["Narail", "Dhaka", "Cox_Bazar", "Jessore Airport", "Shah Makhdum Airport", "Saidpur Airport", "Cox's Bazar Airport", "Barisal Airport", "Rajshahi Airport", "Tejgaon Airport", "Chattogram", "Dinajpur", "Rajshahi", "Kurigram", "Panchagarh", "Lalmonirhat", "Sylhet", "Barishal", "Rangpur", "Khulna"];
                    foreach ($destinationOptions as $option) {
                        $selected = $destination === $option ? 'selected' : '';
                        echo "<option value='$option' $selected>$option</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="acNonAC">AC/Non-AC:</label>
                <select id="acNonAC" name="acNonAC">
                    <option value="">--Select--</option>
                    <option value="AC" <?php echo $acNonAC === 'AC' ? 'selected' : ''; ?>>AC</option>
                    <option value="Non-AC" <?php echo $acNonAC === 'Non-AC' ? 'selected' : ''; ?>>Non-AC</option>
                </select>
            </div>
            <div>
                <label for="routeType">Route Type:</label>
                <select id="routeType" name="routeType">
                    <option value="">--Select--</option>
                    <option value="Bus" <?php echo $routeType === 'Bus' ? 'selected' : ''; ?>>Bus</option>
                    <option value="Train" <?php echo $routeType === 'Train' ? 'selected' : ''; ?>>Train</option>
                    <option value="Flight" <?php echo $routeType === 'Flight' ? 'selected' : ''; ?>>Flight</option>
                </select>
            </div>
            <div>
                <label for="minFare">Min Fare:</label>
                <input type="number" id="minFare" name="minFare" value="<?php echo htmlspecialchars($minFare); ?>">
            </div>
            <div>
                <label for="maxFare">Max Fare:</label>
                <input type="number" id="maxFare" name="maxFare" value="<?php echo htmlspecialchars($maxFare); ?>">
            </div>
            <div>
                <label for="rating">Minimum Rating:</label>
                <input type="number" step="0.1" id="rating" name="rating" value="<?php echo htmlspecialchars($rating); ?>">
            </div>
            <button type="submit">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Operator</th>
                    <th>AC/Non-AC</th>
                    <th>Start From</th>
                    <th>Destination</th>
                    <th>Fare</th>
                    <th>Rating</th>
                    <th>Time Schedule</th>
                    <th>Route Type</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['ID']; ?></td>
                            <td><?php echo htmlspecialchars($row['Operator_Name']); ?></td>
                            <td><?php echo htmlspecialchars($row['AC_Non_AC']); ?></td>
                            <td><?php echo htmlspecialchars($row['StartFrom']); ?></td>
                            <td><?php echo htmlspecialchars($row['Destination']); ?></td>
                            <td><?php echo htmlspecialchars($row['Fare']); ?></td>
                            <td><?php echo htmlspecialchars($row['Rating']); ?></td>
                            <td><?php echo htmlspecialchars($row['Time_Schedule']); ?></td>
                            <td><?php echo htmlspecialchars($row['Route_Type']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No results found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
