<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Adding Frontend Development keuzedeel for all students...\n";
echo "=========================================================\n\n";

// Get all existing students
$result = $conn->query("SELECT studentnummer, klas, password_hash FROM student GROUP BY studentnummer ORDER BY studentnummer");

$added = 0;
$skipped = 0;

while ($student = $result->fetch_assoc()) {
    // Check if this student already has Frontend Development keuzedeel
    $checkStmt = $conn->prepare("SELECT id FROM student WHERE studentnummer = ? AND opleiding = '29380BOL'");
    $checkStmt->bind_param("s", $student['studentnummer']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        // Add the new keuzedeel for this student
        $insertStmt = $conn->prepare("INSERT INTO student (studentnummer, opleiding, klas, score, password_hash) VALUES (?, '29380BOL', ?, NULL, ?)");
        $insertStmt->bind_param("sss", 
            $student['studentnummer'], 
            $student['klas'], 
            $student['password_hash']
        );
        
        if ($insertStmt->execute()) {
            echo "✓ Added Frontend Development for student {$student['studentnummer']}\n";
            $added++;
        } else {
            echo "✗ Error adding for student {$student['studentnummer']}: " . $insertStmt->error . "\n";
        }
    } else {
        $skipped++;
    }
}

echo "\n=========================================================\n";
echo "Summary:\n";
echo "✓ Added: {$added} new keuzedeel enrollments\n";
echo "⊘ Skipped: {$skipped} existing enrollments\n";

// Show updated statistics
echo "\n=========================================================\n";
echo "Database Statistics:\n";
echo "=========================================================\n";

$stats = $conn->query("SELECT opleiding, COUNT(*) as count FROM student GROUP BY opleiding ORDER BY opleiding");
while ($row = $stats->fetch_assoc()) {
    echo "Opleiding {$row['opleiding']}: {$row['count']} enrollments\n";
}

$conn->close();
?>
