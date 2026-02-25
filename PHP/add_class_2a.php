<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sample students for class 2A
$students2A = [
    ['studentnummer' => '2001', 'opleiding' => '25998BOL', 'klas' => '2A', 'score' => 7.5],
    ['studentnummer' => '2002', 'opleiding' => '25998BOL', 'klas' => '2A', 'score' => 8.2],
    ['studentnummer' => '2003', 'opleiding' => '25998BOL', 'klas' => '2A', 'score' => 6.8],
    ['studentnummer' => '2004', 'opleiding' => '25998BOL', 'klas' => '2A', 'score' => 9.0],
    ['studentnummer' => '2005', 'opleiding' => '25998BOL', 'klas' => '2A', 'score' => 7.1],
];

// Default password for new students (you should change this)
$defaultPassword = password_hash('password123', PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO student (studentnummer, opleiding, klas, score, password_hash) VALUES (?, ?, ?, ?, ?)");

$added = 0;
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
            echo "Added student {$student['studentnummer']} to class 2A\n";
            $added++;
        } else {
            echo "Error adding student {$student['studentnummer']}: " . $stmt->error . "\n";
        }
    } else {
        echo "Student {$student['studentnummer']} already exists, skipping...\n";
    }
}

echo "\n✓ Added {$added} new students to class 2A!\n";
echo "Default password for all new students: password123\n";

$conn->close();
?>
