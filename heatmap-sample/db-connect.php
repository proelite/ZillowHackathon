<?php
// Login using default unsecure username/pw
$db = mysqli_connect("localhost", "root");

if (mysqli_connect_errno()) 
{
	echo "Couldn't connect to database. Ask Phil for credentials.";
}

if(!mysqli_select_db($db, "HackHousing"))
{
	echo "Couldn't connect to database. Ask Phil for help.";
}
?>