<?php
include "../phpScripts/isLoggedIn.php";
$val = $_POST["value"]; #get the value from the exerciseMaker form
$id = $_SESSION["ID"];
include "../phpScripts/connectToDB.php"; #connect to the db
$stmt = $mysqli->prepare('SELECT Name FROM exercises WHERE Name = ? AND (UserID = ? OR UserID = 1)');
$stmt->bind_param("si",$val,$id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

echo(json_encode($result->num_rows)); #send the amount of rows found to the page
?>
