<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Guides</title>
    <style>
        body { font-family: 'Arial', sans-serif; background: linear-gradient(to right,while); margin: 0; padding: 0; }
        header { background-color: #34495e; color: white; padding: 20px; text-align: center; }
        form { margin: 20px auto; padding: 20px; background: rgb(255, 255, 224); border-radius: 8px; width: 50%; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        form label { display: block; margin-bottom: 10px; font-weight: bold; }
        form input, form select, form button { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ccc; font-size: 16px; }
        form button { background-color: #2ecc71; color: white; border: none; cursor: pointer; }
        form button:hover { background-color: #27ae60; }

        .navbar {
      background-color: rgba(255, 255, 255, 0.8);
      padding: 10px 5px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      position: fixed; /* Fixes the navbar at the top */
      top: 0; /* Positions it at the top of the screen */
      left: 0; /* Aligns the navbar to the left */
      width: 100%; /* Makes the navbar span across the full width */
      z-index: 1000; /* Ensures the navbar stays on top of other elements */
    }

    body {
      margin-top: 60px; /* Adjusts the body content to avoid being hidden behind the fixed navbar */
    }

    .nav-links {
      list-style: none;
      display: flex;
      margin: 0;
      padding: 10px;
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
        .booked-info-btn {
            display: block;
            width: 50%;
            margin: 20px auto;
            padding: 10px;
            background-color: #3498db;
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 18px;
        }
        .booked-info-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

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
    <h1>Tourism Guide Search</h1>
</header>
<form method="POST" action="search_result.php">
    <h2>Find Your Guide</h2>
    <label for="gender">Gender:</label>
    <select name="gender">
        <option value="">Any</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>

    <label for="experience">Minimum Years of Experience:</label>
    <input type="number" name="experience" min="0">

    <button type="submit">Search</button>
</form>

<a href="booked_info.php" class="booked-info-btn">View My Booked Guides</a>

</body>
</html>
