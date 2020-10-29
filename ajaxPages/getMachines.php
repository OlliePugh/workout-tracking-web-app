<?php
include "../phpScripts/isLoggedIn.php";
$val = $_POST["value"]; #get the value from the exerciseMaker form
$machines= []; #create a blank array
include "../phpScripts/connectToDB.php"; #connect to the db
if (!empty($val)){ #if the value is not empty it means that the user wants to search for a machine
  $val = ($val."%");
  $stmt = $mysqli->prepare('SELECT DISTINCT Name FROM equipment WHERE Name LIKE ? ORDER BY Name;');
  $stmt->bind_param("s",$val);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
}
else{ #this means that the user entered nothing, and therefore should display all of the available machines
  $result = $mysqli->query('SELECT DISTINCT Name FROM equipment ORDER BY Name;');
  $mysqli->close();
}
while($row = $result->fetch_assoc()){
  array_push($machines, $row["Name"]); #append each machine found to the array
}
echo(json_encode($machines)); #send the array to the current page
?>
