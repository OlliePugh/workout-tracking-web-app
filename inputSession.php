<?php
include "phpScripts/isLoggedIn.php";
?>
<html id="inputSession">
  <head lang="en">
    <?php include "phpScripts/favicon.html" ?>
    <script type="text/javascript" src="jsScripts/jQuery.js"></script>
    <script type="text/javascript" src="jsScripts/sessionInput.js"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/inputSession.css">
    <!--Date Picker-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    $( function() {
      $( "#datepicker" ).datepicker({ dateFormat: "dd-mm-yy" }); //taken from https://jqueryui.com/datepicker/
    });
    </script>
  <!----------------->
    <meta charset="utf-8">
  </head>
  <body>
    <?php include "side-nav.php";?>
      <div class="main">
        <?php
          if (isset($_GET["state"]) && ($_GET["state"] == "success")){
            echo("<p>Successfully added Session");
          }?>
      <div id="lhs">
        <p id="date-name">Date of Session</p>
        <div id="date-div">
          <input autocomplete="off" type="search" autocomplete="off" id="datepicker" name="date" placeholder="Date" class="default-button"><br>
          <p class="hidden invalidInput" id="dateInvalid">Please enter a valid date</p>
        </div>
        <div id="lhs-wrapper">
          <div id="spotlight">
            <h1 id="exerciseName" class="hidden">Exercise Name</h1>
            <div id="amountTypeInput" class="hidden spotLightInputs">
              <table class="spotlight-div" style="margin-bottom: 20px;">
                <tbody>
                  <tr>
                    <td>
                      <p>Reps:</p>
                    </td>
                    <td>
                      <input autocomplete="off" id="repInput" type="number"></input>
                    </td>
                  </tr>
                  <p class="hidden invalidInput" id="repInvalid">Please enter a valid rep value.</p>
                  <tr>
                    <td>
                      <p>Weight:</p>
                    </td>
                    <td>
                      <input autocomplete="off" id="weightInput" type="number"></input>
                    </td>
                  </tr>
                  <p class="hidden invalidInput" id="weightInvalid">Please enter a valid weight value.</p>
                  <tr>
                    <td>
                      <p>Unit:</p>
                    </td>
                    <td>
                      <select style="width: 100%;" id="amountUnitInput" type="text"></select>
                    </td>
                  </tr>
                  <p class="hidden invalidInput" id="amountUnitInvalid">Please enter a valid unit value.</p>
                  <tr>
                    <td>
                      <p>Sets:</p>
                    </td>
                    <td>
                      <input autocomplete="off" class="setsInput" id="amountSetsInput" type="number" value=1></input><br>
                    </td>
                  </tr>
                  <p class="hidden invalidInput" id="amountSetsInvalid">Please enter a valid set value.</p>
                </tbody>
              </table>
            </div>
            <div id="timeTypeInput" class="hidden spotLightInputs">
              <table class="spotlight-div" style="margin-bottom: 20px;">
                <tbody>
                  <tr>
                    <td>
                      <p>Time:</p>
                    </td>
                    <td>
                      <input autocomplete="off" id="timeDoneInput" type="number"></input>
                    </td>
                  </tr>
                  <p class="hidden invalidInput" id="timeInvalid">Please enter a valid time value.</p>
                  <tr>
                    <td>
                      <p>Unit:</p>
                    </td>
                    <td>
                      <select style="width: 100%;" id="timeUnitInput"></select>
                    </td>
                  </tr>
                  <p class="hidden invalidInput" id="timeUnitInvalid">Please enter a valid unit value.</p>
                </tbody>
              </table>
            </div>
              <button class="default-button hidden spotlight-button" id="changeExercise" type="button">Change Exercise</button>
              <button class="default-button hidden spotlight-button" id="addToSession" type="button" style="margin-top: 20px;">Add to Session</button>
            <input autocomplete="off" id="nameInput" type="text" name="name" autocomplete="off" placeholder="Exercise Name"></input>
            <div id="table-wrapper" style="height: 312px; overflow-y: auto; overflow-x:hidden; margin-top: 5px;">
              <table id="exerciseTable" class="exercises">
                <thead>
                  <tr>
                    <th>Exercises</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
            <br>
            <p class="hidden invalidInput" id="phpValidationFalse">Oops, something went wrong when adding your session.</p>
          </div>
        </div>
        <button class="default-button" id="submitSession" style="display: block; margin: 20px auto 0 auto;" type="button">Done</button>
      </div>
      <div id="rhs">
        <div id="rhs-wrapper">
          <h1 style="text-align:center">Session</h1>
          <ul id="sessionList">
          </ul>
        </div>
      </div>
    </div>
  </body>
</html>
