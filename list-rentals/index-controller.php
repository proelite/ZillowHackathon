<?php

echo '<a href="../map.html"> Map </a> </br>';

$input = $_GET;

// Validate inputs, if nothing set use defaults
if( !isset($input['householdIncome']) || empty($input['householdIncome']) )
{
	$input['householdIncome'] = 40000;	
}

// Strip commas from householdIncome (if it exists)
$input['householdIncome'] = str_replace(',', '', $input['householdIncome']);
$input['householdIncome'] = intval($input['householdIncome']);

// Based on householdIncome, numberOfResidents, location, veteran and disabled, return an array of houses
// XXX TODO FIND SOME HOUSES!

$output = array( 0 => Array('Property Name' => 'Westside property', 'Typical Rent' => '1200 for your HH size'), 1 => Array('Property Name' => 'Westside property', 'Typical Rent' => '1200 for your HH size') );


// Connect and setup db
$db = mysqli_connect('localhost','root'); 

if (!$db) 
	die("Connection failed: " . mysqli_connect_error());

$selectDB = mysqli_select_db($db, "HackHousing");	
if (!$selectDB)
	echo "Failed to select database </br>";
   
$query = "Select *";
$query .= " FROM HUDHousingInfo, FamilySizeIncomeLimits";
$query .= " (WHERE HUDHousingInfo.Housing_Authority == FamilySizeIncomeLimits.HousingAuthorityName)";
   
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
		echo "id: " . $row["NATIONAL_BUILDING_ID"] . ", Project Name: " . $row["PROJECT_NAME"] . "</br>";
}

mysqli_close($db);
?>