<?php
include 'config.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Access Denied. Admins only.'); window.location.href='login.php';</script>";
    exit;
}

// Check if payment ID and status are passed via GET
if (isset($_GET['id']) && isset($_GET['status'])) {
    $payment_id = intval($_GET['id']);  // Ensure the ID is an integer
    $status = mysqli_real_escape_string($conn, $_GET['status']); // Escape the status

    // Update the payment status in the database using prepared statement
    $query = "UPDATE payments SET payment_status = ? WHERE payment_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        // Bind parameters and execute
        $stmt->bind_param("si", $status, $payment_id); // "s" for string (status), "i" for integer (payment_id)
        if ($stmt->execute()) {
            echo "<script>alert('Payment status updated successfully.'); window.location.href='admin_payments.php';</script>";
        } else {
            echo "<script>alert('Error updating payment status.'); window.location.href='admin_payments.php';</script>";
        }
        $stmt->close();  // Close the prepared statement
    } else {
        echo "<script>alert('Error preparing the query.'); window.location.href='admin_payments.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href='admin_payments.php';</script>";
}
?>
