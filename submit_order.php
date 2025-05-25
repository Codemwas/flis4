<?php
// submit_order.php
$host = "localhost";
$user = "root";
$pass = "";
$db = "project";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get form data
$farmerName = $_POST['farmerName'];
$product = $_POST['product'];
$quantity = $_POST['quantity'];
$destination = $_POST['destination'];

// Insert into orders table
$sql = "INSERT INTO orders (farmer_name, product, quantity, destination)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssis", $farmerName, $product, $quantity, $destination);

if ($stmt->execute()) {
  echo "<script>alert('Order placed successfully!'); window.location.href='orders.html';</script>";
} else {
  echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
