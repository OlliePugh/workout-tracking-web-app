<?php
session_start();
session_destroy();

if (isset($_GET["email"]) && isset($_GET["token"])){
  $email = strtolower($_GET["email"]);
  $token = strtolower($_GET["token"]);
}
else{
  header("location: ../404.html");
  exit;
}

include "../phpScripts/connectToDB.php";

$stmt = $mysqli->prepare('SELECT passwords.UserID, user.timeOfCreation
                          FROM passwords
                          JOIN user ON (passwords.UserID = user.UserID)
                          WHERE user.email = ?');
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows == 0){
  header("location: ../404.html"); //to make it seem as if an email was sent
  exit;
}

session_start();

while($row = $result->fetch_assoc()){
  $realToken = md5($row["timeOfCreation"]);
  $potentialID = $row["UserID"];
}

if ($realToken != $token){
  header("location: ../404.html"); //to make it seem as if an email was sent
  exit;
}
else{
  $_SESSION["resetPassID"] = $potentialID;
  header("location: post-email.php"); //to make it seem as if an email was sent
  exit;
}

?>
