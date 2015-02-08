<?php

// XXX TODO Use Kalapana's APIs to get actual crime data for a particular lat/lon

require_once('../db-connect.php');
require('../apis/getClosestCrimeData.php');

$query = "SELECT BUILDING_NAME AS buildingName, STREET_ADDRESS As streetAddress, CITY AS city, STATE As state, LATITUDE AS lat, LONGITUDE as lon FROM `hudhousinginfo` WHERE city = 'Seattle'";

$result = mysqli_query($db, $query) or die("Can't find section8 buildings!");

while( $row = mysqli_fetch_array($result))
{
	$output["crimeData"][] = Array ("lat" => $row["lat"], "lon" => $row["lon"], "weight" => 1);
}

require_once('../db-close.php');							  
							  
?>