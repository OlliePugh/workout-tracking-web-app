<div class="tile-content-wrapper">
  <h1 style="margin-top: 0px;" class="tile-top">So far you have done</h1>
  <h1 class ="tile-data" style="text-align:center;">
    <?php

    $userID = $_SESSION["ID"];

    include "phpScripts/connectToDB.php"; //needs to as if coming from directory of the page its being displayed on

    $stmt = $mysqli->prepare('SELECT COUNT(*) AS Total
                              FROM sessions
                              WHERE UserID = ?');

    $stmt->bind_param("i",$userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $mysqli->close();

    while($row = $result->fetch_assoc()){
      echo(round($row["Total"],2));
    }

    ?></h1>

    <h1 style="text-align:right;" class="tile-bottom">Sets!</h1>
</div>
