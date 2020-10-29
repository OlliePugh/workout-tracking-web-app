<?php
include "../phpScripts/isLoggedIn.php";
$val = $_POST["value"]; #get the value from the exerciseMaker form
$userID = $_SESSION["ID"];
$exercises= []; #create a blank array
include "../phpScripts/connectToDB.php"; #connect to the db
if (!empty($val)){ #if the value is not empty it means that the user wants to search for a machine
  $val = ($val."%");
  $stmt = $mysqli->prepare('SELECT exercises.ExerciseID, exercises.Name, exercises.Type FROM sessions INNER JOIN exercises ON (sessions.ExerciseID = exercises.ExerciseID) WHERE sessions.ExerciseID IN
		    (
            SELECT ExerciseID FROM sessions
         	GROUP BY ExerciseID
          	HAVING COUNT(*)>1
        ) AND exercises.Name LIKE ? AND sessions.UserID = ?  GROUP BY exercises.Name ORDER BY exercises.Name');

  $stmt->bind_param("si",$val,$userID);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
}
else{ #this means that the user entered nothing, and therefore should display all of the available exercises
  $stmt = $mysqli->prepare('SELECT exercises.ExerciseID, exercises.Name, exercises.Type FROM sessions INNER JOIN exercises ON (sessions.ExerciseID = exercises.ExerciseID) WHERE sessions.ExerciseID IN
		    (
            SELECT ExerciseID FROM sessions
         	GROUP BY ExerciseID
          	HAVING COUNT(*)>1
        ) AND sessions.UserID = ?  GROUP BY exercises.Name ORDER BY exercises.Name;'); // DEBUG: THESE QUERIES NEED LOOKING AT THE ARE NOT CURRNETLY SELECTING THE CORRECT EXERCISES

  $stmt->bind_param("i",$userID);
  $stmt->execute();
  $result = $stmt->get_result();
  $mysqli->close();
}
while($row = $result->fetch_assoc()){
  array_push($exercises, array("ExerciseID" => $row["ExerciseID"],
                               "Name" => $row["Name"],
                               "Type" => $row["Type"])); #append each exercise found to the array
}
echo(json_encode($exercises)); #send the array to the current page
?>
