<!DOCTYPE html>
<?php
include "phpScripts/isLoggedIn.php";
?>
<html>
  <head>
    <?php include "phpScripts/favicon.html"; ?>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/makeExercise.css">
    <script type="text/javascript" src="jsScripts/jQuery.js"></script>
    <script type="text/javascript" src="jsScripts/getMachine.js"></script>
  </head>
  <body>
    <?php include "side-nav.php";?>
    <div class="main">
      <div class="central-div">
        <div id="input-wrapper">
          <h1 style="padding-top: 0;">Exercise Maker</h1>
          <form action="phpScripts/createExercise.php" method="post">
            <p class="input-header">Exercise Name*</p>
            <input id="nameInput"style="cursor: text;" value="<?php if (array_key_exists("exercise", $_GET)) echo($_GET["exercise"]); ?>"class="default-button" type="text" name="name"></input>
            <p id="nameInvalid" class="invalidInput">Please enter a valid exercise name.</p>
            <p id="exerciseExists" class="invalidInput">You already have an exercise with that name.</p>
            <p class="input-header">Type of Exercise*</p>
            <select id="typeInput" style="cursor: pointer; box-sizing: content-box" class="default-button" type="text" name="type">
              <option value="amount">Reps and Weight Performed</option>
              <option value="time">Time Performed</option>
            </select>
            <p id="typeInvalid" class="invalidInput">Please enter a valid unit.</p></br>
            <p class="input-header">Machine Name: <span style="font-size: 8pt;">(Optional)</span></p><div id="machine-name-manage" class="display: hidden"><span id="machine-name" style="font-size: 16pt;">Machine Name Placeholder</span><span id="machine-change" style="font-size: 8pt; color: var(--light-bg-color); text-decoration: underline; cursor: pointer;">(Change)</span></div>
            <div id="machine-selector">
              <input class="default-button" style="cursor: text; margin-bottom: 10px;" placeholder="Search for machine" id="machineInput" type="text" name="machineName"></input>
              <table id="machineTable" class="machines" style="margin: 0 auto; height: 100px; overflow-y: auto; display: inline-block;">
                <thead>
                  <tr>
                    <th style="padding-bottom: 2px;">Machines</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
            <button class="submitButtons default-button" id="createButton" type="button" style="margin-top: 10px; padding: 10px; display:block; margin: 10px auto 0 auto;"> Create Exercise</button>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
