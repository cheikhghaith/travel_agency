<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Access Denied. Admins only.'); window.location.href='login.php';</script>";
    exit;
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE id = $id AND role = 'User'");
    if (mysqli_num_rows($check) === 1) {
        $delete_query = "DELETE FROM users WHERE id = $id";
        $delete_result = mysqli_query($conn, $delete_query);

        if ($delete_result) {
            echo "<script>alert('User deleted successfully!'); window.location.href='manage_users.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error deleting user. Please try again.'); window.location.href='manage_users.php';</script>";
        }
    } else {
        echo "<script>alert('User not found or invalid user role.'); window.location.href='manage_users.php';</script>";
    }
}

$users = mysqli_query($conn, "SELECT * FROM users WHERE role = 'User'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link rel="stylesheet" href="style.css?v=1.0">
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: center;
    }
    th {
      background-color: #f4f4f4;
    }
    a.btn-del {
      color: red;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <section class="dashboard">
    <h1>Manage Users</h1>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Registered</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($users)) { ?>
          <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td><a href="?delete=<?= $row['id'] ?>" class="btn-del" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <br>
    <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
  </section>
</body>
</html>
