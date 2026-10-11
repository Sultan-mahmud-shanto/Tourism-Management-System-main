<?php
// Set content type to HTML to ensure browser renders it correctly
header('Content-Type: text/html; charset=utf-8');

// Check if the request method is POST. If not, redirect to the form.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Get and sanitize data from the HTML form
    $budget = htmlspecialchars($_POST['budget']);
    $days = htmlspecialchars($_POST['days']);
    $place_type = htmlspecialchars($_POST['place_type']);
    $from_location = htmlspecialchars($_POST['from_location']);

    // 2. Define your AI prompt based on user input
    // Craft the prompt carefully to guide the AI to return the desired JSON format.
    $prompt = "Generate a detailed tour plan for a trip with a budget of " . $budget . " BDT, lasting " . $days . " days, preferring " . $place_type . " type places, starting from " . $from_location . ".
    The plan should be returned ONLY as a JSON object, with the following structure:
    {
        \"status\": \"success\" or \"error\",
        \"message\": \"Optional error message if status is error\",
        \"parameters\": {
            \"budget\": \"[input_budget]\",
            \"days\": \"[input_days]\",
            \"place_type\": \"[input_place_type]\",
            \"from_location\": \"[input_from_location]\"
        },
        \"remaining_budget\": \"[estimated_remaining_budget_in_BDT]\",
        \"itinerary\": [
            {
                \"day\": \"Day X\",
                \"end_day\": \"Day Y\",
                \"location\": \"City/Area Name\",
                \"attraction\": \"Main attraction/activity\",
                \"description\": \"Brief description\",
                \"estimated_cost_for_this_segment\": \"[cost_in_BDT]\",
                \"days_spent_here\": \"[number_of_days]\",
                \"estimated_travel_hours_from_dhaka\": \"[hours_from_dhaka]\",
                \"note\": \"Any special notes\"
            },
            // ... more itinerary items
        ]
    }
    Ensure the JSON is valid and complete, enclosed in ```json ... ``` markdown block.
    If you cannot generate a plan, set status to 'error' and provide a message.
    Strictly adhere to the JSON structure provided. Only output the JSON.";


    // 3. Prepare data for the API request
    $api_key = 'AIzaSyBi-ymy831D13C_mpTCM-pzNN4pyotT5W8'; // <--- REPLACE THIS WITH YOUR ACTUAL GOOGLE GEMINI API KEY!
    $api_endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $api_key;
    $request_body = json_encode([
        'contents' => [
            ['parts' => [['text' => $prompt]]]
        ],
        'generationConfig' => [
            'temperature' => 0.7, // Adjust for creativity (0.0 - 1.0)
            'topP' => 0.9,
            'topK' => 40,
            'maxOutputTokens' => 1000 // Increase if plans are cut short
        ]
    ]);

    // 4. Make the API call using cURL
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api_endpoint);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $request_body);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($request_body) // Required for POST
    ]);

    $api_response_raw = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl);
    curl_close($curl);

    // Initialize variables for error handling
    $tour_plan = null;
    $display_error_message = ''; // This will hold user-friendly error messages

    // 5. Process the API response
    if ($api_response_raw === false) {
        $display_error_message = "Network Error: Could not connect to the AI service. cURL Error: " . $curl_error;
    } elseif ($http_status != 200) {
        // API returned an HTTP error (e.g., 400 Bad Request, 401 Unauthorized, 500 Server Error)
        $display_error_message = "AI API Request Failed (Status " . $http_status . "). Raw API Response: <pre>" . htmlspecialchars($api_response_raw) . "</pre>";
    } else {
        // API call was successful (HTTP 200 OK)
        $api_response_data = json_decode($api_response_raw, true);

        // Check if the AI response contains the expected content structure
        if (isset($api_response_data['candidates'][0]['content']['parts'][0]['text'])) {
            $ai_raw_text = $api_response_data['candidates'][0]['content']['parts'][0]['text'];

            // The AI often returns markdown-formatted JSON (e.g., ```json { ... } ```).
            // We need to extract just the JSON string.
            preg_match('/```json(.*?)```/s', $ai_raw_text, $matches); // Regex to find content inside ```json ... ```

            $json_string_to_decode = '';
            if (isset($matches[1])) {
                $json_string_to_decode = trim($matches[1]); // Use extracted JSON if found
            } else {
                $json_string_to_decode = trim($ai_raw_text); // Fallback: use entire text if no markdown block
            }

            $tour_plan = json_decode($json_string_to_decode, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // If JSON decoding failed, it means the AI didn't return valid JSON
                $display_error_message = "AI returned invalid JSON. Raw AI output:<pre>" . htmlspecialchars($ai_raw_text) . "</pre>";
            }
        } else {
            // AI response structure was not as expected (e.g., no 'candidates' or 'parts')
            $display_error_message = "AI response format unexpected. Raw API response: <pre>" . htmlspecialchars($api_response_raw) . "</pre>";
        }
    }

    // --- START HTML OUTPUT ---
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Tour Plan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Specific styles for the tour plan display page, embedded for simplicity */
        body {
            font-family: Arial, sans-serif;
            background: white; /* Matches your form\'s body background */
            color: #333;
            line-height: 1.6;
            padding: 20px;
            /* Adjust margin-top to account for the fixed navbar */
            margin-top: 60px;
        }
        .container {
            max-width: 800px;
            margin: 20px auto; /* Centered, with margin from top */
            background: rgb(255, 255, 224); /* Matches your form container background */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        h1, h2 {
            color: green; /* Matches your form\'s H1 color */
            text-align: center;
            margin-bottom: 20px;
        }
        .error-message {
            color: #d9534f; /* Red for errors */
            background-color: #f2dede; /* Light red background */
            border: 1px solid #ebccd1; /* Red border */
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            white-space: pre-wrap; /* Preserve whitespace and line breaks for tracebacks */
            word-wrap: break-word; /* Break long words */
            text-align: left; /* Align error message content left for readability */
        }
        .itinerary-item {
            background-color: #f9f9f9;
            border-left: 5px solid #20b56a; /* Green left border highlight */
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Subtle shadow */
        }
        .itinerary-item h3 {
            color: #224dc2; /* Blue color for day headers */
            margin-bottom: 5px;
        }
        .itinerary-item p {
            margin-bottom: 5px;
        }
        .itinerary-item strong {
            color: #000; /* Black for labels */
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #224dc2; /* Blue button color */
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s ease;
        }
        .back-button:hover {
            background: #20b56a; /* Green on hover */
        }
        /* Navbar styles (copied from your original HTML for consistency) */
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

    <div class="container">
        <h1>Your AI-Generated Tour Plan</h1>';

    // 6. Conditional display based on API response
    if (!empty($display_error_message)) {
        echo '<div class="error-message">Error: ' . $display_error_message . '</div>';
    } elseif ($tour_plan === null || !is_array($tour_plan) || !isset($tour_plan['status'])) {
        // Fallback for unexpected JSON structure even if basic decoding was attempted
        echo '<div class="error-message">Error processing tour plan. Received unexpected or malformed data from AI.</div>';
    } elseif ($tour_plan['status'] == 'error') {
        // This block handles structured error messages returned by the AI itself (if AI sets status to error)
        echo '<div class="error-message">Error generating plan: ' . htmlspecialchars($tour_plan['message']) . '</div>';
    } else {
        // This block displays the successfully generated tour plan
        echo '<h2>Based on your preferences:</h2>';
        echo '<p><strong>Budget:</strong> ' . htmlspecialchars($tour_plan['parameters']['budget']) . ' BDT</p>';
        echo '<p><strong>Days:</strong> ' . htmlspecialchars($tour_plan['parameters']['days']) . ' days</p>';
        echo '<p><strong>Preferred Place Type:</strong> ' . htmlspecialchars($tour_plan['parameters']['place_type']) . '</p>';
        echo '<p><strong>Starting From:</strong> ' . htmlspecialchars($tour_plan['parameters']['from_location']) . '</p>';

        // Display remaining budget if provided by the AI
        if (isset($tour_plan['remaining_budget'])) {
             echo '<p><strong>Estimated Remaining Budget:</strong> ' . htmlspecialchars($tour_plan['remaining_budget']) . ' BDT</p>';
        }

        echo '<h2>Itinerary:</h2>';
        // Check if itinerary array is not empty and is an array before looping
        if (!empty($tour_plan['itinerary']) && is_array($tour_plan['itinerary'])) {
            foreach ($tour_plan['itinerary'] as $item) {
                echo '<div class="itinerary-item">';
                echo '<h3>' . htmlspecialchars($item['day']) . ' to ' . htmlspecialchars($item['end_day']) . '</h3>';
                echo '<p><strong>Location:</strong> ' . htmlspecialchars($item['location']) . '</p>';
                echo '<p><strong>Attraction:</strong> ' . htmlspecialchars($item['attraction']) . '</p>';
                echo '<p><strong>Description:</strong> ' . htmlspecialchars($item['description']) . '</p>';
                echo '<p><strong>Estimated Cost for Segment:</strong> ' . htmlspecialchars($item['estimated_cost_for_this_segment']) . ' BDT</p>';
                echo '<p><strong>Days Spent Here:</strong> ' . htmlspecialchars($item['days_spent_here']) . '</p>';
                // Conditionally display new fields if they exist
                if (isset($item['estimated_travel_hours_from_dhaka'])) {
                    echo '<p><strong>Estimated Travel Hours (from Dhaka):</strong> ' . htmlspecialchars($item['estimated_travel_hours_from_dhaka']) . ' hours</p>';
                }
                if (isset($item['note'])) {
                    echo '<p><em>Note:</em> ' . htmlspecialchars($item['note']) . '</p>';
                }
                echo '</div>';
            }
        } else {
            echo '<p>No specific itinerary items could be generated with the current parameters, or the AI could not generate a suitable plan in the expected format. Please try adjusting your budget, days, or place type.</p>';
        }
    }
    // Button to go back to the tour planning form
    echo '<a href="tour_form.html" class="back-button">Plan Another Tour</a>';
    echo '</div>
    </body>
    </html>';

   } else {
    // If the script is accessed directly without POST data, redirect back to the main form
    header('Location: index.html');
    exit(); // Terminate script execution
   }
?>