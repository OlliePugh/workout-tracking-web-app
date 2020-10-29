<?php // TODO: THIS WHOLE CODE NEEDS REWRITING ITS A MESS
function getIP(){ //CODE FROM http://itman.in/en/how-to-get-client-ip-address-in-php/
  if (!empty($_SERVER['HTTP_CLIENT_IP'])){
    $ip_address = $_SERVER['HTTP_CLIENT_IP'];
  } //whether ip is from proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  {
      $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }//whether ip is from remote address
    else{ $ip_address = $_SERVER['REMOTE_ADDR'];}
      return $ip_address;
}

function endCheck(){ //this is to stop allowing someone access whe when they should not have it
  if (empty($_SESSION["name"]) || empty($_SESSION["ID"])){
    header("Location: ../login.php");
    exit;
  }
}

session_start(); #this starts the session to recieve all of the variables bound to the session

if (isset($_SESSION["ID"]) && isset($_SESSION["name"])){ #this checks to see if the session variables are set, if they are, the user must be logged in
  endCheck(); //
  return true;
}

if (isset($_COOKIE["sessionID"])){ #run if the cookie is set
  include "connectToDB.php"; #connect to the database
  $sessID = trim($_COOKIE["sessionID"]); //trims the value for the sessionID
  $ipHash = trim(md5(getIP())); //trims ther users ip
  $stmt = $mysqli->prepare("SELECT Email FROM login_log WHERE RememberMe = 1 AND Result = 1 AND LoginHash = ? AND IPHash = ?"); //getting the users email from the database
  $stmt->bind_param("ss",$sessID,$ipHash);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
  if ($result->num_rows != 0){ //if a result is found for the users login hash and ip hash
    while($row = $result->fetch_assoc()){
      $email = $row["Email"]; //set the users email to the value email
    }
  }
    include "connectToDB.php";
    $stmt = $mysqli->prepare("SELECT UserID, FirstName, Surname FROM users WHERE Email = ?"); #Gets the users UserID and Name
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    while($row = $result->fetch_assoc()){
      $_SESSION["ID"] = $row["UserID"]; #creates session variables
      $_SESSION["name"] = ($row["FirstName"]." ".$row["Surname"]);
    }
    $cookieName = "sessionID";
    setcookie($cookieName,"",time()-3600,"/"); #this removes the old cookie
    setcookie($cookieName,$sessID,time()+86400*30,"/"); #and resets the timer of 30 days

    endCheck();
    return true;
  }
  else{
    $cookieName = "sessionID";
    setcookie($cookieName,"",time()-3600,"/"); //sets expiry in the past
  }
if (!(isset($_SESSION["ID"])) || !(isset($_SESSION["name"]))){ //final check to ensure that now the variables have been setup that the user has succesfully logged into an account
  endCheck();
  exit(); #this stops the php script from continuing
}
?>
