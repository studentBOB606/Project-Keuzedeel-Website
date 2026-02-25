<?php
session_start();
require_once '../bootstrap.php';

$username = $_POST["username"] ?? null;
$password = $_POST["password"] ?? null;
$role     = $_POST["role"] ?? null;

if (!$username || !$password || !$role) {
    die("Missing data");
}

// Attempt login
if (Auth::login($username, $password, $role)) {
    header("Location: ../views/index.php");
    exit;
} else {
    header("Location: ../views/login.php?error=1");
    exit;
}
