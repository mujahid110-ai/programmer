<?php
$host = 'localhost';
$user = 'root';
$password = ''; // Leave it empty if no password is set
$database = 'university_attendance_system';
$port = 3307; // Use the new port 3307

$conn = new mysqli($host, $user, $password, $database, $port);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
