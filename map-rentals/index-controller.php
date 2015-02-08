<?php

$input = $_GET;

// Validate inputs, if nothing set use defaults
if( !isset($input['householdIncome']) || empty($input['householdIncome']) )
{
	$input['householdIncome'] = 40000;	
}

// Strip commas from householdIncome (if it exists)
$input['householdIncome'] = str_replace(',', '', $input['householdIncome']);
$input['householdIncome'] = intval($input['householdIncome']);

$numberOfResidents = $input['numberOfResidents'];

if ($numberOfResidents > 8)
	$numberOfResidents = 8;

// Based on householdIncome, numberOfResidents, location, veteran and disabled, return an array of houses
// XXX TODO FIND SOME HOUSES!

// Connect and setup db
require_once('../db-connect.php');

if (!$db) 
	die("Connection failed: " . mysqli_connect_error());

$selectDB = mysqli_select_db($db, "HackHousing");	
if (!$selectDB)
	echo "Failed to select database </br>";
   
$query = "Select *";
$query .= " FROM HUDHousingInfo, FamilySizeIncomeLimits";
$query .= " WHERE HUDHousingInfo.Housing_Authority = FamilySizeIncomeLimits.HousingAuthorityName";
$query .= " AND FamilySizeIncomeLimits.FamilySize = ?";
$query .= " AND FamilySizeIncomeLimits.IncomeLimit >= ?";
$stmt = mysqli_prepare($db, $query);

mysqli_stmt_bind_param($stmt, 'ii', $numberOfResidents, $input['householdIncome']);

mysqli_stmt_execute($stmt);
$buildingIDResult = mysqli_stmt_get_result($stmt);
   
if (!$buildingIDResult)
{
	 echo "Failed Query </br> ";
	 die(mysqli_error($db));
	 return;
}

if ($buildingIDResult->num_rows > 0)
{
	while ($row = $buildingIDResult->fetch_assoc())
	{
		$output["buildings"][] = Array ("projectName" => $row["PROJECT_NAME"], 
			"buildingName" => $row["BUILDING_NAME"], 
			"buildingId" => $row["NATIONAL_BUILDING_ID"], 
			"streetAddress" => $row["STREET_ADDRESS"], 
			"lat" => $row["LATITUDE"], 
			"lng" => $row["LONGITUDE"], 
			"totalunits" =>$row["TOTAL_UNITS"],
			"totaloccupied" =>$row["TOTAL_OCCUPIED"]
		);
	}
}

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
	$crimes["crimeData"][] = Array ("lat" => $row["lat"], "lon" => $row["lon"], "weight" => getLatLonCrimeWeight($row["lat"], $row["lon"]));
}

require_once('../db-close.php');							  
?>