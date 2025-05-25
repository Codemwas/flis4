<?php
// Only process if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if all expected POST variables are set and not empty
    $required_fields = ['name', 'email', 'phone', 'location', 'product_type', 'password'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        // Show which fields are missing
        $error_message = "❗ The following fields are required: " . implode(', ', $missing_fields);
        echo "<script>alert('" . addslashes($error_message) . "'); window.history.back();</script>";
        exit();
    }

    // Database connection with error handling
    try {
        $conn = new mysqli("localhost", "root", "", "project");
        
        if ($conn->connect_error) {
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }

        // Validate and sanitize inputs
        $name = htmlspecialchars(trim($_POST['name']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
        $location = htmlspecialchars(trim($_POST['location']));
        $product_type = htmlspecialchars(trim($_POST['product_type']));
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        // Hash password
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Check if email already exists
        $check_sql = "SELECT id FROM farmers WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            throw new Exception("Email already registered");
        }
        $check_stmt->close();

        // Insert into farmers table
        $sql = "INSERT INTO farmers (name, email, phone, location, product_type, password)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssss", $name, $email, $phone, $location, $product_type, $password);

        if ($stmt->execute()) {
            echo "<script>alert('✅ Farmer registered successfully!'); window.location.href='reg.html';</script>";
        } else {
            throw new Exception("Error: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        // Handle errors gracefully
        echo "<script>alert('❌ Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        if (isset($conn) && $conn) {
            $conn->close();
        }
        exit();
    }
} else {
    echo "<script>alert('⚠️ Invalid request method. Please submit the form.'); window.location.href='reg.html';</script>";
}
?>