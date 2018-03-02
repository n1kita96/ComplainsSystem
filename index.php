<?php include('server.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome</title>
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
</head>
<body>
  <div class="header">
    <h2>Complain</h2>
  </div>

  <div class="content">
    <?php if (!isset($_SESSION['success'])): ?>
      <div class="error success">
        <h3>
          <?php 
          echo $_SESSION['success']; 
          unset($_SESSION['success']); 
          header('location: login.php');
          ?>
        </h3>
      </div>
      <?php endif ?>  
      <?php if(isset($_SESSION['name']) && (!empty($_SESSION['name']))): ?>
        <p>Welcome <strong><?php echo $_SESSION['name']; ?></strong></p>
        <p><a href="index.php?logout='1'" style="color: red;">Logout</a></p>
      <?php elseif(isset($_SESSION['email'])): ?>
        <p>Welcome <strong><?php echo $_SESSION['email']; ?></strong></p>
        <p><a href="index.php?logout='1'" style="color: red;" >Logout</a></p>
      <?php endif ?>

      <form method="post" action="index.php" enctype="multipart/form-data">
        <div class="form-group">
          <label for="types">Select type:</label>
          
          <script type="text/javascript">
            function changeTypeFunc() {
              var types = document.getElementById("types");
              var selectedValue = types.options[types.selectedIndex].value;
                $(document).ready(function(){  
                      $.ajax({
                        type: 'POST',
                        url: 'server.php',
                        data: {type: '' + selectedValue},
                        success: function( data ) {
                          console.log( data );
                        }
                      });
                    });
              issues.removeAttribute('disabled');
              // $("#issues").append( $('<option value="{$row['id']}">somevalue</option>')); foreach issues in issue_array -> if issue.type_id = selectedValue -> append, else -> remove
            }

          </script>

          <select class="form-control" id="types" onchange="changeTypeFunc();">
            <option hidden >Select type</option>
            <?php
           
           //painful shot in own leg here
            $query = "SELECT * FROM types";
            $result = mysqli_query($db, $query) or die("Failed connect to database".mysqli_error());

            while (($row = mysqli_fetch_assoc($result)) != null) {
              echo "<option class=\"option\" value = '{$row['id']}'>{$row['name']}</option>";
            } 
            ?>
          </select>
        </div>

        <div class="form-group">
          <label for="issues">Select issue:</label>
              <script type="text/javascript">
                  function changeIssueFunc() {
                   var issues = document.getElementById("issues");
                   var selectedValue = isuues.options[issues.selectedIndex].value;
                    $(document).ready(function(){  
                      $.ajax({
                        type: 'POST',
                        url: 'server.php',
                        data: {isuue: '' + selectedValue},
                        success: function( data ) {
                          console.log( data );
                        }
                      });
                    });
                }
          </script>
          <select disabled class="form-control" id="issues" onchange="changeIssueFunc();">
            <option hidden >Select issue</option>
              <?php

            //other painful shot in own leg here
            // echo "<script type=\"text/javascript\">alert(document.getElementById(\"types\").options[types.selectedIndex].value);</script>";
           
            $query = "SELECT * FROM issues";// where type_id = $type_id";
            $result = mysqli_query($db, $query) or die("Failed connect to database".mysqli_error());

            while (($row = mysqli_fetch_assoc($result)) != null) {
              echo "<option class=\"option\" value = '{$row['id']}'>{$row['name']}</option>";
            } 

            ?>
          </select>
        </div>

        <div class="form-group">
          <label for="comment">Comment:</label>
          <textarea class="form-control" rows="4" maxlength="200" id="comment" value="comment" name="comment"></textarea>
        </div>

        <div id="map">
          <script>
            function initMap() {
              var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12
              });
              var infoWindow = new google.maps.InfoWindow({map: map});
                // Try HTML5 geolocation.
                if (navigator.geolocation) {
                  navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                      lat: position.coords.latitude,
                      lng: position.coords.longitude
                    };
                    var location = 'lat=' + pos.lat + 'lng=' + pos.lng;

                    $(document).ready(function(){  
                      $.ajax({
                        type: 'POST',
                        url: 'server.php',
                        data: {location: location},
                        success: function( data ) {
                          console.log( data );
                        }
                      });
                    });
                    
                    infoWindow.setPosition(pos);
                    infoWindow.setContent('Location found.');
                    map.setCenter(pos);
                  }, function() {
                    handleLocationError(true, infoWindow, map.getCenter());
                  });
                } else {
                    // Browser doesn't support Geolocation
                    handleLocationError(false, infoWindow, map.getCenter());
                  }
                }

                function handleLocationError(browserHasGeolocation, infoWindow, pos) {
                  infoWindow.setPosition(pos);
                  infoWindow.setContent(browserHasGeolocation ?
                    'Error: The Geolocation service failed.' :
                    'Error: Your browser doesn\'t support geolocation.');
                }

              </script>

            <script async defer
              src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1jsVlQYKpWv8OT86iUYvzvw09SyqPNHA&callback=initMap">
            </script>
          </div>

          <br>

          <div class="file-upload">
            <label>
              <input type="file" name="photo" accept="image/*">
              <span>Choose image</span>
            </label>
          </div>
          <input type="text" id="filename" class="filename" disabled>
          <script type="text/javascript">
          $(document).ready( function() {
            $(".file-upload input[type=file]").change(function(){
            var filename = $(this).val().replace(/.*\\/, "");
            $("#filename").val(filename);
            });
          });
          </script>
          <br>

            <div class="input-group">
              <button type="submit" name="submit_ticket" class="btn">Submit ticket</button>
            </div>
        </form>
    </div>

</body>
</html>