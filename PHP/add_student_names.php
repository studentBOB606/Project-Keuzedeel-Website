<?php
$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Adding naam column to student table...\n";

// Add naam column if it doesn't exist
$checkColumn = $conn->query("SHOW COLUMNS FROM student LIKE 'naam'");
if ($checkColumn->num_rows == 0) {
    $sql = "ALTER TABLE student ADD COLUMN naam VARCHAR(100) DEFAULT NULL AFTER studentnummer";
    if ($conn->query($sql)) {
        echo "✓ naam column added successfully\n\n";
    } else {
        die("Error: " . $conn->error);
    }
} else {
    echo "✓ naam column already exists\n\n";
}

// Sample Dutch names
$firstNames = [
    'Emma', 'Noah', 'Sophie', 'Liam', 'Julia', 'Lucas', 'Tess', 'Daan', 
    'Anna', 'Sem', 'Mila', 'Finn', 'Sara', 'Luuk', 'Evi', 'Bram',
    'Lisa', 'Lars', 'Eva', 'Tom', 'Fleur', 'Max', 'Lynn', 'Tim',
    'Amy', 'Sam', 'Noa', 'Jesse', 'Isa', 'Milan', 'Lotte', 'Thijs',
    'Nina', 'Stijn', 'Sanne', 'Ruben', 'Fien', 'Jasper', 'Lieke', 'Jens',
    'Roos', 'David'
];

$lastNames = [
    'de Jong', 'Jansen', 'de Vries', 'van den Berg', 'van Dijk', 'Bakker', 
    'Visser', 'Smit', 'Meijer', 'de Boer', 'Mulder', 'de Groot', 'Bos',
    'Hendriks', 'Peters', 'van Leeuwen', 'Dekker', 'Brouwer', 'de Wit', 'Dijkstra'
];

// Get all unique students
$result = $conn->query("SELECT DISTINCT studentnummer FROM student ORDER BY studentnummer");

$count = 0;
$index = 0;
$usedNames = [];

while ($row = $result->fetch_assoc()) {
    // Generate unique name
    do {
        $firstName = $firstNames[$index % count($firstNames)];
        $lastName = $lastNames[$index % count($lastNames)];
        $fullName = "$firstName $lastName";
        $index++;
    } while (in_array($fullName, $usedNames) && $index < 1000);
    
    $usedNames[] = $fullName;
    
    // Update all records for this student
    $stmt = $conn->prepare("UPDATE student SET naam = ? WHERE studentnummer = ?");
    $stmt->bind_param("ss", $fullName, $row['studentnummer']);
    
    if ($stmt->execute()) {
        echo "✓ Updated {$row['studentnummer']}: $fullName\n";
        $count++;
    }
}

echo "\n✓ Successfully added names for $count students\n";

$conn->close();
