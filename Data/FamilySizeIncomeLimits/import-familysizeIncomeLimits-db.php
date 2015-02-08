<?php 
// Connect and setup db
$db = mysqli_connect('localhost','root'); 

if (!$db) 
{
    die("Connection failed: " . mysqli_connect_error());
}  

echo "Connection OK </br>"; 

$selectDB = mysqli_select_db($db, "HackHousing");	
if (!$selectDB)
	echo "Failed to select database";
	
// Create FamilySizeIncomeLimits table
$createTableSQL = "CREATE TABLE FamilySizeIncomeLimits";
$createTableSQL .= " (HousingAuthorityName VARCHAR(255),";
$createTableSQL .= " FamilySize INT,";
$createTableSQL .= " IncomeLimit INT,";
$createTableSQL .= " Primary Key (HousingAuthorityName, FamilySize))";
$createTableStmt = mysqli_prepare($db, $createTableSQL);

if (!$createTableStmt)
	echo "create Table Failed</br>";

$createTableEx = mysqli_execute($createTableStmt);

if (!$createTableEx)
	echo "Failed to create table </br>";


$filename = "./Data/FamilySizeIncomeLimits/FamilySizeIncomeLimits.csv";
if (!file_exists($filename))
{
	$filename = "./FamilySizeIncomeLimits.csv";
	if (!file_exists($filename)) 
		die ("Family Size Income Limits CSV Not Found.");
}
else
{
	echo "CSV File is found </br>";
	
	// Load csv data into Database	
 	$sqlText2 = 'LOAD DATA LOCAL INFILE \'' . $filename . '\' '  ; 
 	$sqlText2 .= 'REPLACE INTO TABLE FamilySizeIncomeLimits ';
 	$sqlText2 .= 'FIELDS TERMINATED BY "," ';
 	$sqlText2 .= 'LINES TERMINATED BY "\r\n" ';
 	$sqlText2 .= 'IGNORE 1 LINES ';
  
 	$loadFamilySizeIncomeLimitData = mysqli_query($db, $sqlText2);

 	if (!$loadFamilySizeIncomeLimitData)
		die("Loading Family Size Income Limits Info failed </br>");

	 echo "Family Size Income Limits Data was loaded successfully!";
}

 mysqli_close($db);
?> 