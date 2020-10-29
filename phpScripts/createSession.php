<?php
  include "isLoggedIn.php";
  $sessionArray = $_POST["session"]; //get the values from the ajax request
  $date = strtotime($_POST["date"]);
  $date = date('Y-m-d',$date);

  //Get the units from the database
 // TODO: rejecting float variables
  function getUnit($unitName){
    $tempUnitArray = [];
    include "connectToDB.php";
    $stmt = $mysqli->prepare("SELECT Name, Conversion FROM units WHERE Type=?");
    $stmt->bind_param("s",$unitName);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    while($row = $result->fetch_assoc()){
      $tempUnitArray[$row["Name"]] = $row["Conversion"];
    }
    return $tempUnitArray;
  }

  $amountUnits = getUnit("amount");
  $timeUnits = getUnit("time");

  function getExerciseID($exerName){
    include "connectToDB.php";
    $stmt = $mysqli->prepare("SELECT ExerciseID FROM exercises WHERE (UserID = ? OR UserID = 1) AND Name = ?");
    $stmt->bind_param("is",$_SESSION["ID"],$exerName);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows != 0){ //if a result is found then return the id that the exercise has
      while($row = $result->fetch_assoc()){
        $id = $row["ExerciseID"];
      }
      return $id;
    }
    else{
      return false;
    }
  }

  function exportSession($exercise){
    global $date,$amountUnits,$timeUnits;
    include "connectToDB.php";
    if ($exercise["type"] == "amount"){ //prepares set up for an amount type exercise
      if (!(ucwords($exercise["unit"]) == "Kilograms")){ //if the unit is not the default (kg)
        $exercise["weight"] *= $amountUnits[$exercise["unit"]]; //this converts the amount into kg if it is not already
        $exercise["weight"] = round($exercise["weight"],5);

      }
      $stmt = $mysqli->prepare("INSERT INTO sessions (UserID,ExerciseID,SessionDate,Reps,Amount) VALUES(?,?,?,?,?)"); //machine id is not included beacuse you can not specify the machine when inserting on the web because it provides no useful data to the gym
      $stmt->bind_param("iisid", $_SESSION["ID"],$exercise["localID"],$date,$exercise["reps"],$exercise["weight"]);
    }
    else if ($exercise["type"] == "time"){ //prepares set up for an amount type exercise
      if (!(ucwords($exercise["unit"]) == "Seconds")){ //if the unit is not the default (seconds)
        $exercise["time"] *= $timeUnits[$exercise["unit"]]; //this converts the amount into seconds if it is not already
        $exercise["time"] = round($exercise["time"],5);
      }
      $stmt = $mysqli->prepare("INSERT INTO sessions (UserID,ExerciseID,SessionDate,Amount) VALUES(?,?,?,?)"); //machine id is not included beacuse you can not specify the machine when inserting on the web because it provides no useful data to the gym
      $stmt->bind_param("iisd", $_SESSION["ID"],$exercise["localID"],$date,$exercise["time"]);
    }
    $stmt->execute();
    $stmt->close();
  }


  $validSessionArray = [];


  for ($i=0; $i < sizeof($sessionArray); $i++) { //loop for each object in the session array
    $sessionArray[$i]["localID"] = getExerciseID($sessionArray[$i]["name"]);
    if ($sessionArray[$i]["type"] == "amount"){ //if the exercise is an amount exercise
      if ((ctype_digit($sessionArray[$i]["reps"])) && ($sessionArray[$i]["reps"] <= 99999999999) && (ctype_digit($sessionArray[$i]["weight"])) && ($sessionArray[$i]["weight"] <= 2000000) && (array_key_exists(ucwords($sessionArray[$i]["unit"]), $amountUnits)) && ($sessionArray[$i]["localID"] != false)) {
          array_push($validSessionArray,true);//adds a true value to the valid session array
        }
        else{
          array_push($validSessionArray, false); //adds a false value to the valid session array
        }
      }
    else if($sessionArray[$i]["type"] == "time"){ //if the exercise is a time exercise
      if ((ctype_digit($sessionArray[$i]["time"])) && ($sessionArray[$i]["time"] <= 20000000) && (array_key_exists(ucwords($sessionArray[$i]["unit"]), $timeUnits)) && ($sessionArray[$i]["localID"] != false)) {
        array_push($validSessionArray,true);//adds a true value to the valid session array
      }
      else{
        array_push($validSessionArray, false);//adds a false value to the valid session array
      }
    }
    else{ //if the exercise type is not time or amount
      echo(json_encode("invalid type"));
      exit;
    }
  }

    if (in_array(false,$validSessionArray)){ //if there is one or more false value in the array
      echo(json_encode("invalid input")); //there was an issue with one or more of the objects therefore can not be sent to the database
      exit;
    }
    else{ //if there are no falses in the array that means that everything is valid
      for ($i=0; $i < sizeof($sessionArray); $i++) { //export to the database for the objects in the sessionArray
        exportSession($sessionArray[$i]);
      }
      echo(json_encode("success"));
      exit;
    }

?>
