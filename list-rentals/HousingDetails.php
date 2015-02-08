<?php
// Connect and setup db
require_once('../db-connect.php');

$buildingID = $_GET['NatBuildingID'];

$query = "Select *";
$query .= " FROM HUDHousingInfo";
$query .= " WHERE NATIONAL_BUILDING_ID = ?";
$stmt = mysqli_prepare($db, $query);

mysqli_stmt_bind_param($stmt, 'i', $buildingID);

mysqli_stmt_execute($stmt);
$queryResult = mysqli_stmt_get_result($stmt);

if ($queryResult->num_rows > 0)
{
	echo '<table border ="1">';
	while ($row = $queryResult->fetch_assoc())
	{
		$availableUnits = $row['TOTAL_UNITS'] - $row['TOTAL_OCCUPIED'];
	
		echo '<tr><td>Project Name</td>';
		echo '<td>' . $row['PROJECT_NAME'] . '</td></tr>';
		
		echo '<tr><td>Building Name</td> <td> ' . $row['BUILDING_NAME'] . '</td></tr>';
		
		echo '<tr><td>Address Name</td>';
		echo '<td>' . $row['STREET_ADDRESS'] . ' ' . $row['CITY'] . ', ' . $row['STATE'] . ' ' . $row['ZIP5'] . '</td></tr>'; 
		echo '<tr><td>Available Units</td><td>' . $availableUnits . '</td></tr>';
		echo '<tr><td>Housing Authority</td><td>' . $row['HOUSING_AUTHORITY'] . '</td></tr>';
		echo '</td>';
		
		echo '</tr>';
	}

	echo '</table>';
}


require_once('../db-close.php');
?>


