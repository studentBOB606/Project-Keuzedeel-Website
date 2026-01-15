<?php
// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request");
}

// Read form data
$studentNumber = $_POST["student_number"] ?? null;
$password = $_POST["password"] ?? null;

if (!$studentNumber || !$password) {
    die("Missing data");
}

// Database connection
$conn = new mysqli("localhost", "root", "", "studenten");
if ($conn->connect_error) {
    die("Database connection failed");
}

// Check if student exists
$studentStmt = $conn->prepare(
    "SELECT id FROM student WHERE studentnummer = ?"
);
$studentStmt->bind_param("s", $studentNumber);
$studentStmt->execute();
$result = $studentStmt->get_result();

if ($result->num_rows === 0) {
    die("Student not found");
}

$student = $result->fetch_assoc();
$studentId = $student["id"];

// Check if user already exists
$checkStmt = $conn->prepare(
    "SELECT id FROM users WHERE student_id = ?"
);
$checkStmt->bind_param("i", $studentId);
$checkStmt->execute();
$exists = $checkStmt->get_result();

if ($exists->num_rows > 0) {
    die("Student already registered");
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Create user
$insertStmt = $conn->prepare(
    "INSERT INTO users (username, password_hash, role, student_id)
     VALUES (?, ?, 'student', ?)"
);
$insertStmt->bind_param("ssi", $studentNumber, $passwordHash, $studentId);
$insertStmt->execute();

echo "Registration successful. You can now log in.";
