<?php
include "../phpScripts/isLoggedIn.php";
$val = $_POST["value"]; #get the value from the graphs input boxes
$userID = $_SESSION["ID"];
$exercises= []; #create a blank array
include "../phpScripts/connectToDB.php"; #connect to the db
if (!empty($val)){ #if the value is not empty it means that the user wants to search for a machine
  $val = ($val."%");
  $stmt = $mysqli->prepare('SELECT DISTINCT ExerciseID, Name, Type, MachineName FROM exercises WHERE Name LIKE ? AND (UserID = ? OR UserID = 1) ORDER BY Name;');
  $stmt->bind_param("si",$val,$userID);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
}
else{ #this means that the user entered nothing, and therefore should display all of the available exercises
  $stmt = $mysqli->prepare('SELECT DISTINCT ExerciseID, Name, Type, MachineName
                            FROM exercises
                            WHERE (UserID = ? OR UserID = 1)
                            ORDER BY Name');

  $stmt->bind_param("i",$userID);
  $stmt->execute();
  $result = $stmt->get_result();
  $mysqli->close();
}
while($row = $result->fetch_assoc()){
  array_push($exercises, array("ExerciseID" => $row["ExerciseID"],
                               "Name" => $row["Name"],
                               "Type" => $row["Type"],
                               "MachineName" => $row["MachineName"])); #append each machine found to the array
}
echo(json_encode($exercises)); #send the array to the current page
?>
