<?php

if (isset($_POST["email"])){
  $email = strtolower($_POST["email"]);
}
else{
  header("location:../404.html");
}

include "../phpScripts/connectToDB.php";

$stmt = $mysqli->prepare('SELECT timeOfCreation
                          FROM users
                          WHERE email = ?');
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows == 0){
  header("location: email-sent.php"); //to make it seem as if an email was sent
  exit;
}

while($row = $result->fetch_assoc()){
  $token = md5($row["timeOfCreation"]);
}

$subject = "Password Recovery";
$message = "Click link to reset password: https://www.flex-fit.co.uk/reset-password/processing.php?email=".$email."&token=".$token;
$headers = "Content-Type: text/html; charset=UTF-8\r\n";  // (https://stackoverflow.com/questions/11238953/send-html-in-email-via-php)

mail($email, $subject, $message);
header("location: email-sent.php");
exit;

?>
