<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete student with ID 27
$stmt = $conn->prepare("DELETE FROM student WHERE id = ?");
$id = 27;
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "Successfully deleted student #27 from the database.\n";
    } else {
        echo "Student #27 not found.\n";
    }
} else {
    echo "Error deleting student: " . $stmt->error . "\n";
}

$conn->close();
?>
