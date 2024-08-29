<?php
// Include the database connection file
include_once("verified/connection.php");
$conn = connection();

// Check if the request is POST and the id is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $acc_id = $_POST['id'];

    // Prepare SQL statement to update the user's status
    $sql = "UPDATE useracc SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $acc_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Failed to approve user.'));
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(array('success' => false, 'message' => 'Invalid request.'));
}
?>
