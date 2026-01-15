<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    die("Access denied");
}
?>
<h1>Student dashboard</h1>
