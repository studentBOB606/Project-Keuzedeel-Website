<?php
session_start();
require_once __DIR__ . '/../bootstrap.php';

// Require admin access
Auth::requireAdmin();
?>
<h1>Admin dashboard</h1>
