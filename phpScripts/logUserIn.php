<?php
$email=$_POST["emailAddress"];
$password=$_POST["password"];
$remember = (integer)$_POST["rememberMe"];

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
  function redirect($response, $user,$name){ //this function will send data to the sql log and
    global $email;
    global $remember;
    $loginHash = uniqid(); //sets the login has to the md5 of the microtime
    $ipHash = md5(getIP()); //hashes the users ip
    $time = date('Y-m-d H:i:s');
    if ($response){$result = 1;} //converts the value of $response to a bit for the query
    else{$result = 0;}

    include "connectToDB.php"; //establishes the connection to the database
    if ($email != null){
      $stmt = $mysqli->prepare('INSERT INTO login_log (Email,LoginHash,IPHash,RememberMe,Time,Result) VALUES (?,?,?,?,?,?)');
      $stmt->bind_param("sssisi",$email,$loginHash,$ipHash,$remember,$time,$response);
    }
    else{
      $stmt = $mysqli->prepare('INSERT INTO login_log (LoginHash,IPHash,RememberMe,Time,Result) VALUES (?,?,?,?,?)');
      $stmt->bind_param("ssisi",$loginHash,$ipHash,$remember,$time,$response);
    }
    $stmt->execute();
    $stmt->close();

    if ($response === true){
      session_start();
      $_SESSION["ID"] = $user;
      $_SESSION["name"] = $name;
      if ($remember == 1){
        $cookieName = "sessionID";
        setcookie($cookieName, $loginHash, (time()+86400*30),"/");
      }
      header("Location: ../index.php");
      exit;
    }

    elseif($response === "unverified"){
      echo("unverified");
      header("Location: ../account-verification/email-sent.php?email=".$email);
      exit;
    }

    elseif($response === false){
      echo("rejected");
      header("Location: ../login.php?state=rejected");
      exit;
    }

    else{
      echo("unknown response");
      exit;
    }
  }

    if (empty($email) || empty($password) && ($remember == 0 || $remember == 1)){ //if email or password are blank or if the rememberme checkbox has been tampered with
      $email = null;
      redirect(false,null,null); //the end result is that the login was a failure
    }
    else{
      include "connectToDB.php";
      $stmt = $mysqli->prepare('SELECT passwords.UserID, passwords.hashedPass, users.FirstName, users.Surname FROM passwords JOIN users ON (users.UserID=passwords.UserID) WHERE Email = ?');
      $stmt->bind_param("s",$email);
      $stmt->execute();
      $result = $stmt->get_result();
      $stmt->close();
      if ($result->num_rows > 1){
        echo("ERROR: More than one email found");
        redirect(false,null,null);
      }
      else{
        if ($result->num_rows != 0){
          while($row = $result->fetch_assoc()){
            $userID = $row["UserID"];
            $realPass = $row["hashedPass"];
            $Name = ($row["FirstName"]." ".$row["Surname"]);
            if ($realPass == md5($password)){
              //CHECK IF THE USER IS VERIFIED
              include "connectToDB.php";
              $stmt = $mysqli->prepare('SELECT * FROM unverified_users
                                        WHERE UserID = ?');
              $stmt->bind_param("i",$userID);
              $stmt->execute();
              $result = $stmt->get_result();
              $stmt->close();
              if ($result->num_rows > 0){ //that user id is unverified
                redirect("unverified",null,null);
              }
              else{
                redirect(true,$userID,$Name);
              }
            }
            else{
              redirect(false,null,null);
            }
          }
        }
      else{
        redirect(false,null,null);
      }
    }
  }
?>
