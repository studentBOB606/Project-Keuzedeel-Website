<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== CLASS STATISTICS DEBUG ===\n\n";

// Get all classes
$classes = $conn->query("SELECT DISTINCT klas FROM student ORDER BY klas");

while ($class = $classes->fetch_assoc()) {
    $klas = $class['klas'];
    
    echo "CLASS: {$klas}\n";
    echo str_repeat("-", 50) . "\n";
    
    // Get all students in class with their scores
    $students = $conn->query("SELECT studentnummer, score FROM student WHERE klas = '$klas' ORDER BY studentnummer");
    
    $count = 0;
    $scoreCount = 0;
    $sum = 0;
    
    while ($student = $students->fetch_assoc()) {
        $count++;
        $score = $student['score'];
        if ($score !== null) {
            $scoreCount++;
            $sum += $score;
            echo "  Student {$student['studentnummer']}: {$score}\n";
        } else {
            echo "  Student {$student['studentnummer']}: NULL\n";
        }
    }
    
    echo "\nTotal students: {$count}\n";
    echo "Students with scores: {$scoreCount}\n";
    
    if ($scoreCount > 0) {
        $manual_avg = $sum / $scoreCount;
        echo "Sum of scores: {$sum}\n";
        echo "Manual average: {$manual_avg}\n";
    }
    
    // Get MySQL calculated average
    $result = $conn->query("SELECT COUNT(*) as total, AVG(score) as avg_score FROM student WHERE klas = '$klas'");
    $stats = $result->fetch_assoc();
    
    echo "MySQL COUNT(*): {$stats['total']}\n";
    echo "MySQL AVG(score): " . ($stats['avg_score'] !== null ? $stats['avg_score'] : 'NULL') . "\n";
    
    echo "\n" . str_repeat("=", 50) . "\n\n";
}

$conn->close();
?>
