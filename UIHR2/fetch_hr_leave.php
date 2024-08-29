<?php
$servername = "localhost";
$username = "root"; // change this to your database username
$password = ""; // change this to your database password
$dbname = "accinfo";

$conn = new mysqli($servername, $username, $password, $dbname, '3307');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT leave_id, Emp_name, leave_type, from_when, to_when, leave_datelog, reason FROM leave_request";
$result = $conn->query($sql);

$leave_requests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $leave_requests[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($leave_requests);
?>
