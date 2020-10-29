<!DOCTYPE html>
<?php
include_once "phpScripts/isLoggedIn.php";
?>
<html lang="en">
  <head>
    <?php include "phpScripts/favicon.html"; ?>
    <link rel="stylesheet" type="text/css" href = "css/style.css"/>
    <link rel="stylesheet" type="text/css" href = "css/map.css"/>
    <meta charset="utf-8">
  </head>
  <body style="min-height: 325px;">
    <?php include_once "side-nav.php";?>
    <div class="main">
      <div id="map" style="height: 100%; width: 100%; -webkit-transiton: 0s; transition: 0s;">
      </div>
      <script>
      var gyms = <?php //leaves a json object to the javascript assignment to the variable gyms

      $gyms = [];

      class Gym{ //class that stores the name and position of the gym
        function __construct($n,$latitude,$longitude)
        {
          $this->name = $n;
          $this->lat = $latitude;
          $this->lng = $longitude;
        }
      }

      include "phpScripts/connectToDB.php"; #connect to the db

      $result = $mysqli->query('SELECT Name, Latitude, Longitude FROM gym');

      while($row = $result->fetch_assoc()){
        array_push($gyms,new Gym($row["Name"],$row["Latitude"],$row["Longitude"]));
      }

      $result->close();

      echo(json_encode($gyms));
      ?>

      var map;
      var house;

      function initMap() {

        var mapOptions = {
          zoom: 7,
          minZoom: 2,
          center: new google.maps.LatLng(52.5389,-1.37613)
        };

        var map = new google.maps.Map(document.getElementById('map'),mapOptions);

        function createMarker(obj,i){
          obj.marker = new google.maps.Marker(
            {
              position: new google.maps.LatLng(obj.lat,obj.lng),
              map: map,
              title: name,
              icon: {
                size: new google.maps.Size(30,48),
                scaledSize: new google.maps.Size(30,48),
                url: "css/images/map-images/grey-marker.png"
              },
            });

            let marker = obj.marker;
            var infowindow = new google.maps.InfoWindow();

            var userLocation;

            function setUserLocation(pos){

              function findDistance(lat1,lon1,lat2,lon2){ //haversine forumala MODIFIED: (https://www.htmlgoodies.com/beyond/javascript/calculate-the-distance-between-two-points-in-your-web-apps.html)
                var radlat1 = Math.PI * lat1/180
                var radlat2 = Math.PI * lat2/180
                var radlon1 = Math.PI * lon1/180
                var radlon2 = Math.PI * lon2/180
                var theta = lon1-lon2
                var radtheta = Math.PI * theta/180
                var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
                dist = Math.acos(dist)
                dist = dist * 180/Math.PI
                dist = (dist * 60 * 1.1515) * 0.8684 //gives in miles
                return dist
              }

              var userLocation = pos.coords;

              google.maps.event.addListener(marker, 'click', function() {
                 infowindow.setContent(content);
                 infowindow.open(map, this);
               });

              if (i === 0){
                currentLocation = new google.maps.Marker(
                  {
                    position: new google.maps.LatLng(userLocation.latitude,userLocation.longitude),
                    map: map,
                    icon: {
                      size: new google.maps.Size(13,13),
                      scaledSize: new google.maps.Size(13,13),
                      url: "css/images/map-images/current-location-marker.png"
                    },
                    title: "Current Location"
                  });

                  google.maps.event.addListener(currentLocation, 'click', function() {
                     infowindow.setContent("<div class='marker-popup' style='color: black;'>Current Location</div>");
                     infowindow.open(map, this);
                  });
              }

              let content = "<div class='marker-popup'><a href='gym-profile.php?gym="+obj.name+"'>"+obj.name+"</a><p class='marker-distance' text-align: right;'>"+findDistance(userLocation.latitude,userLocation.longitude,Number(obj.lat),Number(obj.lng)).toFixed(2)+" Miles</p></div>";

              google.maps.event.addListener(marker, 'click', function() {
                 infowindow.setContent(content);
                 infowindow.open(map, this);
              });
            }

            function noUserLocation(error){
              let content = "<div class='marker-popup'><a href='gym-profile.php?gym="+obj.name+"'>"+obj.name+"</a></div>";

              google.maps.event.addListener(marker, 'click', function() {
                 infowindow.setContent(content);
                 infowindow.open(map, this);
              });
            }

            if(navigator.geolocation){ //check if browser supports geolocation
              navigator.geolocation.getCurrentPosition(setUserLocation, noUserLocation); //1st param is success, 2nd is error
            }
        }

        for (var i = 0; i < gyms.length; i++) {
          createMarker(gyms[i],i);
        }
      }

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAuVLMVHWCTOY5TFKwcKUWxTPs1CIK35dE&callback=initMap"
    async defer></script>
    </div>
  </body>
</html>
