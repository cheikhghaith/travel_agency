<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to log in first.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Prepare the query for retrieving payment history by joining with the bookings table
$query = "SELECT p.amount, p.payment_status, p.created_at AS payment_date
          FROM payments p
          JOIN bookings b ON p.booking_id = b.booking_id
          WHERE b.user_id = ?
          ORDER BY p.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id); // "i" stands for integer (user_id is assumed to be an integer)
$stmt->execute();
$payments_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <link rel="stylesheet" href="style.css?v=1.0">
</head>
<body>

<section class="header">
    <a href="home.php" class="logo"><span>Travel TN</span></a>
    <nav class="navbar">
        <a href="home.php">Home</a>
        <a href="book.php">Book</a>
        <a href="my_bookings.php">My Bookings</a>
        <a href="payment.php">Payments</a>
    </nav>
</section>

<section class="payments">
    <h1 class="heading-title">Your Payments</h1>

    <div class="payment-container">
        <?php if ($payments_result->num_rows > 0): ?>
            <?php while ($payment = $payments_result->fetch_assoc()) : ?>
                <div class="payment-box">
                    <p><strong>Amount:</strong> $<?= number_format($payment['amount'], 2) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($payment['payment_status']) ?></p>
                    <p><strong>Date:</strong> <?= date("F j, Y", strtotime($payment['payment_date'])) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No payment records found.</p>
        <?php endif; ?>
    </div>
</section>

</body>
</html>
