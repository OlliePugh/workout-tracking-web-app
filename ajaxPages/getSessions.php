<?php
include "../phpScripts/isLoggedIn.php";

$errorPrefix = "getSessions says: ";

//GET ALL POST VALUES

if (isset($_POST["value"])){
  $val = $_POST["value"];
}
else{
  $val = NULL;
}

if (isset($_POST["limit"])){
  $limit = $_POST["limit"];
}
else{
  $limit = 5;
}

if (isset($_POST["exact"])){
  $exact = $_POST["exact"];
  if (strtolower($exact) == "true"){
    $exact = True;
  }
  else if (strtolower($exact) == "false"){
    $exact = False;
  }
  else{
    echo(json_encode("Invalid Exact"));
    exit;
  }
}
else{
  $exact = false;
}

if (isset($_POST["searchType"])){
  $searchType = $_POST["searchType"];
}
else{
  $searchType = NULL;
}

$earliest = $_POST["earliest"]; //these will be null if they are not specified
$latest = $_POST["latest"];

if (($earliest == NULL || $latest == NULL) && ($searhType == "maxAmount" || $searchType = "maxReps")){
  echo(json_encode($errorPrefix."Earliest or Latest not specified"));
  exit;
}

if($searchType == "maxAmount" || $searchType == "maxReps"){
  try {
      $ear = new DateTime($earliest); //test if it is a date
      $lat = new DateTime($latest);
  } catch (Exception $e) {
      echo(json_encode($errorPrefix."Invalid Time Constraints"));
      exit;
  }
  if($lat < $ear){ //if the end date is before the start date
    echo(json_encode($errorPrefix."End Constraint Is before Start Constraint"));
    exit;
  }
}

$id = $_SESSION["ID"];
$sessions= []; #create a blank array
include "../phpScripts/connectToDB.php"; #connect to the db
if ($searchType === NULL){
  if (!empty($val)){ #if the value is not empty it means that the user wants to search for a certain session
    if ($exact){
      $val = ($val."%");
    }
    //GET EXERCISES WITH SPECIFIED NAME
    $stmt = $mysqli->prepare('SELECT exercises.Name, exercises.Type, SessionDate, Reps, Amount
                              FROM sessions
                              INNER JOIN exercises ON (sessions.ExerciseID=exercises.ExerciseID)
                              WHERE exercises.Name LIKE ? AND sessions.UserID = ?
                              ORDER BY SessionDate DESC
                              LIMIT ?');
    $stmt->bind_param("sii",$val,$id,$limit);
  }
  else{ #no specific exercise type requested therefore show all of the users
    //GET ALL EXERCISES
    $stmt = $mysqli->prepare('SELECT exercises.Name, exercises.Type, SessionDate, Reps, Amount
                              FROM sessions
                              INNER JOIN exercises ON (sessions.ExerciseID=exercises.ExerciseID)
                              WHERE sessions.UserID = ?
                              ORDER BY SessionDate DESC
                              LIMIT ?'); //orders all sessoins from a user in newest first
    $stmt->bind_param("ii",$id,$limit);
  }
}
else if ($searchType == "maxAmount"){
  //GET MAX AMOUNT DONE PER DAY WITH SPECIFIED EXERCISE NAME
  $stmt = $mysqli->prepare('SELECT exercises.Name, exercises.Type, sessions.SessionDate, sessions.Reps, max(Amount) AS Amount
                            FROM sessions
                            INNER JOIN exercises ON (sessions.ExerciseID = exercises.ExerciseID)
                            WHERE exercises.Name = ? AND sessions.UserID = ?
                            AND sessions.SessionDate >= STR_TO_DATE(?, "%d-%m-%Y") AND sessions.SessionDate <= STR_TO_DATE(?, "%d-%m-%Y")
                            GROUP BY SessionDate;');
                            $stmt->bind_param("siss",$val,$id,$earliest,$latest);
}
else if ($searchType == "maxReps"){
  //GET MAX REPS DONE PER DAY WITH SPECIFIED EXERCISE NAME
  $stmt = $mysqli->prepare('SELECT exercises.Name, exercises.Type, sessions.SessionDate, max(Reps) As Reps, Amount
                            FROM sessions
                            INNER JOIN exercises ON (sessions.ExerciseID = exercises.ExerciseID)
                            WHERE exercises.Name = ? AND sessions.UserID = ?
                            AND sessions.SessionDate >= STR_TO_DATE(?, "%d-%m-%Y") AND sessions.SessionDate <= STR_TO_DATE(?, "%d-%m-%Y")
                            GROUP BY SessionDate;');
                            $stmt->bind_param("siss",$val,$id,$earliest,$latest);
}
else{
  echo(json_encode("Unknown Search Type: ".$searchType));
  exit;

}

if (isset($stmt)){
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
}

if ($result->num_rows < 2){
  echo(json_encode("NED")); //NED = Not Enough Data
  exit;
}

while($row = $result->fetch_assoc()){
  array_push($sessions, array($row["Name"],$row["Type"],$row["SessionDate"],$row["Reps"],$row["Amount"])); #append each machine found to the array
}

echo(json_encode($sessions)); #send the array to the current page
exit;
?>
