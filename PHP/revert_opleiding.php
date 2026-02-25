<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Change opleiding back to code
$conn->query("UPDATE student SET opleiding = '25998BOL' WHERE opleiding = 'Game Developer'");

echo "Updated opleiding back to '25998BOL'!\n";

$conn->close();
?>
