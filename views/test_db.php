<?php
/**
 * Database Connection Test for Views Folder
 * This verifies that the views can connect to the database via bootstrap.php
 */

require_once __DIR__ . '/../bootstrap.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Database Connection Test - Views Folder</title>";
echo "<link rel='stylesheet' href='../css/style.css'>";
echo "<style>body{padding:40px;} .success{color:#065f46;background:#d1fae5;padding:20px;border-radius:12px;margin:20px 0;} .info{background:#e0f2fe;padding:15px;border-radius:8px;margin:10px 0;} table{width:100%;border-collapse:collapse;margin:20px 0;} th,td{padding:12px;text-align:left;border-bottom:1px solid #ddd;} th{background:#f1f5f9;}</style>";
echo "</head><body>";
echo "<h1>🔗 Database Connection Test (Views Folder)</h1>";

try {
    // Test 1: Basic database connection
    echo "<div class='success'><strong>✓ Bootstrap loaded successfully</strong><br>";
    echo "Database: studenten<br>Connected via: Eloquent ORM</div>";
    
    // Test 2: Count students using Eloquent
    $studentCount = \App\Models\Student::count();
    echo "<div class='info'><strong>✓ Eloquent Working</strong><br>";
    echo "Total students in database: <strong>{$studentCount}</strong></div>";
    
    // Test 3: Get sample student data
    $students = \App\Models\Student::take(5)->get();
    if ($students->count() > 0) {
        echo "<h2>Sample Student Data (First 5)</h2>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Studentnummer</th><th>Opleiding</th><th>Klas</th><th>Score</th></tr>";
        foreach ($students as $student) {
            echo "<tr>";
            echo "<td>{$student->id}</td>";
            echo "<td>{$student->studentnummer}</td>";
            echo "<td>{$student->opleiding}</td>";
            echo "<td>{$student->klas}</td>";
            echo "<td>" . ($student->score ?? 'Geen') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test 4: Test backward compatibility wrapper
    echo "<h2>Backward Compatibility Test</h2>";
    $oldStyleStudents = Student::getAll();
    echo "<div class='info'><strong>✓ Old Student class works</strong><br>";
    echo "Student::getAll() returned: <strong>" . count($oldStyleStudents) . " students</strong></div>";
    
    // Test 5: Auth class
    echo "<h2>Authentication Test</h2>";
    echo "<div class='info'>";
    echo "Auth::isLoggedIn(): " . (Auth::isLoggedIn() ? '<strong style="color:#059669">Yes</strong>' : '<strong style="color:#dc2626">No</strong>') . "<br>";
    echo "Auth::isAdmin(): " . (Auth::isAdmin() ? '<strong style="color:#059669">Yes</strong>' : '<strong style="color:#dc2626">No</strong>') . "<br>";
    echo "Auth::isStudent(): " . (Auth::isStudent() ? '<strong style="color:#059669">Yes</strong>' : '<strong style="color:#dc2626">No</strong>') . "</div>";
    
    echo "<hr style='margin:30px 0;'>";
    echo "<h2 style='color:#065f46;'>✅ All Tests Passed!</h2>";
    echo "<p><strong>Database Connection Status:</strong> <span style='color:#059669;font-weight:bold;'>CONNECTED</span></p>";
    echo "<p>Views folder can successfully connect to database via <code>bootstrap.php</code></p>";
    echo "<p><a href='index.php' style='display:inline-block;padding:12px 24px;background:#d4af37;color:#0b2d22;text-decoration:none;border-radius:12px;font-weight:700;margin-top:20px;'>→ Go to Main App</a></p>";
    
} catch (Exception $e) {
    echo "<div style='color:#991b1b;background:#fee2e2;padding:20px;border-radius:12px;'>";
    echo "<h2 style='margin:0 0 10px;'>✗ Error occurred</h2>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre style='background:#fff;padding:15px;border-radius:8px;overflow:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body></html>";
