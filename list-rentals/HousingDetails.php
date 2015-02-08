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
	
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold;">Project Name</td>';
		echo '<td>' . $row['PROJECT_NAME'] . '</td></tr>';
		
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold;">Building Name</td> <td> ' . $row['BUILDING_NAME'] . '</td></tr>';
		
		echo '<tr style="border-bottom: 1px solid #ccc"><td style="color: #0074e4; font-weight: bold;">Address Name</td>';
		echo '<td>' . $row['STREET_ADDRESS'] . ' ' . $row['CITY'] . ', ' . $row['STATE'] . ' ' . $row['ZIP5'] . '</td></tr>'; 
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold;">Available Units</td><td>' . $availableUnits . '</td></tr>';
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold;">Total Units</td><td>' . $row['TOTAL_UNITS'] . '</td></tr>';
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold;">Nearest School </td><td>' . $schoolRow['0'] . '</td></tr>';
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold;">School Score </td><td>' . $schoolRow['1'] . '</td></tr>';
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold;">Housing Authority</td><td>' . $row['HOUSING_AUTHORITY'] . '</td></tr>';
		echo '<tr style="border-bottom: 1px solid #ccc;"><td style="color: #0074e4; font-weight: bold;">Phone Number</td><td>' . $row['ContactNumber'] . '</td></tr>';
		echo '</td>';
		echo '</tr>';
	}

	echo '</table>';
}


require_once('../db-close.php');
?>


