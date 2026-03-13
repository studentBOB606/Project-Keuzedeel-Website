<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all students in class 2A
$result = $conn->query("SELECT id, studentnummer, opleiding, klas, score FROM student WHERE klas = '2A' ORDER BY id");

echo "Students in class 2A:\n";
echo "=====================\n";
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']} | Studentnummer: {$row['studentnummer']} | Score: " . ($row['score'] ?? 'NULL') . "\n";
}

$conn->close();
?>
