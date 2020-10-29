<?php
$val = $_POST["value"];
include "../phpScripts/connectToDB.php";
$stmt = $mysqli->prepare('SELECT * FROM users WHERE Email = ?');
$stmt->bind_param("s",$val);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
if ($result->num_rows == 0){
  echo(json_encode("Free"));
}
else{
  echo(json_encode("Taken"));
}
?>
