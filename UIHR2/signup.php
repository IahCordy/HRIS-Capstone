<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "accinfo";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname, '3307');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Add the `status` column if it doesn't exist (one-time migration)
    $conn->query("ALTER TABLE useracc ADD COLUMN IF NOT EXISTS status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO useracc (username, email, password, role, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    // Set parameters and execute
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = sha1($_POST["password"]); // Hash the password using SHA1
    $role = $_POST["role"];

    // Execute SQL statement
    if ($stmt->execute()) {
        // Redirect back to login.html
        header("Location: Login.html");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
