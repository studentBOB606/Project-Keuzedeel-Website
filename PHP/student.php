<?php
session_start();
require_once 'classes.php';

// Require student access
Auth::requireStudent();
?>
<h1>Student dashboard</h1>
