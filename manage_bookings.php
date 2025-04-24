<?php
include 'config.php';
session_start();

// Ensure the user is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Access Denied. Admins only.'); window.location.href='login.php';</script>";
    exit;
}

// Update booking status
if (isset($_POST['update_status'])) {
    $id = intval($_POST['booking_id']); // Ensure ID is an integer
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_bookings.php"); // Redirect to avoid re-submission
    exit;
}

// Mark payment as paid
if (isset($_POST['mark_paid'])) {
    $id = intval($_POST['booking_id']); // Ensure ID is an integer

    // Update payment status in payments and bookings tables
    $stmt1 = $conn->prepare("UPDATE payments SET payment_status = 'Paid' WHERE booking_id = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $stmt1->close();

    $stmt2 = $conn->prepare("UPDATE bookings SET payment_status = 'Paid' WHERE booking_id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->close();

    header("Location: manage_bookings.php"); // Redirect to avoid re-submission
    exit;
}

// Delete booking
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // Ensure ID is an integer

    // Delete the booking from the database
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_bookings.php"); // Redirect to avoid re-submission
    exit;
}

// Fetch bookings data
$query = "SELECT b.booking_id, b.guests, b.arrival_date, b.leaving_date, b.status, b.payment_status, u.name AS user_name, t.location
          FROM bookings b
          JOIN users u ON b.user_id = u.user_id
          JOIN trips t ON b.trip_id = t.trip_id
          ORDER BY b.created_at DESC";
$bookings = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Bookings</title>
  <link rel="stylesheet" href="style.css?v=1.0">
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; font-size: 14px; }
    th { background-color: #f0f0f0; }
    .form-inline { display: flex; gap: 5px; justify-content: center; align-items: center; }
    .btn-small { padding: 4px 10px; font-size: 13px; }
    .btn-del { color: red; text-decoration: none; }
  </style>
</head>
<body>
  <section class="dashboard">
    <h1>Manage Bookings</h1>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Destination</th>
          <th>Guests</th>
          <th>Dates</th>
          <th>Status</th>
          <th>Payment</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($bookings)) { ?>
          <tr>
            <td><?= $row['booking_id'] ?></td>
            <td><?= $row['user_name'] ?></td>
            <td><?= $row['location'] ?></td>
            <td><?= $row['guests'] ?></td>
            <td><?= $row['arrival_date'] ?> → <?= $row['leaving_date'] ?></td>
            <td>
              <form method="post" class="form-inline">
                <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                <select name="status">
                  <option <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                  <option <?= $row['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                  <option <?= $row['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
                <input type="submit" name="update_status" value="Update" class="btn-small">
              </form>
            </td>
            <td>
              <?= $row['payment_status'] ?>
              <?php if ($row['payment_status'] !== 'Paid') { ?>
                <form method="post" style="margin-top: 5px;">
                  <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                  <input type="submit" name="mark_paid" value="Mark Paid" class="btn-small">
                </form>
              <?php } ?>
            </td>
            <td>
              <a href="?delete=<?= $row['booking_id'] ?>" class="btn-del" onclick="return confirm('Delete this booking?')">Delete</a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <br>
    <a href="admin_dashboard.php" class="btn">← Back to Dashboard</a>
  </section>
</body>
</html>
