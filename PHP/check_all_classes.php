<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get count of students by class
$result = $conn->query("SELECT DISTINCT klas, COUNT(*) as count FROM student GROUP BY klas ORDER BY klas");

echo "Current class distribution:\n";
echo "===========================\n";
while ($row = $result->fetch_assoc()) {
    echo "Class {$row['klas']}: {$row['count']} students\n";
}

// Get details of class 2B
echo "\n\nStudents in class 2B:\n";
echo "=====================\n";
$result2b = $conn->query("SELECT id, studentnummer, opleiding, klas, score FROM student WHERE klas = '2B' ORDER BY studentnummer");
while ($row = $result2b->fetch_assoc()) {
    echo "ID: {$row['id']} | Studentnummer: {$row['studentnummer']} | Opleiding: {$row['opleiding']} | Score: " . ($row['score'] ?? 'NULL') . "\n";
}

$conn->close();
?>
