<?php
  if (!isset($_GET["email"])){ // if email is not specified
    header("Location: ../404.html");
    exit;
  }

  $email = $_GET["email"];
  include "../phpScripts/connectToDB.php";
  $stmt = $mysqli->prepare('SELECT unverified_users.Token
                            FROM unverified_users
                            JOIN user ON user.UserID=unverified_users.UserID
                            WHERE user.Email = ?;');
  $stmt->bind_param("s",$email);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  if ($result->num_rows === 0){ //user is not in unverified
    header("Location: ../404.html");
    exit;
  }

  while($row = $result->fetch_assoc()){
    $token = $row["Token"]; //set $token to the response of the query
  }

  $sender = "verify-email@flex-fit.co.uk";
  $subject = "Authenticate Account";
  $message = "Click link to authenticate account: https://www.flex-fit.co.uk/account-verification/verify-account.php?token=".$token;
  $headers = 'From:' . $sender;
  //$headers = "Content-Type: text/html; charset=UTF-8\r\n";  // (https://stackoverflow.com/questions/11238953/send-html-in-email-via-php)

  mail($email, $subject, $message, $headers);

  header("Location: email-sent.php?email=".$email);
?>
