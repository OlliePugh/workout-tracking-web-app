<!DOCTYPE html>
<?php
  session_start();
  if (isset($_SESSION["ID"]) && isset($_SESSION["name"])){
    Header("Location: index.php");
  }
?>
<html lang="en" id="login-page"> <!--Something causing issues making things render in black and change to white -->
  <head>
    <title>Flex-Fit</title>
    <?php include "phpScripts/favicon.html"; ?>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
  </head>
  <body>
    <div style="text-align: center; position: relative; top: 50%; transform: translateY(-50%);">
      <div id="wrapper">
        <a href="index.php"><img src="css/images/logo-with-text.png" style="height:100px; margin-bottom: 10px;" alt="Flex-Fit logo"></a>
        <h1 style="color: white; margin: 5px 0;">Login</h1>
        <form action="phpScripts/logUserIn.php" method="post">
          <p class="input-title">Email Address</p>
          <input id="login" type="text" class="default-button has-transition input-box" name="emailAddress"><br>
          <p class="input-title">Password</p>
          <input id="password" type="password" class="default-button has-transition input-box" name="password"><br>
          <div style="margin: 10px 0;">
            <p style="display: inline; font-size: 15pt;">Remember Me</p><input type="hidden" name="rememberMe" value="0"><input id="rememberMe" type="checkbox" name="rememberMe" value="1"><br>
          </div>
          <?php
          if (isset($_GET["state"])){
            if ($_GET["state"] == "rejected"){
              echo("<p>Please check all credentials</p>");
            }
          }
          ?>
          <a href="reset-password/pre-email.php" id="reset-pass-link">Forgot your password?</a>
          <button id="submit-button" type="submit" class="default-button has-transition " style="padding: 10px 20px; font-size: 12pt;">Login</button>
        </form>
        <p id="invalid" class="invalidInput">Incorrect username or password.</p>
        <a href="signup.php" id="sign-up-link">Sign Up</a>
      </div>
    </div>
  </body>
</html>
