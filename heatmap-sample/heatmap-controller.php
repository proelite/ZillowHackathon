<?php

require_once('../db-connect.php');
require('../apis/getClosestCrimeData.php');

$query = "SELECT BUILDING_NAME AS buildingName, STREET_ADDRESS As streetAddress, CITY AS city, STATE As state, LATITUDE AS lat, LONGITUDE as lon FROM `hudhousinginfo` WHERE city = 'Seattle'";

$result = mysqli_query($db, $query) or die("Can't findr any section8 buildings!");

function getLatLonCrimeWeight($lat, $lon)
{
	$crimeWeights = getCrimeDataWeights();
	
	$crimeData = getClosestCrimeData($lat, $lon);
	
	$weight = 1;
	foreach( $crimeData as $crimeType => $count )
	{
		$crimeWeightAmt = 1;
		if(!empty($crimeWeights[$crimeType]))
		{
			$crimeWeightAmt = $crimeWeights[$crimeType];
		}
		else
		{
			$crimeWeightAmt = $crimeWeights["OTHER"];
		}
		$weight *= $count * $crimeWeightAmt;
	}
	return $weight;
}

while( $row = mysqli_fetch_array($result))
{
	$output["crimeData"][] = Array ("lat" => $row["lat"], "lon" => $row["lon"], "weight" => getLatLonCrimeWeight($row["lat"], $row["lon"]));
}

require_once('../db-close.php');							  
							  
?>