<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION["ID"]) && isset($_SESSION["name"])){
  Header("Location: index.php");
}
?>
<html id="signUp" lang="en">
  <head>
    <title>FlexFit</title>
    <?php include "phpScripts/favicon.html"; ?>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/signUp.css">
    <script src="jsScripts/jQuery.js"></script>
    <script src="jsScripts/password-strength.js"></script>
  </head>
  <body>
    <div id="wrapper" style="min-width: 375px; overflow: auto;">
      <a href="index.php"><img src="css/images/logo-with-text.png" style="height:100px; margin: 10px 0;" alt="Flex-Fit logo"></a>
      <form action="phpScripts/createAccount.php" method="post">
      <h1 class="center">Sign Up</h1>
        <div class="input">
          <p class="input-header">First Name*</p>
          <input name="fName" id="fName" autocomplete="given-name" class="validate" type="text" data-input-type="name" data-error="fNameInvalid" required>
          <p id="fNameInvalid" class="invalidInput">Please Enter a valid first name.</p>
        </div>
      <div class="input">
        <p class="input-header">Surname*</p>
        <input name="sName" id="sName" autocomplete="family-name" class="validate" type="text" data-input-type="name" data-error="sNameInvalid" required>
        <p id="sNameInvalid" class="invalidInput">Please Enter a valid surname.</p>
      </div>
      <div class="input">
        <p class="input-header">Email*</p>
        <input name="email" id="email" class="validate" type="text" data-input-type="email" data-error="emailInvalid" data-unique="Email" data-table="user" required>
        <p id="emailInvalid" class="invalidInput">Please Enter a valid email.</p>
      </div>
      <div class="input">
        <p class="input-header">Password*</p>
        <input name="password" id="pass" class="validate" type="password" data-input-type="newPassword" data-pass-confirm="passConfirm" data-error="passNoMatch" required><br>
        <p class="input-header">Confirm Password*</p>
        <input name="passwordConfirm" id="passConfirm" type="password" required>
        <p id="passNoMatch" class="invalidInput"></p>
      </div>
      <div class="validate input" id="genderRadio" data-radio-name="gender" data-input-type="radio" data-error="radioNoInput" data-required="true">
        <p class="input-header">Sex*</p>
        <input class="multiQuestion" autocomplete="sex" type="radio" name="gender" value="m">Male
        <input class="multiQuestion" autocomplete="sex" type="radio" name="gender" value="f">Female
        <input class="multiQuestion" autocomplete="sex" type="radio" name="gender" value="o">Other
        <p id="radioNoInput" class="invalidInput">Please choose a gender.</p>
      </div>
      <div class="input">
        <p class="input-header">Date of Birth*</p>
        <div id="date-input" data-error="dob-invalid" class="center validate" data-input-type="date" style="width: 90%; margin: 0 auto; text-align: left;">
            <input name="dobDay" id="dobDay" autocomplete="bday-day" style="width: 25%; padding: 1px 0; border-top-left-radius: 3px; border-bottom-left-radius: 3px; box-sizing: border-box;" class="validate dateInput date-input" type="number" placeholder="Day (DD)" data-input-type="number" data-min="1" data-max= "31" data-error="dobDayInvalid" required><select autocomplete="bday-month" name="dobMonth" id="dobMonth" style="width: 40%;" class="validate date-input" data-error="dobMonthInvalid"  required><option id="month-placeholder" value="" disabled selected>Month</option><option value="01">January</option><option value="02">February</option><option value="03">March</option><option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option><option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>
            </select><input name="dobYear" id="dobYear" style="box-sizing: border-box; width: 35%;padding: 1px 0; border-top-right-radius: 3px; border-bottom-right-radius: 3px;" class="validate dateInput date-input" type="number" autocomplete="bday-year" placeholder="Year (YYYY)" data-oldest="125" data-error="dobYearInvalid" required>
        </div>
        <p id="dob-invalid" class="invalidInput">Please enter a valid birth date.</p>
      </div>
      <div class="input">
        <p class="input-header">Mobile Number</p>
        <input name="mobile" id="mobile" place-holder="tel-national" class="validate" type="text" maxlength="11" data-input-type="phone" data-error="mobileInvalid" placeholder="Optional">
        <p id="mobileInvalid" class="invalidInput">Please enter a valid mobile number.</p>
      </div>
      <p id="tos-link">By pressing the Sign Up button you agree to Flex-Fit's <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" target="_blank">Terms and Conditions of Use.</a></p>
      <button class="default-button submitButtons center" style="box-sizing: border-box; width: auto; padding: 10px 20px; font-size: 15pt;" id="submitButton" type="button">Sign Up</button>
      <?php
      if (isset($_GET["state"])){
        if ($_GET["state"] == "rejected"){
          echo("<p>Please check all credentials</p>");
        }
      }
      ?>
      </form>
      <div>
        <p>Already have an account? <a href="login.php" id="login-link">Log In</a></p>
      </div>
    </div>
    <script>
    $("#dobMonth").on('change', function() {
      $("#dobMonth").css("color", "white");
      $("#month-placeholder").remove();
    });
    </script>
    <script src="jsScripts/signUpValid.js"></script>
  </body>
</html>
