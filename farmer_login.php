<?php
session_start();

// Connect to DB
$conn = new mysqli("localhost", "root", "", "project");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get form data
$email = $_POST['email'];
$password = $_POST['password'];

// Fetch farmer from DB
$sql = "SELECT * FROM farmers WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $farmer = $result->fetch_assoc();

  if (password_verify($password, $farmer['password'])) {
    // Set session variables
    $_SESSION['farmer_id'] = $farmer['id'];
    $_SESSION['farmer_name'] = $farmer['name'];

    // ✅ Redirect to dashboard
    header("Location: farmer_dashboard.php");
    exit();
  } else {
    echo "<script>alert('Incorrect password'); window.location.href='farmer_login.html';</script>";
  }
} else {
  echo "<script>alert('Email not found'); window.location.href='farmer_login.html';</script>";
}

$stmt->close();
$conn->close();
?>
