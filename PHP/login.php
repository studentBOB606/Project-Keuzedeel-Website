<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h1>Login</h1>

<form method="post" action="login_handler.php">
    <label>Studentnummer</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="role" value="student">Login as student</button>
    <button type="submit" name="role" value="admin">Login as admin</button>
</form>

</body>
</html>
