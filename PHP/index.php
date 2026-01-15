<?php
$host = "localhost";
$db   = "studenten";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed");
}

$result = $conn->query("SELECT * FROM student");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Students</title>
</head>
<body>
<h1>Students</h1>

<table border="1">
<tr>
<?php
if ($result && $result->num_rows > 0) {
    foreach ($result->fetch_assoc() as $key => $value) {
        echo "<th>$key</th>";
    }
    echo "</tr>";

    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>$value</td>";
        }
        echo "</tr>";
    }
} else {
    echo "<td>No students found</td>";
}

?>

</table>

</body>
</html>
