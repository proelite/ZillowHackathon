<?php
// DON'T REMOVE THIS INCLUDE 
// Put all of the "control" logic in the controller file. 
// Leave this file (index.php) to just visuals and the view logics
require("index-controller.php");
?>


<!DOCTYPE html>
<html>
  <head>
    <style type="text/css">
      html, body, #map-canvas { height: 100%; margin: 0; padding: 0;}
    </style>

    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC16FVIVK4DIVy_p6UwyBaekvYcgB_6OnM">
    </script>
    
    <script type="text/javascript"
      src="http://apicdn.walkscore.com/api/v1/traveltime_widget/js?wsid=f64c1f8a65290fca0c6cc0442dfe59b5">
    </script>
        
    <script type="text/javascript">

      const geocodeapi = 'https://maps.googleapis.com/maps/api/geocode/json?address=';
      const apikey = '&key=AIzaSyC16FVIVK4DIVy_p6UwyBaekvYcgB_6OnM';

      var initialLocation;
      var seattle = new google.maps.LatLng(47.61460,-122.31704);
      var browserSupportFlag =  new Boolean();
      var map;
      var widget;
      var loaded = false;

	  var buildings = 
	  	<?php
			echo '\'{"Buildings":[';
			if(isset($output["buildings"]) && !empty($output["buildings"]))
			{   
				$length = count($output["buildings"]);

				$counter = 1;

				foreach( $output["buildings"] as $building)
				{
					echo '{';
					$projectName = $building["projectName"];
					$buildingName = $building["buildingName"];
					$buildingId = $building["buildingId"];
					$streetAddress = $building["streetAddress"];
					$lat = $building["lat"];
					$lng = $building["lng"];
					$totalunits = $building["totalunits"];
					$totaloccupied = $building["totaloccupied"];
				
					echo '"projectName":"';
					echo $projectName;
					echo '",';

					echo '"buildingName":"';
					echo $buildingName;
					echo '",';

					echo '"buildingId":"';
					echo $buildingId;
					echo '",';

					echo '"streetAddress":"';
					echo $streetAddress;
					echo '",';

					echo '"lat":"';
					echo $lat;
					echo '",';

					echo '"lng":"';
					echo $lng;
					echo '",';

					echo '"totalunits":"';
					echo $totalunits;
					echo '",';

					echo '"totaloccupied":"';
					echo $totaloccupied;

					if ($counter < $length)
						echo '"},';
					else
						echo '"}';

					$counter++;
				}
			}
			echo ']}\';'
		?>
	  
	  var buildingsJson;

      function initialize() {

        initialLocation = seattle;

        var mapOptions = {
          mapTypeId: google.maps.MapTypeId.ROADMAP, center: initialLocation, zoom: 8, scrollwheel : false 
        };
        
        map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        // Try W3C Geolocation (Preferred)
        if(navigator.geolocation) {
          browserSupportFlag = true;
          navigator.geolocation.getCurrentPosition(function(position) {
            initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
            map.setCenter(initialLocation);
          }, function() {
            handleNoGeolocation(browserSupportFlag);
          });
        }
        // Browser doesn't support Geolocation
        else {
          browserSupportFlag = false;
          handleNoGeolocation(browserSupportFlag);
        }

        widget = new walkscore.TravelTimeWidget({
          map    : map,
          origin : ''+initialLocation.lat()+','+initialLocation.lng(),
          show   : true,
          mode   : walkscore.TravelTime.Mode.DRIVE
        });

       	buildingsJson = JSON.parse(buildings);

		google.maps.event.addListenerOnce(map, 'idle', function(){
			loaded = true;
        	plotBuildings();
		});
     }

    function handleNoGeolocation(errorFlag) {
      initialLocation = seattle;
      map.setCenter(initialLocation);
    }

    function submitLocation() 
    {
      if (loaded) 
      {
	      var address = document.getElementById("addressInput").value;
	      var url = geocodeapi+address+apikey;
	      var json = JSON.parse(httpGet(url));
	      if (json.status=="OK") 
	      {
	        var latlng = json.results[0].geometry.location;

	        map.setCenter(new google.maps.LatLng(latlng.lat, latlng.lng));
	        widget.setOrigin(''+latlng.lat+','+latlng.lng);
	        plotBuildings();
	      }
	      else
	      {
	        alert("Please enter a valid address");
	      }	
      }
      else
      {
      	alert("Please wait for map to load.");
      }
    }

    function plotBuildings()
    {
    	var bounds = map.getBounds();
    	var i = 0;

    	for (; i < buildingsJson.Buildings.length; i++)
    	{
    		var building = buildingsJson.Buildings[i];
    		var latlng = new google.maps.LatLng(building.lat, building.lng);
    		if (bounds.contains(latlng)) 
    		{
				var marker = new google.maps.Marker({
				    position: latlng,
				    map: map,
				    title: building.buildingName
				});
    		}
    	}
    }

    function httpGet(theUrl)
    {
        var xmlHttp = null;

        xmlHttp = new XMLHttpRequest();
        xmlHttp.open( "GET", theUrl, false );
        xmlHttp.send( null );
        return xmlHttp.responseText;
    }

    </script>

  </head>
  <body onload="initialize()">
    <div class="container">
          <label class="input-label">
            Where do you work?
          <input id = "addressInput" type="text" name="location" placeholder="Enter a city, address, school, business, etc.">
          </label>
          <button type="button" onclick="submitLocation()">See Homes</button> 
    </div>
    <div id="map-canvas"></div>
  </body>
</html>


