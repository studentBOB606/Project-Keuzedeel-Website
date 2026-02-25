<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check all students in class 2A
$result = $conn->query("SELECT id, studentnummer, opleiding, klas, score FROM student WHERE klas = '2A'");

echo "Students in class 2A:\n";
echo "===================\n\n";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']} | ";
        echo "Studentnummer: {$row['studentnummer']} | ";
        echo "Opleiding: {$row['opleiding']} | ";
        echo "Klas: {$row['klas']} | ";
        echo "Score: " . ($row['score'] ?? 'NULL') . "\n";
    }
    echo "\nTotal: " . $result->num_rows . " students in class 2A\n";
} else {
    echo "No students found in class 2A\n";
}

echo "\n\nAll classes in database:\n";
echo "========================\n";
$klassResult = $conn->query("SELECT DISTINCT klas, COUNT(*) as count FROM student GROUP BY klas ORDER BY klas");
while ($row = $klassResult->fetch_assoc()) {
    echo "Class {$row['klas']}: {$row['count']} students\n";
}

$conn->close();
?>
