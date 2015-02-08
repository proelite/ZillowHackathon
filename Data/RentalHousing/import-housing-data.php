<?php 
$conn = mysqli_connect('localhost','root'); 

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}  

echo "Connection OK </br>"; 

// Database setup
$dropDB = mysqli_query($conn, "drop database HackHousing");

if (!$dropDB)
	echo "Failed to drop Database </br>";
$createDBSql = "Create database HackHousing";
$createDB = mysqli_query($conn, $createDBSql);
if (!$createDB)
	echo "Failed to create Database fail </br>";

$selectDB = mysqli_select_db($conn, "HackHousing");	
if (!$selectDB)
	echo "Failed to select database";
	
// Create HubHouseInfo table
$createTableSQL = "CREATE TABLE HUDHousingInfo (NATIONAL_BUILDING_ID INT PRIMARY KEY,";
$createTableSQL .= " PROJECT_NAME VARCHAR(255),";
$createTableSQL .= " BUILDING_NAME VARCHAR(255),";
$createTableSQL .= " HOUSING_AUTHORITY VARCHAR(255),";
$createTableSQL .= " STREET_ADDRESS VARCHAR(255) NOT NULL,";
$createTableSQL .= " CITY VARCHAR(255) NOT NULL,";
$createTableSQL .= " STATE VARCHAR(255) NOT NULL,";
$createTableSQL .= " ZIP5 INT,";
$createTableSQL .= " ZIP9 INT,";
$createTableSQL .= " LATITUDE DOUBLE,";
$createTableSQL .= " LONGITUDE DOUBLE,";
$createTableSQL .= " TOTAL_UNITS INT,";
$createTableSQL .= " ACC_UNITS INT,";
$createTableSQL .= " TOTAL_OCCUPIED INT,";
$createTableSQL .= " PERCENT_OCCUPIED INT,";
$createTableSQL .= " MSA_NAME VARCHAR(255),";
$createTableSQL .= " CONSTRUCT_DATE VARCHAR(255),";
$createTableSQL .= " DOF_ACTUAL_DT VARCHAR(255))";

$createTableStmt = mysqli_prepare($conn, $createTableSQL);

if (!$createTableStmt)
	echo "create Table Failed</br>";

$createTableEx = mysqli_execute($createTableStmt);

if (!$createTableEx)
	echo "Failed to create table </br>";


$filename = "./Data/RentalHousing/FilteredHUDData.csv";
if (!file_exists($filename))
{
	$filename = "./FilteredHUDData.csv";
	if (!file_exists($filename)) 
		die ("HousingInfo CSV Not Found.");
}
else
{
	echo "CSV File is found </br>";
	
	// Load csv data into Database	
 	$sqlText2 = 'LOAD DATA LOCAL INFILE \'' . $filename . '\' '  ; 
 	$sqlText2 .= 'REPLACE INTO TABLE HUDHousingInfo ';
 	$sqlText2 .= 'FIELDS TERMINATED BY "," ';
 	$sqlText2 .= 'LINES TERMINATED BY "\r\n" ';
 	$sqlText2 .= 'IGNORE 1 LINES ';
  
 	$loadHudHousingData = mysqli_query($conn, $sqlText2);

 	if (!$loadHudHousingData)
		die("Loading HUD Housing Info failed </br>");

	 echo "Housing Data was loaded successfully!";
}

 mysqli_close($conn);
?> 