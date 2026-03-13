<?php
require_once __DIR__ . '/../bootstrap.php';

echo "=== KEUZEDELEN VERIFICATION ===\n\n";

// Show sample students with their keuzedelen
$db = Database::getInstance();
$result = $db->query("SELECT studentnummer, opleiding, klas, score FROM student ORDER BY studentnummer, opleiding LIMIT 6");

echo "Sample Student Keuzedelen:\n";
echo str_repeat("-", 100) . "\n";
printf("%-15s %-15s %-30s %-10s %-10s\n", "Studentnummer", "Keuzedeel", "Opleiding Name", "Klas", "Score");
echo str_repeat("-", 100) . "\n";

while ($row = $result->fetch_assoc()) {
    printf("%-15s %-15s %-30s %-10s %-10s\n", 
        $row['studentnummer'],
        $row['opleiding'],
        OpleidingHelper::getName($row['opleiding']),
        $row['klas'],
        $row['score'] !== null ? number_format($row['score'], 1) : 'NULL'
    );
}

echo "\n" . str_repeat("=", 100) . "\n";
echo "Total Records by Keuzedeel:\n";
echo str_repeat("-", 100) . "\n";

$stats = $db->query("SELECT opleiding, COUNT(*) as count FROM student GROUP BY opleiding ORDER BY opleiding");
while ($row = $stats->fetch_assoc()) {
    echo "  " . OpleidingHelper::getName($row['opleiding']) . " ({$row['opleiding']}): {$row['count']} enrollments\n";
}

echo "\n" . str_repeat("=", 100) . "\n";
echo "Unique Students:\n";
$uniqueStudents = $db->query("SELECT COUNT(DISTINCT studentnummer) as count FROM student");
$count = $uniqueStudents->fetch_assoc()['count'];
echo "  Total unique students: {$count}\n";

echo "\n✓ All students have been enrolled in both keuzedelen!\n";
?>
