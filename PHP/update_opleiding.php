<?php
// Script to update opleiding names to proper course names

$conn = new mysqli("localhost", "root", "", "studenten");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check current opleiding values
$result = $conn->query("SELECT DISTINCT opleiding FROM student");
echo "Current opleiding values:\n";
while ($row = $result->fetch_assoc()) {
    echo "- " . $row['opleiding'] . "\n";
}

echo "\n\nUpdating opleiding names...\n";

// Update opleiding names to proper course names
$updates = [
    "Software Development" => "Game Developer",
    "ICT" => "Software Developer",
    "IT" => "Web Developer",
    "Computer Science" => "Application Developer"
];

foreach ($updates as $old => $new) {
    $conn->query("UPDATE student SET opleiding = '$new' WHERE opleiding = '$old'");
}

// If any empty or null values, set a default
$conn->query("UPDATE student SET opleiding = 'Game Developer' WHERE opleiding = '' OR opleiding IS NULL");

echo "Update complete!\n\n";

// Show updated values
$result = $conn->query("SELECT DISTINCT opleiding FROM student");
echo "Updated opleiding values:\n";
while ($row = $result->fetch_assoc()) {
    echo "- " . $row['opleiding'] . "\n";
}

$conn->close();
?>
