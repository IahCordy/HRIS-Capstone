<?php
// Include the database connection file
include_once("verified/connection.php");

// Start session
session_start();

// Establish database connection
$con = connection();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve email and password from form
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Hash the password using SHA1
    $hashedPassword = sha1($password);

    // Prepare SQL statement to fetch user's hashed password and status from user_account table
    $sql = "SELECT id, username, role, status FROM useracc WHERE email = ? AND password = ?";

    // Prepare and bind parameters
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $email, $hashedPassword);

    // Execute query
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($id, $username, $role, $status);

    // Fetch the result from user_account table
    if ($stmt->fetch()) {
        if ($status == 'approved') {
            // Login successful and user is approved
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Return success along with username and role
            echo json_encode(array('success' => true, 'username' => $username, 'role' => $role));
        } else {
            // User is not approved
            echo json_encode(array('success' => false, 'message' => 'Your account is not approved yet. Please wait for admin approval.'));
        }

        // Close statement and connection
        $stmt->close();
        $con->close();
        exit; // Ensure no further code execution
    } 

    // Prepare SQL statement to fetch user's hashed password from power table
    $sql = "SELECT admin_id, email FROM power WHERE email = ? AND password = ?";

    // Prepare and bind parameters for power table
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $email, $hashedPassword);

    // Execute query for power table
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($admin_id, $admin_email);

    // Fetch the result from power table
    if ($stmt->fetch()) {
        // Login successful from power table (assume admin role)
        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['username'] = $admin_email; // Storing email as username for admin
        $_SESSION['role'] = "admin"; // Fixed role for all users in power table

        // Return success along with username and role
        echo json_encode(array('success' => true, 'username' => $admin_email, 'role' => "admin"));
    } else {
        // Login failed
        echo json_encode(array('success' => false, 'message' => 'Incorrect email or password. Please try again.'));
    }

    // Close statement and connection
    $stmt->close();
    $con->close();
}
?>
