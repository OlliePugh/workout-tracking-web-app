<!DOCTYPE html>
<?php
  session_start();
  if (isset($_SESSION["ID"]) && isset($_SESSION["name"])){
    header("Location: ../index.php");
    exit;
  }

  if (!isset($_SESSION["resetPassID"])){ //stop people from going back on the brower and not having all the correct session variables
    header("location: email-sent.php");
    exit;
  }
?>
<html lang="en">
  <head>
    <title>Flex-Fit</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/login.css"> <!--Has very similair setup to the login page -->
    <script type="text/javascript" src="../jsScripts/jQuery.js"></script>
    <script type="text/javascript" src="../jsScripts/password-strength.js"></script>
  </head>
  <body>
    <div style="text-align: center; position: relative; top: 50%; transform: translateY(-50%);">
      <div id="wrapper">
        <a href="../index.php"><img src="../css/images/logo-with-text.png" style="height:100px; margin-bottom: 10px;" alt="Flex-Fit logo"></a>
        <h1 style="color: white; margin: 5px 0;">Reset Password</h1>
        <form id="reset-form" action="reset-password.php" method="post">
          <p class="input-title" style="margin-top: 5px;">New Password</p>
          <input id="new-password" type="password" autocomplete="new-password" class="default-button has-transition input-box" name="password"/><br>
          <p class="input-title" style="margin-top: 5px;">Confirm New Password</p>
          <input id="confirm-password" type="password" class="default-button has-transition input-box" name="conf-password"/><br>
          <p id="pass-error" class="invalidInput hidden">Please enter a new password</p>
          <input id="submit-button" type="button" class="default-button has-transition" value="Reset Password" style="padding: 10px 20px; font-size: 12pt; margin-top: 20px;">
        </form>
      </div>
    </div>
    <script>

      $( document ).ready(function(){
        $("#submit-button").click(function(event){

          let passVal = $("#new-password").val();
          let confirmVal = $("#confirm-password").val();

          let passwordValid = checkPassword(passVal);

          if(passwordValid === true && (passVal === confirmVal)){ // needs to be === to true because a string is truthy
            $("#pass-error").fadeOut();
            $("#reset-form").submit();
          }
          else if (passVal != confirmVal) {
            $("#pass-error").fadeIn();
            $("#pass-error").text("Passwords do not match");
            //passwords do not match
          }
          else{
            $("#pass-error").fadeIn();
            $("#pass-error").text(passwordValid);
            //password is bad
          }
        })
      });
    </script>
  </body>
</html>
