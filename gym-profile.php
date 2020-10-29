<!DOCTYPE html>
<?php
include_once "phpScripts/isLoggedIn.php";

if (!isset($_GET["gym"])) { //if no  gym name is specified redirect
  header("location: 404.html");
  exit;
}

//if gym is specified then search for it

$name = $_GET["gym"];

include "phpScripts/connectToDB.php"; #connect to the db

$stmt = $mysqli->prepare('SELECT * FROM gym
                          WHERE Name = ?;');
$stmt->bind_param("s",$name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0){ //if only one result is found
  header("location: error.html");
  exit;
}

//confirmed that a gym does exist under that name

while($row = $result->fetch_assoc()){ //get all the details of the gym
  $name = $row["Name"];
  $gymID = $row["GymID"];
  $desc = $row["Description"];
  $address = $row["Address"];
  $town = $row["Town"];
  $county = $row["County"];
  $postcode = $row["Postcode"];
  $website = $row["Website"];
  $email = $row["Email"];
  $phone = $row["Phone"];
  $lat = $row["Latitude"];
  $long = $row["Longitude"];
  $monday = $row["MondayTimes"];
  $tuesday = $row["TuesdayTimes"];
  $wednesday = $row["WednesdayTimes"];
  $thursday = $row["ThursdayTimes"];
  $friday = $row["FridayTimes"];
  $saturday = $row["SaturdayTimes"];
  $sunday = $row["SundayTimes"];
}
?>
<html lang="en">
  <head>
    <?php include "phpScripts/favicon.html"; ?>
    <link rel="stylesheet" type="text/css" href = "css/style.css"/>
    <link rel="stylesheet" type="text/css" href = "css/gym-profile.css"/>
    <script type="text/javascript" src="jsScripts/jQuery.js"></script>
    <meta charset="utf-8">
  </head>
  <body style="min-height: 325px;">
    <?php include_once "side-nav.php";?>
    <div class="main">
      <h1 id="gym-name"><?php echo($name); ?></h1>
      <div id="image-slider">
        <div id="slides" style="padding: 0;">
          <?php
            $stmt = $mysqli->prepare('SELECT Image FROM gym_images
                                      WHERE GymID = ?;');
            $stmt->bind_param("i",$gymID);
            $stmt->execute();
            $result = $stmt->get_result();

            while($row = $result->fetch_assoc()){
              echo('<img src="data:image/jpeg;base64,'.base64_encode($row['Image']).'"/>'); //https://stackoverflow.com/questions/20556773/php-display-image-blob-from-mysql
            }

          ?>
          <p class="controllers" id="back-button" style="left:0;">Back</p> <?php // TODO: MAKE NICE LOOKING IMAGES ?>
          <p class="controllers" id="forward-button" style="right:0;">Next</p>
        </div>
      </div>
      <div id="price" class="tile-wrapper">
        <div id="opening-times" class="tile-content" style="width: 47%; box-sizing: border-box; float: left;">
          <h3>Opening Times</h3>
          <table>
            <tr>
              <td>
                Monday
              </td>
              <td>
                <? echo($monday); ?>
              </td>
            </tr>
            <tr>
              <td>
                Tuesday
              </td>
              <td>
                <? echo($tuesday); ?>
              </td>
            </tr>
            <tr>
              <td>
                Wednesday
              </td>
              <td>
                <? echo($wednesday); ?>
              </td>
            </tr>
            <tr>
              <td>
                Thursday
              </td>
              <td>
                <? echo($thursday); ?>
              </td>
            </tr>
            <tr>
              <td>
                Friday
              </td>
              <td>
                <? echo($friday); ?>
              </td>
            </tr>
            <tr>
              <td>
                Saturday
              </td>
              <td>
                <? echo($saturday); ?>
              </td>
            </tr>
            <tr>
              <td id="sunday">
                Sunday
              </td>
              <td>
                <? echo($sunday); ?>
              </td>
            </tr>
          </table>
        </div>
        <div id="location" class="tile-content" style="width: 47%; box-sizing: border-box; float: right;">
          <h3>Location</h3>
          <a style="display: block; color: var(--light-accent-color);" target="_blank" href="https://www.google.com/maps/place/<?php echo($address."+".$town."+".$county."+".$postcode)?>"><?php echo($address.", ".$town.", ".$county.",".$postcode); ?> </a>
          <div id="map"style="width: 100%;height:300px; background-color: black; -webkit-transiton: 0s; transition: 0s;">
            <script>
              function initMap() {
                var locationRio = {lat: <?php echo($lat); ?>, lng: <?php echo($long); ?>};
                var map = new google.maps.Map(document.getElementById('map'), {
                  zoom: 17,
                  mapTypeControl: true,
                  center: locationRio,
                  mapTypeControl: false,
                  gestureHandling: 'cooperative',
                  mapTypeId: google.maps.MapTypeId.TERRAIN
                });
                var marker = new google.maps.Marker({
                  position: locationRio,
                  map: map,
                  title: '<?php echo($name); ?>'
                });
              }
            </script>
            <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAuVLMVHWCTOY5TFKwcKUWxTPs1CIK35dE&callback=initMap">
            </script>
          </div>
        </div>
      </div>
      <div class="tile-wrapper">
        <div id="prices" class="tile-content" style="width: 47%; box-sizing: border-box; float: left;">
          <h3>Prices</h3>
          <table>
            <?php

            $stmt = $mysqli->prepare('SELECT GroupName, Price
                                      FROM gym_prices
                                      WHERE GymID=?');
            $stmt->bind_param("i",$gymID);
            $stmt->execute();
            $result = $stmt->get_result();

            while($row = $result->fetch_assoc()){
              echo("<tr>
                      <td>
                        ".$row["GroupName"]."
                      </td>
                      <td>
                        £".$row["Price"]."
                        </td>
                    </tr>");
            }
            ?>
          </table>
        </div>
        <div id="contact" class="tile-content" style="width: 47%; box-sizing: border-box; float: right;">
          <h3>Contact Details</h3>
          <a href="<?php echo($website); ?>" target="_blank" style="color: var(--light-accent-color); display:block; text-align: center;">Website<a>
          <table>
            <tr>
              <td>
                Phone
              </td>
              <td>
                <?php echo($phone); ?>
              </td>
            </tr>
            <tr>
              <td>
                Email
              </td>
              <td>
                <?php echo($email); ?>
              </td>
            </tr>

          </table>
        </div>
      </div>
      <div id="description" class="tile-wrapper">
        <div class="tile-content">
          <h3>About <? echo($name); ?> </h3>
          <p> <?php echo($desc); ?> </p>
        </div>
      </div>
      <script type="text/javascript" src="jsScripts/slideshow.js"></script>
      <script>
        $( document ).ready(function() {
            $("#back-button").click(function() {
              changeImage(-1);
            })
            $("#forward-button").click(function() {
              changeImage(1);
            })
          });
      </script>
  </body>
</html>

<?php $mysqli->close(); //close the connection?>
