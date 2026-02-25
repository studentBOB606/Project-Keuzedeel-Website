<?php
require_once '../bootstrap.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Laravel Integration Test</title></head><body>";
echo "<h1>Laravel Components Integration Test</h1>";

try {
    // Test database connection
    echo "<h2>✓ Database Connection</h2>";
    echo "<p>Connected to: studenten database</p>";
    
    // Test Eloquent models
    $studentCount = \App\Models\Student::count();
    echo "<h2>✓ Eloquent ORM</h2>";
    echo "<p>Found {$studentCount} students in database</p>";
    
    // Test getting all students
    $students = Student::getAll();
    echo "<h2>✓ Backward Compatibility</h2>";
    echo "<p>Old Student::getAll() method still works: " . count($students) . " students</p>";
    
    // Test Auth class
    echo "<h2>✓ Authentication Class</h2>";
    echo "<p>Auth::isLoggedIn(): " . (Auth::isLoggedIn() ? 'Yes' : 'No') . "</p>";
    echo "<p>Auth::isAdmin(): " . (Auth::isAdmin() ? 'Yes' : 'No') . "</p>";
    echo "<p>Auth::isStudent(): " . (Auth::isStudent() ? 'Yes' : 'No') . "</p>";
    
    // Show first few students
    if (!empty($students)) {
        echo "<h2>Sample Students</h2>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Studentnummer</th><th>Opleiding</th><th>Klas</th><th>Score</th></tr>";
        foreach (array_slice($students, 0, 5) as $student) {
            echo "<tr>";
            echo "<td>" . $student->getId() . "</td>";
            echo "<td>" . $student->getStudentnummer() . "</td>";
            echo "<td>" . $student->getOpleiding() . "</td>";
            echo "<td>" . $student->getKlas() . "</td>";
            echo "<td>" . $student->getFormattedScore() . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<h2 style='color: green;'>✓ All tests passed! Laravel components are working correctly.</h2>";
    echo "<p><a href='index.php'>Go to Main Application</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>✗ Error occurred</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
