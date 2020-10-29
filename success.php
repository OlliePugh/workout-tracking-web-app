<?php
include_once "phpScripts/isLoggedIn.php";
$accepted_types = ["session add","exercise add"];
$type = $_GET["system"];
if (!in_array($type,$accepted_types)){
  header("location: 404.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Dashboard</title>
    <?php include "phpScripts/favicon.html" ?>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <link rel="stylesheet" type="text/css" href="css/success.css"/>
    <script src="jsScripts/jQuery.js"></script>
    <script src="jsScripts/navBar.js"></script>
    <meta charset="utf-8">
  </head>
  <body>

    <div class="main">
      <?php include "side-nav.php";?>
      <div id="success-message" style="text-align:center; position: relative; top: 50%; transform: translateY(-50%);"">
        <?php
          switch ($type) {
            case "session add":
              echo("<h1>Session succesfully created</h1>");
              echo("<h2>If you would like to add another session please follow the link to the <a href='inputSession.php'>Session Input Page</a></h2>");
              break;
              case "exercise add":
                echo("<h1>Exercise succesfully created</h1>");
                echo("<h2>If you would like to create another exercise please follow the link to the <a href='exerciseMaker.php'>Exercise Maker Page</a></h2>");
                break;
            default:
              echo("Unrecognised System");
            }
        ?>
      </div>
    </div>
  </body>
</html>
