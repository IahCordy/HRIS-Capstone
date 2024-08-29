<?php
session_start();

date_default_timezone_set('Asia/Manila');
// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect or handle unauthorized access
    header("HTTP/1.1 401 Unauthorized");
    exit("Unauthorized access. Please log in.");
}

$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "accinfo"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, '3307');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if POST data is set
if (!isset($_POST['leave_type'], $_POST['from_when'], $_POST['to_when'], $_POST['reason'])) {
    exit("Error: Required fields are missing.");
}

// Prepare and bind the parameters
$Emp_name = $_SESSION['username']; // Get the username from session
$leave_type = $_POST['leave_type'];
$from_when = $_POST['from_when'];
$to_when = $_POST['to_when'];
$reason = $_POST['reason'];
$leave_datelog = date('Y-m-d H:i:s'); // Current date and time

// SQL statement to insert leave request
$sql = "INSERT INTO leave_request (Emp_name, leave_type, from_when, to_when, reason, leave_datelog, leave_status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param("ssssss", $Emp_name, $leave_type, $from_when, $to_when, $reason, $leave_datelog);

// Execute query
if ($stmt->execute()) {
    echo "Leave request submitted successfully.";
} else {
    echo "Error: " . $stmt->error;
}

// Close statement and database connection
$stmt->close();
$conn->close();
?>
