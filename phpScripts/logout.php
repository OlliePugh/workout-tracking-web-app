<?php
session_start(); #this starts the session
session_destroy(); #this destroys all of the variables from the session therefore logging the user out
$cookieName = "sessionID";
if(isset($_COOKIE[$cookieName])){
  echo($_COOKIE[$cookieName]);
  echo("clearing");
  setcookie($cookieName,"",time()-3600,"/"); //sets expiry in the past
}
header("Location: ../login.php");
 ?>
