<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update the opleiding to Game Developer
$conn->query("UPDATE student SET opleiding = 'Game Developer' WHERE opleiding = '25604BOL'");

echo "Updated opleiding to 'Game Developer'!\n";

$conn->close();
?>
