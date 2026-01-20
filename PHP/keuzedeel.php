<?php
$conn = new mysqli("localhost", "root", "", "studenten");
if ($conn->connect_error) {
    die("Database connection failed");
}

$result = $conn->query(
    "SELECT id, studentnummer, opleiding, klas FROM student"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Studenten Keuzedeel</title>
</head>
<body>

<h1>Studenten Keuzedeel</h1>

<table border="1">
<tr>
    <th>ID</th>
    <th>Studentnummer</th>
    <th>Opleiding</th>
    <th>Klas</th>
</tr>

<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['studentnummer']}</td>";
        echo "<td>{$row['opleiding']}</td>";
        echo "<td>{$row['klas']}</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No students found</td></tr>";
}
?>

</table>

</body>
</html>
