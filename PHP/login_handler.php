<?php
session_start();
require_once 'classes.php';

$username = $_POST["username"] ?? null;
$password = $_POST["password"] ?? null;
$role     = $_POST["role"] ?? null;

if (!$username || !$password || !$role) {
    die("Missing data");
}

// Attempt login
if (Auth::login($username, $password, $role)) {
    header("Location: index.php");
    exit;
} else {
    die("Login failed");
}
