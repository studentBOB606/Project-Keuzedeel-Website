<!DOCTYPE html>
<html>
<head>
    <title>Student registration</title>
</head>
<body>

<h1>Register</h1>

<form method="post" action="register_handler.php">
    <label>Student number</label><br>
    <input type="text" name="student_number" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Register</button>
</form>

</body>
</html>
