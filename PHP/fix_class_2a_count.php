<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete the old students from class 2A (IDs 23, 24, 25, 26)
$oldStudentIds = [23, 24, 25, 26];

echo "Removing old students from class 2A...\n";
echo "========================================\n\n";

foreach ($oldStudentIds as $id) {
    $stmt = $conn->prepare("DELETE FROM student WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "✓ Deleted student ID: {$id}\n";
        } else {
            echo "⊘ Student ID {$id} not found\n";
        }
    } else {
        echo "✗ Error deleting student ID {$id}: " . $stmt->error . "\n";
    }
}

// Check final counts
echo "\n========================================\n";
echo "Final class distribution:\n";
echo "========================================\n";

$result = $conn->query("SELECT DISTINCT klas, COUNT(*) as count FROM student GROUP BY klas ORDER BY klas");
while ($row = $result->fetch_assoc()) {
    echo "Class {$row['klas']}: {$row['count']} students\n";
}

$check2A = $conn->query("SELECT COUNT(*) as count FROM student WHERE klas = '2A'");
$count2A = $check2A->fetch_assoc()['count'];

$check2B = $conn->query("SELECT COUNT(*) as count FROM student WHERE klas = '2B'");
$count2B = $check2B->fetch_assoc()['count'];

if ($count2A == $count2B) {
    echo "\n🎉 SUCCESS! Class 2A now has the same number of students as 2B!\n";
} else {
    echo "\n⚠ Class 2A has " . abs($count2A - $count2B) . " " . ($count2A < $count2B ? "fewer" : "more") . " students than 2B\n";
}

$conn->close();
?>
