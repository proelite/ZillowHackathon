<?php

echo '<a href="/map-rentals"> Map </a> </br>';

$input = $_GET;

//var_dump($input);

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
$query .= " AND PERCENT_OCCUPIED < 100";
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
	echo '<table border ="1">';
	echo '<tr> <th> Project Name </th> <th> Building Name </th>';
	echo '<th> Address </th> <th> Available Units </th> <th> Income Limit </th></tr>';
	while ($row = $buildingIDResult->fetch_assoc())
	{
		$availableUnits = $row['TOTAL_UNITS'] - $row['TOTAL_OCCUPIED'];
	
		echo '<tr>';
		echo '<td>' . $row['PROJECT_NAME'] . '</td><td> ' . $row['BUILDING_NAME'] . '</td>';
		echo '<td>' . $row['STREET_ADDRESS'] . ' ' . $row['CITY'] . ', ' . $row['STATE'] . ' ' . $row['ZIP5'] . '</td>'; 
		echo '<td>' . $availableUnits . '</td>';
		echo '<td>' . '$' . $row['IncomeLimit'] . '</td>';
		echo '</tr>';
	}

	echo '</table>';
}
else
{
	echo "No HUD housing available at" . $input['householdIncome'] . "</br>";
}

require_once('../db-close.php');							  
?>