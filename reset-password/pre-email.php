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
        <h1 style="color: white; margin: 5px 0;">Reset Password</h1>
        <p>Enter your email address that you signed up with and we will send you a link to reset your password.</p>
        <form action="send-pass-reset.php" method="post">
          <p class="input-title" style="margin-top: 5px;">Email Address</p>
          <input id="login" type="text" class="default-button has-transition input-box" name="email"/><br>
          <button id="submit-button" type="submit" class="default-button has-transition " style="padding: 10px 20px; font-size: 12pt; margin-top: 20px;">Send</button>
        </form>
      </div>
    </div>
  </body>
</html>
