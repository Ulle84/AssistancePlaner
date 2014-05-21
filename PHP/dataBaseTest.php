<?php
$con = mysqli_connect("rdbms.strato.de", "U1702580", "LdX5GPoDUoYAgPSoa24R6Sf0dPNlAL", "DB1702580");

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

mysqli_close($con);
?>