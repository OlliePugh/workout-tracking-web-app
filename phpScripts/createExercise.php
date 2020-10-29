<?php
include "isLoggedIn.php"; //makes sure the user is logged in
$name = $_POST["name"];
$name = strip_tags($name,"<script>");  //protection against XSS Attack
$type = $_POST["type"];
$machineName = $_POST["machineName"];
if (empty($machineName)){
  $machineName = null;
}
$types = ["amount","time"];

function validText($val,$min,$max){ //validates text to make sure it only contains text CURRENTLY REJECTING ANYTHING WITH A SPACE IN
  return (!empty($val) && ((strlen($val) <= $max && strlen($val) >= $min)) && (ctype_alpha(str_replace(array("\n", "\t", ' '), '', $val))));
}

function textInArray($val,$arr){
  trim($val);
  return(in_array($val,$arr));
}

function machineValid(){ //validates that the machine is setup currectly
  global $machineName;

  if(empty($machineName)){ //a machine is not required therefore if it is empty it will return true
    return true;
    echo("<br>MACHINE NAME IS EMPTY</br>");
  }

  include "connectToDB.php"; //connects to the db
  $stmt = $mysqli->prepare("SELECT Name FROM equipment WHERE Name = ?");
  $stmt->bind_param("s",$machineName);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  if ($result->num_rows != 0){ //makes sure the machine is in the database and is a real machine
    echo("<br>MACHINE IS FOUND");
    return true;
  }
  else{
    echo("<br>MACHINE IS NOT FOUND");
    return false;
  }
}

if(machineValid() && !empty($name) && textInArray($type,$types)){ #Check if exercise has already been made by the user in the database
  include "connectToDB.php";
  if (empty($machineName)){
    $stmt = $mysqli->prepare("SELECT Name FROM exercises WHERE Name = ? AND UserID = ?");
    $stmt->bind_param("si",$name,$_SESSION["ID"]);
  }
  else{
    $stmt = $mysqli->prepare("SELECT Name FROM exercises WHERE Name = ? AND UserID = ? AND MachineName = ?");
    $stmt->bind_param("sis",$name,$_SESSION["ID"],$machineName);
  }
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  if($result->num_rows == 0){
    echo("<br>NO RESULT FOUND");
    include "connectToDB.php";
    $stmt = $mysqli->prepare("INSERT INTO exercises (Name,UserID,Type,MachineName)VALUES (?,?,?,?)");
    $stmt->bind_param("siss",$name,$_SESSION["ID"],$type,$machineName);
    $stmt->execute();
    $stmt->close();
    echo("<br>successfuly entered into db");
    redirect(true);
  }
  else{
    echo("ALREADY IN DB");
    redirect(false);
  }
}
else{
  echo("INPUTS NOT VALID");
  redirect(false);
}
echo("name is ".$name);

function redirect($result){
  if($result){
    //validation was correct, send to success page
    header("Location: ../success.php?system=exercise%20add");
  }
  else{
    //an issue occured, send error
    header("Location: ../404.html");
  }
}
?>
