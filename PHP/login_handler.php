<?php
session_start();

$conn = new mysqli("localhost", "root", "", "studenten");
if ($conn->connect_error) {
    die("DB error");
}

$username = $_POST["username"] ?? null;
$password = $_POST["password"] ?? null;
$role     = $_POST["role"] ?? null;

if (!$username || !$password || !$role) {
    die("Missing data");
}

if ($role === "admin") {

    // ADMIN LOGIN
    $stmt = $conn->prepare(
        "SELECT id, password_hash
         FROM users
         WHERE username = ? AND role = 'admin'"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        die("Login failed");
    }

    $admin = $result->fetch_assoc();

    if (!password_verify($password, $admin["password_hash"])) {
        die("Login failed");
    }

    $_SESSION["admin_id"] = $admin["id"];
    $_SESSION["role"] = "admin";

    header("Location: index.php");
    exit;

} else {

    // STUDENT LOGIN
    $stmt = $conn->prepare(
        "SELECT id, password_hash
         FROM student
         WHERE studentnummer = ?"
    );
    $stmt->bind_param("s", $username);
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
    $_SESSION["role"] = "student";

    header("Location: index.php");
    exit;
}
