<?php
session_start();

if(!isset($_SESSION["resetPassID"])){
  header("location: email-sent.php");
  exit;
}

if(!isset($_POST["password"]) || !isset($_POST["conf-password"])){
  header("location:../404.html");
  exit;
}
$UserID = $_SESSION["resetPassID"];
$pass = $_POST["password"];
$confPass = $_POST["conf-password"];

include "../phpScripts/password-strength.php";

$passwordValid = checkPassword($pass);

if(($passwordValid != true) || ($pass != $confPass)){ // needs to be === to true because a string is truthy
  header("location:../404.html"); //redirect as an error has occured
  exit;
  //password is bad
}

$hashedPass = md5($pass);

include "../phpScripts/connectToDB.php";

$stmt = $mysqli->prepare('UPDATE passwords SET hashedPass = ?
                          WHERE UserID = ?;');
$stmt->bind_param("si",$hashedPass,$UserID);
$stmt->execute();
$stmt->close();

session_destroy();

echo("success");

header("location: password-success-reset.php");
exit;
?>
