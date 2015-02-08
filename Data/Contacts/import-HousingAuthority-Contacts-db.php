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
	
// Create HousingAuthorityContacts table
$createTableSQL = "CREATE TABLE HAContactInfo";
$createTableSQL .= " (HAName VARCHAR(255) PRIMARY KEY,";
$createTableSQL .= " ContactNumber VARCHAR(255))";
$createTableStmt = mysqli_prepare($db, $createTableSQL);

if (!$createTableStmt)
	echo "create Table Failed</br>";

$createTableEx = mysqli_execute($createTableStmt);

if (!$createTableEx)
	echo "Failed to create table </br>";


$filename = "./Data/Contacts/HAContactInfo.csv";
if (!file_exists($filename))
{
	$filename = "./HAContactInfo.csv";
	if (!file_exists($filename)) 
		die ("HA Contacts CSV Not Found.");
}
else
{
	echo "Contacts CSV File is found </br>";
	
	// Load csv data into Database	
 	$sqlText2 = 'LOAD DATA LOCAL INFILE \'' . $filename . '\' '  ; 
 	$sqlText2 .= 'REPLACE INTO TABLE HAContactInfo ';
 	$sqlText2 .= 'FIELDS TERMINATED BY "," ';
 	$sqlText2 .= 'LINES TERMINATED BY "\r\n" ';
 	$sqlText2 .= 'IGNORE 1 LINES ';
  
 	$loadContactsData = mysqli_query($db, $sqlText2);

 	if (!$loadContactsData)
		die("Loading Contacts Info failed </br>");

	 echo "Contacts Data was loaded successfully!";
}

 mysqli_close($db);
?> 