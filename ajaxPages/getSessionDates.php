<?php
include "../phpScripts/isLoggedIn.php";
$name = $_POST["name"]; #get the value from the graphs input boxes
$userID = $_SESSION["ID"];

$dates= []; #create a blank array

include "../phpScripts/connectToDB.php"; #connect to the db
  $stmt = $mysqli->prepare('SELECT DATE_FORMAT(MIN(SessionDate),"%d-%m-%Y") AS Earliest, DATE_FORMAT(MAX(SessionDate),"%d-%m-%Y") AS Latest
                            FROM sessions
                            INNER JOIN exercises ON(sessions.ExerciseID=exercises.ExerciseID)
                            WHERE sessions.UserID = ? AND exercises.Name = ?;');
  $stmt->bind_param("is",$userID,$name);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
while($row = $result->fetch_assoc()){
  array_push($dates, $row["Earliest"]);
  array_push($dates, $row["Latest"]);
}

echo(json_encode($dates)); #send the array to the current page
exit;
?>
