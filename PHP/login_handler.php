<?php
session_start();

$conn = new mysqli("localhost", "root", "", "studenten");
if ($conn->connect_error) {
    die("DB error");
}

$studentnummer = $_POST["username"] ?? null;
$password      = $_POST["password"] ?? null;

if (!$studentnummer || !$password) {
    die("Missing data");
}

$stmt = $conn->prepare(
    "SELECT id, password_hash
     FROM student
     WHERE studentnummer = ?"
);
$stmt->bind_param("s", $studentnummer);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Login failed");
}

$student = $result->fetch_assoc();

if (!password_verify($password, $student["password_hash"])) {
    die("Login failed");
}

$_SESSION["student_id"] = $student["id"];


// NORMAL LOGIN REDIRECT
header("Location: index.php");
exit;
