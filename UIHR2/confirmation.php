<?php
// Start session and check if the user is an admin
// Include the database connection file
include_once("verified/connection.php");
$conn = connection();

// Fetch users awaiting approval
$sql = "SELECT id, username, email, role FROM useracc WHERE status = 'pending'";
$result = $conn->query($sql);

// Store the result in an array for JSON output
$pendingUsers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pendingUsers[] = $row;
    }
}
$conn->close();

// Output the data as JSON
header('Content-Type: application/json');
echo json_encode($pendingUsers);
?>
