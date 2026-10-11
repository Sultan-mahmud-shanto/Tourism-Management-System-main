<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['tourist_id'])) {
    header("Location: index.html"); // Redirect to login page if not logged in
    exit();
}

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

// Fetch tourist details using the stored session ID
$tourist_id = $_SESSION['tourist_id'];
$sql = "SELECT Tourist_ID, Tourist_Name, Country, Gender_, Phone_Number, Age FROM Tourist WHERE Tourist_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tourist_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $tourist = $result->fetch_assoc();
} else {
    echo "<p>No tourist found.</p>";
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $country = $_POST['country'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];

    // Update the tourist's information in the database
    $update_sql = "UPDATE Tourist SET Tourist_Name = ?, Country = ?, Gender_ = ?, Phone_Number = ?, Age = ? WHERE Tourist_ID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssii", $name, $country, $gender, $phone, $age, $tourist_id);

    if ($update_stmt->execute()) {
        // Successfully updated, refresh the page
        header("Location: profile.php");
        exit();
    } else {
        echo "<p>Error updating profile: " . $conn->error . "</p>";
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
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        header { background-color: #34495e; color: white; padding: 20px; text-align: center; }
        /* Navbar styles */
        .navbar {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin-top: 80px;
            background: while;
            color: #fff;
        }

        .nav-links {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .nav-links li {
            margin: 0 15px;
        }

        .nav-links a {
            text-decoration: none;
            color: #007BFF;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #0056b3;
        }

        .logo {
            height: 40px;
        }

        .profile-container {
            max-width: 650px;
            margin: 100px auto;
            background: rgb(255, 255, 224);
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            color: #333;
        }

        .profile-header {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 1.5rem;
        }

        .profile-body {
            margin-top: 20px;
        }

        .profile-body p {
            font-size: 1.1rem;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        input[type="text"], input[type="number"] {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #ccc;
            width: 100%;
            transition: border 0.3s ease;
        }

        input[type="text"]:focus, input[type="number"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn-primary, .btn-secondary {
            border-radius: 10px;
            padding: 12px 30px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover, .btn-secondary:hover {
            background-color: #0056b3;
        }

        .logout-button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #dc3545;
            color: white;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .logout-button:hover {
            background-color: #c82333;
        }

        .edit-btn {
            background-color:rgb(188, 160, 34);
            color: white;
            padding: 10px 25px;
            border-radius: 10px;
            margin-top: 20px;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .edit-btn:hover {
            background-color: #ec971f;
        }
    </style>
</head>
<body>

<!-- Navbar -->
 <header>
    <h1 >Profile</h1>
</header>
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

<!-- Profile -->
<div class="profile-container">
    <div class="profile-header">
        <h3>Hello</h3>
        <h1> <?php echo htmlspecialchars($tourist['Tourist_Name']); ?></h1>
    </div>
    <div class="profile-body">
        <?php if (isset($_GET['edit']) && $_GET['edit'] == 'true'): ?>
            <!-- Edit Form -->
            <form action="profile.php" method="post">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($tourist['Tourist_Name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="country">Country:</label>
                    <input type="text" name="country" id="country" class="form-control" value="<?php echo htmlspecialchars($tourist['Country']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <input type="text" name="gender" id="gender" class="form-control" value="<?php echo htmlspecialchars($tourist['Gender_']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($tourist['Phone_Number']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="age">Age:</label>
                    <input type="number" name="age" id="age" class="form-control" value="<?php echo htmlspecialchars($tourist['Age']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        <?php else: ?>
            <!-- Profile Details -->
            <p><strong>Name:</strong> <?php echo htmlspecialchars($tourist['Tourist_Name']); ?></p>
            <p><strong>Country:</strong> <?php echo htmlspecialchars($tourist['Country']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($tourist['Gender_']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($tourist['Phone_Number']); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($tourist['Age']); ?></p>
            <a href="profile.php?edit=true" class="edit-btn">Edit Profile</a>
        <?php endif; ?>
    </div>
    <br><a href="logout.php" class="logout-button">Logout</a>
</div>

</body>
</html>
