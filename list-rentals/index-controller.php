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

$numberOfResidents = $input['numberOfResidents'];

if ($numberOfResidents > 8)
	$numberOfResidents = 8;

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
	echo '<table border ="1">';
	echo '<tr> <th> Building ID </th> <th> Project Name </th> </tr>';
	while ($row = $buildingIDResult->fetch_assoc())
	{
		echo "<tr>";
		echo '<td>' . $row['NATIONAL_BUILDING_ID'] . '</td><td> ' . $row["PROJECT_NAME"] . '</td>';
		echo '</tr>';
	}

	echo '</table>';
}

mysqli_close($db);
?>