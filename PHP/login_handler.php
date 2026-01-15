<?php
session_start();

$host = "localhost";
$db   = "studenten";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB error");
}

$username = $_POST["username"];
$password = $_POST["password"];
$role     = $_POST["role"];

$stmt = $conn->prepare(
    "SELECT id, password_hash, role FROM users WHERE username = ? AND role = ?"
);
$stmt->bind_param("ss", $username, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $userRow = $result->fetch_assoc();

    if (password_verify($password, $userRow["password_hash"])) {
        $_SESSION["user_id"] = $userRow["id"];
        $_SESSION["role"] = $userRow["role"];

        if ($userRow["role"] === "admin") {
            header("Location: admin.php");
        } else {
            header("Location: student.php");
        }
        exit;
    }
}

echo "Login failed";
