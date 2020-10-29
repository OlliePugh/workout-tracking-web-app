<?php
include_once "phpScripts/isLoggedIn.php";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Dashboard</title>
    <?php include "phpScripts/favicon.html"; ?>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <link rel="stylesheet" type="text/css" href="css/dashboard.css"/>
    <script src="jsScripts/jQuery.js"></script>
    <script src="jsScripts/navBar.js"></script>
    <meta charset="utf-8">
  </head>
  <body>
    <?php include "side-nav.php";?>
    <div class="main">
      <div id="dashboard">
        <div class="tile-wrapper">
          <div class="tile">
            <?php
              include "dashboard-tiles/total-weight.php";
            ?>
          </div>
        </div>
        <div class="tile-wrapper">
          <div class="tile">
            <?php
              include "dashboard-tiles/total-sets.php";
            ?>
          </div>
        </div>
        <div class="tile-wrapper">
          <div class="tile">
            <?php
              include "dashboard-tiles/highest-weight-lifted.php";
            ?>
          </div>
        </div>
        <div class="tile-wrapper">
          <div class="tile">
            <?php
              include "dashboard-tiles/total-time.php";
            ?>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
