<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process tracking request
$tracking_id = '';
$tracking_data = null;
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['trackingID'])) {
    $tracking_id = trim($_GET['trackingID']);
    
    if (!empty($tracking_id)) {
        // Sanitize input
        $tracking_id = $conn->real_escape_string($tracking_id);
        
        // Query database
        $sql = "SELECT * FROM shipments WHERE tracking_id = '$tracking_id'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $tracking_data = $result->fetch_assoc();
        } else {
            $error = "No shipment found with tracking ID: " . htmlspecialchars($tracking_id);
        }
    } else {
        $error = "Please enter a tracking ID";
    }
}

// Output HTML
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Track Your Product</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .tracking-results {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .status-processing { color: #f39c12; }
        .status-transit { color: #3498db; }
        .status-delivered { color: #2ecc71; }
        .status-delayed { color: #e74c3c; }
        .error { color: #e74c3c; }
    </style>
</head>

<body>
    <section class="reveal">
        <h2>Why Farmers Logistics?</h2>
        <p>We simplify transport, tracking, and payments.</p>
    </section>

    <button class="btn-toggle" onclick="toggleDarkMode()">🌓 Toggle Mode</button>

    <header>
        <h1>Track Your Product</h1>
        <nav>
            <ul>
                <li><a href="home.html">Home</a></li>
                <li><a href="reg.html">Register</a></li>
                <li><a href="product.html">Products</a></li>
                <li><a href="order.html">Orders</a></li>
                <li><a href="tracking.php">Track Product</a></li>
                <li><a href="dashboard.html">Reports</a></li>
                <li><a href="payments.html">Payments</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <h2>Track Your Order</h2>
        <p>Enter your tracking ID below to view the current status of your shipment.</p>
        <form action="tracking.php" method="GET">
            <label for="trackingID">Tracking ID:</label>
            <input type="text" id="trackingID" name="trackingID" 
                   value="<?php echo htmlspecialchars($tracking_id); ?>" 
                   placeholder="e.g. TRK123456" required>
            <button type="submit">Track</button>
        </form>

        <?php if ($error): ?>
            <div class="error">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($tracking_data): ?>
            <div class="tracking-results">
                <h3 class="status-<?php echo strtolower(str_replace(' ', '-', $tracking_data['status'])); ?>">
                    Status: <?php echo $tracking_data['status']; ?>
                </h3>
                <p><strong>Product:</strong> <?php echo $tracking_data['product_name']; ?></p>
                <p><strong>From:</strong> <?php echo $tracking_data['origin']; ?></p>
                <p><strong>To:</strong> <?php echo $tracking_data['destination']; ?></p>
                <p><strong>Current Location:</strong> <?php echo $tracking_data['current_location']; ?></p>
                <p><strong>Last Update:</strong> <?php echo date('M j, Y g:i A', strtotime($tracking_data['last_update'])); ?></p>
                <p><strong>Estimated Delivery:</strong> <?php echo date('M j, Y g:i A', strtotime($tracking_data['estimated_delivery'])); ?></p>
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "GET" && empty($tracking_id)): ?>
            <div class="tracking-results">
                <p>Please enter a tracking ID to check your shipment status.</p>
            </div>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Farmers Logistics System</p>
    </footer>

</body>
</html>

<?php
$conn->close();
?>