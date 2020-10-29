<!DOCTYPE html>
<?php
include_once "phpScripts/isLoggedIn.php";
?>
<html lang="en">
  <head>
    <?php include "phpScripts/favicon.html"; ?>
    <link rel="stylesheet" type="text/css" href = "css/style.css"/>
    <link rel="stylesheet" type="text/css" href = "css/graphs.css"/>
    <script type="text/javascript" src="jsScripts/jQuery.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <!--Date Picker-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="jsScripts/createGraph.js"></script>
    <script type="text/javascript" src="jsScripts/graphInput.js"></script>
    <script>
    $('.datepicker').datepicker();
    $( function() {
      $( ".datepicker" ).datepicker({ dateFormat: "dd-mm-yy" }); //taken from https://jqueryui.com/datepicker/
    });
    </script>
    <!-------------------->
    <meta charset="utf-8">
  </head>
  <body style="min-height: 325px;">
    <?php include "side-nav.php";?>
    <div class="main">
      <div id="top-div">
        <div id="graphContainer" style="min-height: 205px; height: 100%;">
            <p id="noGraphText">Please choose a graph to display</p>
            <div id="chart_div" style="height: 100%; width: 100%;">
            </div>
        </div>
      </div>
      <div id="bottom-div" style="min-height: 136px;">
        <div id="bottom-left-div">
          <div id="bottom-left-wrapper">
            <div id="exerciseNameContainer" style="padding: 20px 0;">
              <input id="exerciseSearch" style="display: block; margin: 0 auto;" placeholder="Exercise"></input>
              <ul id="exerciseNames" style="margin-bottom: 0;">
              </ul>
              <div id="spotlight" style="text-align: center;">
                <div class="option">
                  <select id="amountUnitInput" name="unit" class="hidden unitInputs default-button"><br>
                  </select>
                  <select id="timeUnitInput" name="unit" class="hidden unitInputs default-button"><br>
                  </select>
                </div>
                <div class="option">
                  <select id="graphDataTypeSelect" name="graphDataType" class="hidden default-button">
                    <option value="maxAmount">Max Amount Performed</option>
                    <option value="maxReps">Max Reps Performed</option>
                  </select>
                </div>
                <div id="timeConstraintInput" class="hidden">
                  <div id="graph-date-inputs">
                    <div class="option">
                      <p class="time-labels">Start Date: </p>
                      <input type="text" autocomplete="<?php echo(uniqid())?>" class="datepicker default-button" id="earliest" style="width: 80px;"/>
                    </div>
                    <div class="option">
                      <p class="time-labels" >End Date: </p>
                      <input type="text" autocomplete="<?php echo(uniqid())?>" class="datepicker default-button" id="latest" style="width: 80px;"/>
                    </div>
                  <p id="invalidDateText" class="hidden invalidInput ned" style="margin: 5px auto;width: 80%;">InvalidDatemessage</p>
                </div>
              </div>
              <button style="margin: 0 auto;" class="hidden default-button" id="changeExercise">Change Exercise</button>
            </div>
          </div>
        </div>
      </div>
        <div id="bottom-right-div">
          <h1 id="exerciseName" class="hidden">Exercise Name</h1>
        </div>
      </div>
    </div>
  </body>
</html>
