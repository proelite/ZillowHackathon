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
	  
	  
body {
	background-color: #EEE;
	color: #444;
	font-size: 1em;
	font-family: Helvetica;
}

	  
	  .header {
	line-height: 0;
	margin: 0 auto;
	text-decoration: none;
	-webkit-user-select: none;  /* Chrome all / Safari all */
	-moz-user-select: none;     /* Firefox all */
	-ms-user-select: none;      /* IE 10+ */
	/* No support for these yet, use at own risk */
	-o-user-select: none;
	user-select: none;
	min-width: 640px;
}

.header img {
	background-color: #0074e4;
	border-radius: 0 0 4px 4px;
	font-size: 0.750em;
	line-height: 1.5em;
	padding: 1em;
	width: 150px;
}
	</style>

	<script type="text/javascript"
	  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC16FVIVK4DIVy_p6UwyBaekvYcgB_6OnM&libraries=visualization">
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

		var pointarray, heatmap;

		var crimeData = [
		<?php
			if(isset($crimes["crimeData"]) && !empty($crimes["crimeData"]))
			{
				foreach( $crimes["crimeData"] as $crimeLatLon)
				{
					$lat = $crimeLatLon["lat"];
					$lon = $crimeLatLon["lon"];
					$weight = $crimeLatLon["weight"];
					echo "new google.maps.LatLng($lat, $lon, $weight),";
				}
			}
		?>
		];

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
	  	var infoWindow;

	  	function initialize() {
	  	infoWindow = new google.maps.InfoWindow(
	  		{
	  			content:"null"
	  		});

		initialLocation = seattle;

		var mapOptions = {
		  mapTypeId: google.maps.MapTypeId.ROADMAP, center: initialLocation, zoom: 14, scrollwheel : false 
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
		  mode   : walkscore.TravelTime.Mode.TRANSIT
		});

		buildingsJson = JSON.parse(buildings);

		google.maps.event.addListenerOnce(map, 'idle', function(){
			loaded = true;
			plotBuildings();

			var pointArray = new google.maps.MVCArray(crimeData);

			heatmap = new google.maps.visualization.HeatmapLayer({
				data: pointArray
			});

			heatmap.setMap(map);  
			heatmap.set('radius', 50);
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
			plotBuildings();

			widget.setOrigin(''+latlng.lat+','+latlng.lng);
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

  	var markers = [];

	function plotBuildings()
	{
		var bounds = map.getBounds();

		var tempM;
		var i = 0;

		for (i; i < markers.length; i++)
		{
			markers[i].setMap(null);
		}

		google.maps.event.clearListeners(map, 'click');
		markers = [];

		for (i=0; i < buildingsJson.Buildings.length; i++)
		{
			var building = buildingsJson.Buildings[i];
			var latlng = new google.maps.LatLng(building.lat, building.lng);
			if (bounds.contains(latlng)) 
			{
				var name = building.buildingName;

				if (name || name === "") 
				{
					name = building.projectName;
				}

				var address = building.streetAddress;
				var totalunits = building.totalunits;
				var totaloccupied = building.totaloccupied;

				var house = 'house.png';

				if (totaloccupied >= totalunits) 
				{
					house = 'fullhouse.png';
				}

				var marker = new google.maps.Marker({
					position: latlng,
					map: map,
					title: building.buildingName,
					icon: house
				});

				marker.html = '<div id="content">'+
					      '<h1 id="firstHeading" class="firstHeading">'+name+'</h1>'+
					      '<div id="bodyContent">'+
					      '<p><b>'+address+'</b></p>'+
					      '<p>'+totaloccupied+'/'+totalunits+' occupied</p>'+
					      '</div>'+
					      '</div>';

				google.maps.event.addListener(marker, 'click', function() {
					infoWindow.setContent(this.html);
			    	infoWindow.open(map, this);
				});

				markers.push(marker);
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
  	<div class="header">
		<a href="/index.html">
			<img src="/Findr8-mix.png">
		</a>
		<span class="container">
		  <label class="input-label" style="margin: 5px">
			Where do you work?
		  <input id = "addressInput" type="text" name="location" placeholder="Enter a city, address, school, business, etc.">
		  </label>
		  <button type="button" onclick="submitLocation()">Center map here</button> 
		</span>
	</div>
	<div style="float:left; width:120px;" class="left-gutter">
		<table>
			<tr><td></td></tr>
		</table>
	</div>
	<div style="width:auto;" id="map-canvas"></div>
  </body>
</html>


