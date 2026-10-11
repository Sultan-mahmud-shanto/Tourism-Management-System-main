<?php
// Start the session securely
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

// Check if the guide is logged in
if (!isset($_SESSION['guide_id'])) {
    die("Access Denied. Please log in as a guide.");
}

$guide_id = $_SESSION['guide_id']; // Get the logged-in guide's ID

// Initialize success and error message variables
$success_message = '';
$error_message = '';

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = intval($_POST['booking_id']); // Sanitize input
    if ($booking_id > 0) {
        $delete_sql = "DELETE FROM Booked_Guide WHERE Booking_ID = ? AND Guide_ID = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("ii", $booking_id, $guide_id);
        if ($delete_stmt->execute()) {
            $success_message = "Booking canceled successfully!";
        } else {
            $error_message = "Failed to cancel the booking.";
        }
    }
}

// Handle booking confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    $booking_id = intval($_POST['booking_id']); // Sanitize input
    if ($booking_id > 0) {
        $update_sql = "UPDATE Booked_Guide SET Booking_Status = 'Confirmed' WHERE Booking_ID = ? AND Guide_ID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $booking_id, $guide_id);
        if ($update_stmt->execute()) {
            $success_message = "Booking confirmed successfully!";
        } else {
            $error_message = "Failed to confirm the booking.";
        }
    }
}

// Query to get booking details along with tourist information
$sql = "SELECT 
            bg.Booking_ID, 
            bg.Booking_Date, 
            bg.Booking_Status,
            t.Tourist_ID, 
            t.Tourist_Name, 
            t.Country, 
            t.Gender_, 
            t.Phone_Number, 
            t.Age 
        FROM Booked_Guide bg
        JOIN Tourist t ON bg.Tourist_ID = t.Tourist_ID
        WHERE bg.Guide_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourists Who Booked Me</title>
    <link rel="stylesheet" href="style/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url(Guide.webp);
            background-size: cover; /* Make sure the background covers the entire page */
            background-position: center;
            text-align: center;
            padding: 0;
            margin: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 80vh; /* Ensures container takes enough space for the content */
        }

        h2 {
            color: #fff;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background: white;
            border-radius: 10px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #ff5733;
            color: white;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .cancel-btn, .confirm-btn, .logout-btn {
            padding: 8px 15px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .cancel-btn {
            background: #ff5733;
        }

        .cancel-btn:hover {
            background: #c70039;
        }

        .confirm-btn {
            background: #28a745; /* Green for confirmation */
        }

        .confirm-btn:hover {
            background: #218838;
        }

        .logout-btn {
            background: #c70039;
        }

        .logout-btn:hover {
            background: #b2002d;
        }

        .logout-container {
            margin-top: auto; /* Push logout to the bottom of the container */
            text-align: center;
            width: 100%;
        }

        button {
            user-select: none;
        }

        /* Responsive design for smaller screens */
        @media (max-width: 600px) {
            .container {
                padding: 15px;
                width: 100%;
                max-width: none;
                margin: 10px;
            }

            .button-container {
                flex-direction: column;
                align-items: stretch;
            }

            .cancel-btn, .confirm-btn, .logout-btn {
                width: 100%;
                margin: 5px 0;
            }

            .logout-container {
                margin-top: 20px; /* Add some space before logout button */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tourists Who Booked Me</h2>

        <!-- Display success or error messages -->
        <?php if ($success_message): ?>
            <p style="color: green;"><?= htmlspecialchars($success_message) ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <table>
            <tr>
                <th>Booking Date</th>
                <th>Tourist Name</th>
                <th>Country</th>
                <th>Gender</th>
                <th>Phone Number</th>
                <th>Age</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Booking_Date']) ?></td>
                    <td><?= htmlspecialchars($row['Tourist_Name']) ?></td>
                    <td><?= htmlspecialchars($row['Country']) ?></td>
                    <td><?= htmlspecialchars($row['Gender_']) ?></td>
                    <td><?= htmlspecialchars($row['Phone_Number']) ?></td>
                    <td><?= htmlspecialchars($row['Age']) ?></td>
                    <td><?= htmlspecialchars($row['Booking_Status']) ?></td>
                    <td>
                        <div class="button-container">
                            <?php if ($row['Booking_Status'] !== 'Confirmed'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="booking_id" value="<?= $row['Booking_ID'] ?>">
                                    <button type="submit" name="confirm_booking" class="confirm-btn">Confirm</button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" action="">
                                <input type="hidden" name="booking_id" value="<?= $row['Booking_ID'] ?>">
                                <button type="submit" name="cancel_booking" class="cancel-btn">Cancel</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Logout Button at the end of the content -->
        <div class="logout-container">
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
