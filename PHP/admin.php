<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    die("Access denied");
}
?>
<h1>Admin dashboard</h1>
