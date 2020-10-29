<?php
$token = $_GET["token"];
include "../phpScripts/connectToDB.php";
$stmt = $mysqli->prepare('SELECT *
                          FROM unverified_users
                          WHERE Token = ?');
$stmt->bind_param("s",$token);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
if ($result->num_rows === 0){ //user is already verified or has not made an account with that token
  header("Location: ../404.html");
  exit;
}

//this means that a value has been found in the table that has that token
include "../phpScripts/connectToDB.php"; //create connection
$stmt = $mysqli->prepare("DELETE
                          FROM unverified_users
                          WHERE Token = ?");
$stmt->bind_param("s",$token); //bind variables
$stmt->execute();
$result = $stmt->get_result();
$stmt->close(); //close connection
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Flex-Fit</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/login.css"> <!--Has very similair setup to the login page -->
  </head>
  <body>
    <div style="text-align: center; position: relative; top: 50%; transform: translateY(-50%);">
      <div id="wrapper">
        <a href="../index.php"><img src="../css/images/logo-with-text.png" style="height:100px; margin-bottom: 10px;" alt="Flex-Fit logo"></a>
        <h1 style="color: white; margin: 5px 0;">Succesfully Verified Account</h1>
        <p>Please <a href="../login.php" style="color: var(--accent-color);">login</a> to access your account.</p>
      </div>
    </div>
  </body>
</html>
