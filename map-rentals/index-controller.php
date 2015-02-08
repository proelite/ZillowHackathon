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
$query .= " (WHERE (HUDHousingInfo.Housing_Authority == FamilySizeIncomeLimits.HousingAuthorityName)";
$query .= " AND (FamilySizeIncomeLimits.FamilySize == ))";
   
$buildingIDResult = mysqli_query($db, "Select * FROM HUDHousingInfo");   
  
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

require_once('../db-close.php');							  
?>