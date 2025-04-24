<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Access Denied. Admins only.'); window.location.href='login.php';</script>";
    exit;
}

if (isset($_POST['add_trip'])) {
    $location    = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price       = $_POST['price'];
    $duration    = $_POST['duration'];

    $query = "INSERT INTO trips (location, description, price, duration, created_at)
              VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdi", $location, $description, $price, $duration);
    $stmt->execute();
    header("Location: manage_trips.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM trips WHERE trip_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_trips.php");
    exit;
}

$edit_mode = false;
$edit_trip = null;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM trips WHERE trip_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_trip = $result->fetch_assoc();
}

if (isset($_POST['update_trip'])) {
    $trip_id    = intval($_POST['trip_id']);
    $location   = mysqli_real_escape_string($conn, $_POST['location']);
    $description= mysqli_real_escape_string($conn, $_POST['description']);
    $price      = $_POST['price'];
    $duration   = $_POST['duration'];

    $stmt = $conn->prepare("UPDATE trips SET location = ?, description = ?, price = ?, duration = ? WHERE trip_id = ?");
    $stmt->bind_param("ssdii", $location, $description, $price, $duration, $trip_id);
    $stmt->execute();
    header("Location: manage_trips.php");
    exit;
}

$trips = mysqli_query($conn, "SELECT * FROM trips ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Trips</title>
  <link rel="stylesheet" href="style.css?v=1.0">
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    th { background-color: #f0f0f0; }
    .form { margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 10px; }
    .form input, .form textarea { width: 100%; padding: 10px; margin-bottom: 10px; }
    .btn-del { color: red; text-decoration: none; }
    .btn-edit { color: #007bff; text-decoration: none; }
  </style>
</head>
<body>
  <section class="dashboard">
    <h1>Manage Trips</h1>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Location</th>
          <th>Description</th>
          <th>Price ($)</th>
          <th>Duration</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($trips)) { ?>
          <tr>
            <td><?= $row['trip_id'] ?></td>
            <td><?= $row['location'] ?></td>
            <td><?= $row['description'] ?></td>
            <td><?= $row['price'] ?></td>
            <td><?= $row['duration'] ?> days</td>
            <td>
              <a href="?edit=<?= $row['trip_id'] ?>" class="btn-edit">Edit</a> |
              <a href="?delete=<?= $row['trip_id'] ?>" class="btn-del" onclick="return confirm('Delete this trip?')">Delete</a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <div class="form">
      <h2><?= $edit_mode ? "Edit Trip" : "Add New Trip" ?></h2>
      <form method="post">
        <input type="text" name="location" placeholder="Location" value="<?= $edit_mode ? htmlspecialchars($edit_trip['location']) : '' ?>" required>
        <textarea name="description" placeholder="Description" required><?= $edit_mode ? htmlspecialchars($edit_trip['description']) : '' ?></textarea>
        <input type="number" name="price" placeholder="Price" value="<?= $edit_mode ? $edit_trip['price'] : '' ?>" step="0.01" required>
        <input type="number" name="duration" placeholder="Duration (in days)" value="<?= $edit_mode ? $edit_trip['duration'] : '' ?>" required>

        <?php if ($edit_mode): ?>
          <input type="hidden" name="trip_id" value="<?= $edit_trip['trip_id'] ?>">
          <input type="submit" name="update_trip" value="Update Trip" class="btn">
        <?php else: ?>
          <input type="submit" name="add_trip" value="Add Trip" class="btn">
        <?php endif; ?>
      </form>
    </div>

    <br>
    <a href="admin_dashboard.php" class="btn">← Back to Dashboard</a>
  </section>
</body>
</html>
