<?php include('authentication.php'); ?>

<?php

$db = new SQLite3('../Data/' . $_SESSION['clientName'] . '/Database.sqlite3');
$db->exec("CREATE TABLE IF NOT EXISTS Team(
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   FirstName TEXT NOT NULL DEFAULT '0',
   LastName TEXT NOT NULL DEFAULT '0',
   Priority INTEGER NOT NULL DEFAULT '0')");

$db->exec("INSERT INTO Team (FirstName, LastName, Priority)
VALUES ('Ulrich', 'Belitz', 100)");

/*$results = $db->query("SELECT s.spielid, t.trackdate, t.id
   FROM tableTracks AS t
   JOIN tableSpieler AS s
   WHERE t.trackspieler = s.id
   ORDER BY t.id");
while ($row = $results->fetchArray()) {
    echo $r['trackdate'];
    echo $r['spielid'];
    …
}*/

$results = $db->query('SELECT * FROM Team');

echo "<table>
<tr>
<th>ID</th>
<th>Firstname</th>
<th>Lastname</th>
<th>Priority</th>
</tr>";

while ($row = $results->fetchArray()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['FirstName'] . "</td>";
    echo "<td>" . $row['LastName'] . "</td>";
    echo "<td>" . $row['Priority'] . "</td>";
    echo "</tr>";
}

echo "</table>";


echo 'all operations finished';



/*$con = mysqli_connect("rdbms.strato.de", "U1702580", "LdX5GPoDUoYAgPSoa24R6Sf0dPNlAL", "DB1702580");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Create table
$sql = "CREATE TABLE Persons
(
PID INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(PID),
FirstName CHAR(15),
LastName CHAR(15),
Age INT
)";

// Execute query
if (mysqli_query($con, $sql)) {
    echo "Table persons created successfully";
} else {
    echo "Error creating table: " . mysqli_error($con);
}

mysqli_query($con, "INSERT INTO Persons (FirstName, LastName, Age)
VALUES ('Peter', 'Griffin',35)");

mysqli_query($con, "INSERT INTO Persons (FirstName, LastName, Age)
VALUES ('Glenn', 'Quagmire',33)");

$result = mysqli_query($con, "SELECT * FROM Persons");

echo "<table>
<tr>
<th>Firstname</th>
<th>Lastname</th>
</tr>";

while ($row = mysqli_fetch_array($result)) {
    echo "<tr>";
    echo "<td>" . $row['FirstName'] . "</td>";
    echo "<td>" . $row['LastName'] . "</td>";
    echo "</tr>";
}

echo "</table>";

mysqli_close($con);*/
?>