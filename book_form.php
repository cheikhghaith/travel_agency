<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

// Check if form is submitted
if (isset($_POST['submit_booking'])) {
    // Sanitize form data
    $trip_id = intval($_POST['trip_id']); // Ensure it's an integer
    $user_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $guests = intval($_POST['guests']); // Ensure it's an integer
    $arrival_date = mysqli_real_escape_string($conn, $_POST['arrival_date']);
    $leaving_date = mysqli_real_escape_string($conn, $_POST['leaving_date']);

    // Validate form fields
    if (empty($name) || empty($email) || empty($address) || empty($guests) || empty($arrival_date) || empty($leaving_date)) {
        echo "<h2 style='color:red;'>All fields are required.</h2>";
    } else {
        // Prepare the SQL query with placeholders
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, trip_id, name, email, address, guests, arrival_date, leaving_date)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssiss", $user_id, $trip_id, $name, $email, $address, $guests, $arrival_date, $leaving_date);

        // Execute the query and check for success
        if ($stmt->execute()) {
            // Redirect to confirmation page or bookings page after success
            header("Location: my_bookings.php");
            exit;
        } else {
            echo "<h2 style='color:red;'>Error: " . $stmt->error . "</h2>";
        }

        // Close the statement
        $stmt->close();
    }
}
?>
