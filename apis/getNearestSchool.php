<?php

// Login using default unsecure username/pw
$db = mysqli_connect("localhost", "root");

if (mysqli_connect_errno()) 
{
	echo "Couldn't connect to database. Ask Phil for credentials.";
}

if(!mysqli_select_db($db, "test"))
{
	echo "Couldn't connect to database. Ask Phil for help.";
}

function getNearestSchoolScore($lat, $lon)
{
	global $db;
	$query = "SELECT Name, parentRating FROM `schools` ORDER BY ABS(schools.lat-$lat)+ABS(schools.lon-($lon)) ASC LIMIT 1,1";
	$responseFromQuery = mysqli_query($db, $query) or die ("WHAT");
	$response = mysqli_fetch_row($responseFromQuery);
	if(!$response) return Array();
	return $response;
}

if (isset($_GET['json']) && $_GET['json'] == 1)
{
	$lat = isset($_GET['lat']) ? $_GET['lat'] : 0;
	$lon = isset($_GET['lon']) ? $_GET['lon'] : 0;
	
	$response = getNearestSchoolScore($lat, $lon);
	
	header('Content-Type: application/json');
	echo json_encode($response);
}

if(isset($db))
	mysqli_close($db);
?>