<?php
include "../phpScripts/isLoggedIn.php";
$type = $_POST["type"];
$units= []; #create a blank array
include "../phpScripts/connectToDB.php"; #connect to the db
$stmt = $mysqli->prepare('SELECT Name, Conversion FROM units WHERE Type = ? ORDER BY Conversion DESC;');
$stmt->bind_param("s",$type);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
while($row = $result->fetch_assoc()){
  array_push($units, [$row["Name"],$row["Conversion"]]); #append each machine found to the array
}
echo(json_encode($units)); #send the array to the current page
?>
