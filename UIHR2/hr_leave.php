<?php
$servername = "localhost";
$username = "root"; // change this to your database username
$password = ""; // change this to your database password
$dbname = "accinfo";

$conn = new mysqli($servername, $username, $password, $dbname, '3307');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$leave_id = $data['leave_id'];
$action = $data['action'];

if ($action == 'approve') {
    $sql = "UPDATE leave_request SET leave_status = 'approved' WHERE leave_id = ?";
} elseif ($action == 'reject') {
    $sql = "UPDATE leave_request SET leave_status = 'rejected' WHERE leave_id = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $leave_id);

if ($stmt->execute()) {
    $response = ["message" => "Leave request has been {$action}d successfully."];
} else {
    $response = ["message" => "Error: " . $stmt->error];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
