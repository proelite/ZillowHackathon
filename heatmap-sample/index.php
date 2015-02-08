<?php
// DON'T REMOVE THIS INCLUDE
// Use the controller file for 'business' logic
// This file (index.php) should only be view logic
require('./heatmap-controller.php');
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Heatmaps</title>
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
      #panel {
        position: absolute;
        top: 5px;
        left: 50%;
        margin-left: -180px;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=visualization"></script>
    <script>

var map, pointarray, heatmap;

var crimeData = [
<?php
	if(isset($output["crimeData"]) && !empty($output["crimeData"]))
	{
		foreach( $output["crimeData"] as $crimeLatLon)
		{
			$lat = $crimeLatLon["lat"];
			$lon = $crimeLatLon["lon"];
			$weight = $crimeLatLon["weight"];
			echo "new google.maps.LatLng($lat, $lon, $weight),";
		}
	}
?>
];

function initialize() {
  var mapOptions = {
    zoom: 13,
    center: new google.maps.LatLng(47.61460, -122.31704),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  var pointArray = new google.maps.MVCArray(crimeData);

  heatmap = new google.maps.visualization.HeatmapLayer({
    data: pointArray
  });

  heatmap.setMap(map);
  
  heatmap.set('radius', 20);
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>

  <body>
    <div id="map-canvas"></div>
  </body>
</html>