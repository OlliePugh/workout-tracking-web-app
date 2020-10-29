<div class="tile-content-wrapper">
  <h1 style="margin-top: 0px;" class="tile-top">So far you have lifted a total of</h1>
  <h1 class ="tile-data" style="text-align:center;">
    <?php

    $userID = $_SESSION["ID"];

    include "phpScripts/connectToDB.php"; //needs to as if coming from directory of the page its being displayed on

    $stmt = $mysqli->prepare('SELECT SUM(sessions.Amount) AS Total
                              FROM sessions
                              INNER JOIN exercises ON (sessions.ExerciseID=exercises.ExerciseID)
                              WHERE sessions.UserID = ? AND exercises.Type = "Amount"');

    $stmt->bind_param("i",$userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $mysqli->close();

    while($row = $result->fetch_assoc()){
      echo(round($row["Total"],2));
    }

    ?>

    KG</h1>
    <h3 style="text-align:right;" class="tile-bottom" >Keep up the good work!</h3>
</div>
