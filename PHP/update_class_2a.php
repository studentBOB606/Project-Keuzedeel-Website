<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check current 2A students
$check2A = $conn->query("SELECT COUNT(*) as count FROM student WHERE klas = '2A'");
$current2A = $check2A->fetch_assoc()['count'];

$check2B = $conn->query("SELECT COUNT(*) as count FROM student WHERE klas = '2B'");
$current2B = $check2B->fetch_assoc()['count'];

echo "Current status:\n";
echo "===============\n";
echo "Class 2A: {$current2A} students\n";
echo "Class 2B: {$current2B} students\n";
echo "\nNeed to add " . ($current2B - $current2A) . " students to class 2A\n\n";

// Generate students for class 2A to match 2B count
$students2A = [];
for ($i = 1; $i <= 21; $i++) {
    $studentnummer = 2000 + $i;
    $scores = [6.5, 7.0, 7.5, 8.0, 8.5, 6.8, 7.2, 8.3, 7.8, 6.9, 7.4, 8.1, 7.6, 6.7, 7.9, 8.4, 7.3, 6.6, 8.2, 7.7, 7.1];
    $students2A[] = [
        'studentnummer' => (string)$studentnummer,
        'opleiding' => '25998BOL',
        'klas' => '2A',
        'score' => $scores[$i - 1]
    ];
}

// Default password for new students
$defaultPassword = password_hash('password123', PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO student (studentnummer, opleiding, klas, score, password_hash) VALUES (?, ?, ?, ?, ?)");

$added = 0;
$skipped = 0;

foreach ($students2A as $student) {
    // Check if student already exists
    $check = $conn->prepare("SELECT id FROM student WHERE studentnummer = ?");
    $check->bind_param("s", $student['studentnummer']);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->bind_param("sssds", 
            $student['studentnummer'], 
            $student['opleiding'], 
            $student['klas'], 
            $student['score'],
            $defaultPassword
        );
        
        if ($stmt->execute()) {
            echo "✓ Added student {$student['studentnummer']} to class 2A (Score: {$student['score']})\n";
            $added++;
        } else {
            echo "✗ Error adding student {$student['studentnummer']}: " . $stmt->error . "\n";
        }
    } else {
        $skipped++;
    }
}

echo "\n===================\n";
echo "Summary:\n";
echo "===================\n";
echo "✓ Added: {$added} new students\n";
echo "⊘ Skipped: {$skipped} existing students\n";

// Final count
$finalCheck = $conn->query("SELECT COUNT(*) as count FROM student WHERE klas = '2A'");
$final2A = $finalCheck->fetch_assoc()['count'];

echo "\nFinal count:\n";
echo "Class 2A: {$final2A} students\n";
echo "Class 2B: {$current2B} students\n";

if ($final2A == $current2B) {
    echo "\n🎉 SUCCESS! Class 2A now has the same number of students as 2B!\n";
} else {
    echo "\n⚠ Class 2A has " . abs($final2A - $current2B) . " " . ($final2A < $current2B ? "fewer" : "more") . " students than 2B\n";
}

echo "\nDefault password for all new students: password123\n";

$conn->close();
?>
