<?php
	// Login using default unsecure username/pw
	$db = mysqli_connect("localhost", "root");
	
	if (mysqli_connect_errno()) 
	{
		echo "Couldn't connect to database. Ask Phil for credentials.";
	}

	if(!mysqli_select_db($db, "test"))
	{
		echo "Couldn't connect to database. Ask Phil for help.";
	}
	
	// Create the tables
	$query = "DROP TABLE IF EXISTS Schools";
	mysqli_query($db, $query);
	
	$query = "CREATE TABLE Schools (gsId INT(6) UNSIGNED PRIMARY KEY, name VARCHAR(64), type VARCHAR(30), gradeRange VARCHAR(30), lat DOUBLE, lon DOUBLE)";
	mysqli_query($db, $query);

	// Dump school data into our database
	$cities = ["federal-way", "bellevue", "burien", "kent", "renton", "seattle"];
	foreach($cities as $city)
	{
		$filename = "./SeattleSchoolData/schools-near-$city.xml";
		if (!file_exists($filename))
		{
			$filename = "./schools-near-$city.xml";
			if (!file_exists($filename)) die("Yo, there aint no xml shizzle!");
		}
		$string = file_get_contents($filename);
		
		$xml = new SimpleXMLElement($string);
		$schools = $xml->xpath("/schools/school");
		if (!$schools) die("WHAT DID YOU DO?");
		if ($schools)
		{
			foreach($schools as $school)
			{
				$gsId = $school->gsId;
				$name = mysqli_real_escape_string($db, $school->name);
				$type = mysqli_real_escape_string($db, $school->type);
				$gradeRange = mysqli_real_escape_string($db, $school->gradeRange);
				$lat = $school->lat;
				$lon = $school->lon;
				$query = "INSERT INTO Schools (gsId, name, type, gradeRange, lat, lon) VALUES ($gsId, '$name', '$type', '$gradeRange', $lat, $lon);";
				mysqli_query($db, $query);
			}
		}
	}
	
	Echo "Yay! The DB has all your school data!";
	
	mysqli_close($db);
?>