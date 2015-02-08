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

// Returns the weighted map of various offenses
function getCrimeDataWeights() {
    $crimeWeightMap = array(
        "ASSAULT" => 10,
        "BURGLARY" => 9,
        "PROPERTY DAMAGE" => 8,
        "STOLEN PROPERTY" => 6,
        "THREATS" => 7,
        "VEHICLE THEFT" => 6,
        "BIKE THEFT" => 3,
        "FRAUD" => 5,
        "CAR PROWL" => 4,
        "WARRANT ARREST" => 2,
        "OTHER PROPERTY" => 1,
        "DISTURBANCE" => 3,
        "ROBBERY" => 8,
        "MAIL THEFT" => 3,
        "LOST PROPERTY" => 1,
        "OTHER" => 1,
        "NARCOTICS" => 8,
        "TRESPASS" => 5,
        "PICKPOCKET" => 4,
        "WEAPON" => 7,
        "OBSTRUCT" => 1,
        "DUI" => 2,
        "LIQUOR VIOLATION" => 1,
        "TRAFFIC" => 3,
        "SHOPLIFTING" => 1,
        "COUNTERFEIT" => 1,
        "DISPUTE" => 2,
        "INJURY" => 3,
        "EMBEZZLE" => 2,
        "RECKLESS BURNING" => 2,
        "PURSE SNATCH" => 4
    );
    return $crimeWeightMap;
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
