<?php
function connection() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "accinfo";

    // Create connection
    $con = new mysqli($host, $username, $password, $database, '3307');

    // Check connection
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    return $con;
}