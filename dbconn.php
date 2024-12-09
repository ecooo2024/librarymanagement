<?php
// Ensure session is started only once

$dbservername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "database";  // Replace with your actual database name

// Create connection
$conn = mysqli_connect($dbservername, $dbusername, $dbpassword, $dbname);

// Check connection
if (!$conn) {
    echo "Connected unsuccessfully";
    die("Connection failed: " . mysqli_connect_error());
}

// Select the database
if (!mysqli_select_db($conn, $dbname)) {
    die("Database selection failed: " . mysqli_error($conn));
}
?>
