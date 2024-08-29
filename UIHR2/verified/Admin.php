<?php
function Admin() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "adm1n";

    // Create connection
    $con = new mysqli($host, $username, $password, $database, '3307');

    // Check connection
    if ($con->connect_error) {
        die("Admin connection failed: " . $con->connect_error);
    }

    return $con;
}