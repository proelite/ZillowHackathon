<?php
function getClosestCrimeData($lat, $lon) {
	$jsonurl = "https://data.seattle.gov/resource/7ais-f98f.json?\$where=within_circle(location,$lat,$lon,100)";
	$json = file_get_contents($jsonurl);
	$j = json_decode($json);
	$offenses = array();
	foreach ($j as $item) {
		if (array_key_exists($item->summarized_offense_description, $offenses)) {
			$offenses[$item->summarized_offense_description] += 1;
		} else {
			$offenses[$item->summarized_offense_description] = 1;
		}
	}
	return $offenses;
}
if (isset($_GET['json']) && $_GET['json'] == 1)
{
	$lat = isset($_GET['lat']) ? $_GET['lat'] : 0;
	$lon = isset($_GET['lon']) ? $_GET['lon'] : 0;
	
	$response = getClosestCrimeData($lat, $lon);
	
	header('Content-Type: application/json');
	echo json_encode($response);
}

?>
