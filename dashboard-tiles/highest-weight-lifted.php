<div class="tile-content-wrapper">
  <h1 style="margin-top: 0px;" class="tile-top">The most you have ever lifted is</h1>
  <h1 class ="tile-data" style="text-align:center;">
    <?php

    $userID = $_SESSION["ID"];

    include "phpScripts/connectToDB.php"; //needs to as if coming from directory of the page its being displayed on

    $stmt = $mysqli->prepare('SELECT sessions.amount AS maxLift, exercises.Name
                              FROM sessions
                              JOIN exercises
                              ON (sessions.ExerciseID = exercises.ExerciseID)
                              WHERE sessions.UserID = ? AND exercises.Type = "amount"
                              ORDER BY sessions.amount DESC
                              LIMIT 1;');

    $stmt->bind_param("i",$userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $mysqli->close();

    while($row = $result->fetch_assoc()){
      $maxLift = round($row["maxLift"],2);
      $exerciseName = $row["Name"];
    }

    echo($maxLift);

    ?>KG</h1>

    <h1 style="text-align:right;" class="tile-bottom">doing <?php echo($exerciseName); ?>!</h1>
</div>
