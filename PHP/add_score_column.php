<?php
// Script to add score column to student table

$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add score column
$sql = "ALTER TABLE student ADD COLUMN score DECIMAL(3,1) DEFAULT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Score column added successfully!";
} else {
    if (strpos($conn->error, "Duplicate column name") !== false) {
        echo "Score column already exists.";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
