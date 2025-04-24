<?php
include 'config.php';
session_start();

if (!isset($_GET['id'])) {
    echo "Trip not found.";
    exit;
}

// Sanitize and validate the trip_id to prevent SQL injection
$trip_id = intval($_GET['id']); // Ensuring it's an integer

// Prepare the query to prevent SQL injection
$query = $conn->prepare("SELECT * FROM trips WHERE trip_id = ?");
$query->bind_param("i", $trip_id);  // "i" denotes an integer parameter
$query->execute();
$result = $query->get_result();

if (mysqli_num_rows($result) == 0) {
    echo "Trip not found.";
    exit;
}

$trip = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($trip['name']); ?> - Trip Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="trip-details">
    <h1><?php echo htmlspecialchars($trip['name']); ?></h1>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($trip['location']); ?></p>
    <p><strong>Duration:</strong> <?php echo htmlspecialchars($trip['duration']); ?></p>
    <p><strong>Price:</strong> $<?php echo number_format($trip['price'], 2); ?></p>
    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($trip['description'])); ?></p>
    <p><strong>Availability:</strong> <?php echo ($trip['availability'] > 0) ? 'Available' : 'Not Available'; ?></p>

    <?php if ($trip['availability'] > 0): ?>

        <a href="book.php?trip_id=<?php echo $trip_id; ?>" class="btn">Book Now</a>
    <?php else: ?>
        <p class="text-danger">This trip is currently unavailable.</p>
    <?php endif; ?>
</div>

</body>
</html>
