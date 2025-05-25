<?php
// farmer_dashboard.php
// This file is the dashboard for farmers after they log in.
// It checks if the farmer is logged in and displays their name.
// If not logged in, it redirects to the login page.
session_start();
$conn = new mysqli("localhost", "root", "", "project");
if (!isset($_SESSION['farmer_id'])) {
  header("Location: farmerlogin.html");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Farmer Dashboard</title></head>
<body>
  <h2>Welcome, <?= $_SESSION['farmer_name']; ?>! 👨‍🌾</h2>
  <p>You are now logged in as a farmer.</p>
  <a href="logout.php">Logout</a>
</body>
</html>
