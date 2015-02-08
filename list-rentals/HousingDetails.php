<?php
// Connect and setup db
require_once("../apis/GetNearestSchool.php");
require_once('../db-connect.php');

$buildingID = $_GET['NatBuildingID'];

$query = "Select *";
$query .= " FROM HUDHousingInfo, HAContactInfo";
$query .= " WHERE HAName = HOUSING_AUTHORITY ";
$query .= " AND NATIONAL_BUILDING_ID = ?";
$stmt = mysqli_prepare($db, $query);

mysqli_stmt_bind_param($stmt, 'i', $buildingID);

mysqli_stmt_execute($stmt);
$queryResult = mysqli_stmt_get_result($stmt);

if ($queryResult->num_rows > 0)
{
	echo '<table style="border-collapse: collapse; font-size: 0.75em; font-style: Helvetica; color: #333;">';
	while ($row = $queryResult->fetch_assoc())
	{
		$availableUnits = $row['TOTAL_UNITS'] - $row['TOTAL_OCCUPIED'];
	
		
	
		$schoolRow = getNearestSchoolScore($row['LATITUDE'], $row['LONGITUDE']);
	
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold; padding: 1em;">Project Name</td>';
		echo '<td style="padding: 1em;">' . $row['PROJECT_NAME'] . '</td></tr>';
		
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold; padding: 1em;">Building Name</td> <td style="padding: 1em;"> ' . $row['BUILDING_NAME'] . '</td></tr>';
		
		echo '<tr style="border-bottom: 1px solid #ccc"><td style="color: #0074e4; font-weight: bold; padding: 1em;">Address Name</td>';
		echo '<td style="padding: 1em;">' . $row['STREET_ADDRESS'] . ' ' . $row['CITY'] . ', ' . $row['STATE'] . ' ' . $row['ZIP5'] . '</td></tr>'; 
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold; padding: 1em;">Available Units</td><td style="padding: 1em;">' . $availableUnits . '</td></tr>';
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold; padding: 1em;">Total Units</td><td style="padding: 1em;">' . $row['TOTAL_UNITS'] . '</td></tr>';
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold; padding: 1em;">Nearest School </td><td style="padding: 1em;">' . $schoolRow['0'] . '</td></tr>';
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold; padding: 1em;">School Score </td><td style="padding: 1em;">' . $schoolRow['1'] . '</td></tr>';
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold; padding: 1em;">Housing Authority</td><td style="padding: 1em;">' . $row['HOUSING_AUTHORITY'] . '</td></tr>';
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold; padding: 1em;">Phone Number</td><td style="padding: 1em;">' . $row['ContactNumber'] . '</td></tr>';
		echo '</td>';
		echo '</tr>';
	}

	echo '</table>';
}


require_once('../db-close.php');
?>


