<?php
class CrimeDataCache
{
	private $db;
	
	function __construct()
	{
		$this->db = mysqli_connect("localhost", "root");

		if (mysqli_connect_errno()) 
		{
			echo "Couldn't connect to database. Ask Phil for credentials.";
		}

		if(!mysqli_select_db($this->db, "HackHousing"))
		{
			echo "Couldn't connect to database. Ask Phil for help.";
		}
	}
	
	function __destruct()
	{
		if(isset($this->db))
			mysqli_close($this->db);
	}
	
	private function ensureCache()
	{
		$query = "CREATE TABLE CrimeDataCache(lat DOUBLE, lon DOUBLE, crimeData LONGBLOB)";
		mysqli_query($this->db, $query);
	}
	
	public function get($lat, $lon)
	{
		$this->ensureCache();
		
		$query = "SELECT crimeData FROM CrimeDataCache WHERE lat=$lat AND lon=$lon";
		$result = mysqli_query($this->db, $query);
		if(!$result) return null;
		
		return json_decode(mysqli_fetch_array($result)[0]);
	}
	
	public function set($lat, $lon, $crimeData)
	{
		$this->ensureCache();
		
		$crimeDataJson = json_encode($crimeData);
		
		$query = "INSERT INTO CrimeDataCache(lat, lon, crimeData) VALUES ($lat, $lon, '$crimeDataJson')";
		
		mysqli_query($this->db, $query) or die("Error");
	}
};

function getClosestCrimeData($lat, $lon) {
	$cache = new CrimeDataCache();

	$cachedVal = $cache->get($lat, $lon);
	set_time_limit(0); //unlimited
	if( !empty($cachedVal) )
	{
		return $cache->get($lat,$lon);
	}
	
	$jsonurl = "https://data.seattle.gov/resource/7ais-f98f.json?\$where=within_circle(location,$lat,$lon,100)";
	$json = file_get_contents($jsonurl);
	$j = json_decode($json);
	$offenses = array();
	foreach ($j as $item) {
		if (array_key_exists($item->summarized_offense_description, $offenses)) {
			$offenses[$item->summarized_offense_description] += 1;
		} else {
			$offenses[$item->summarized_offense_description] = 1;
		}
	}
	
	$cache->set($lat, $lon, $offenses);
	
	return $offenses;
}

// Returns the weighted map of various offenses
function getCrimeDataWeights() {
    $crimeWeightMap = array(
        "ASSAULT" => 10,
        "BURGLARY" => 9,
        "PROPERTY DAMAGE" => 8,
        "STOLEN PROPERTY" => 6,
        "THREATS" => 7,
        "VEHICLE THEFT" => 6,
        "BIKE THEFT" => 3,
        "FRAUD" => 5,
        "CAR PROWL" => 4,
        "WARRANT ARREST" => 2,
        "OTHER PROPERTY" => 1,
        "DISTURBANCE" => 3,
        "ROBBERY" => 8,
        "MAIL THEFT" => 3,
        "LOST PROPERTY" => 1,
        "OTHER" => 1,
        "NARCOTICS" => 8,
        "TRESPASS" => 5,
        "PICKPOCKET" => 4,
        "WEAPON" => 7,
        "OBSTRUCT" => 1,
        "DUI" => 2,
        "LIQUOR VIOLATION" => 1,
        "TRAFFIC" => 3,
        "SHOPLIFTING" => 1,
        "COUNTERFEIT" => 1,
        "DISPUTE" => 2,
        "INJURY" => 3,
        "EMBEZZLE" => 2,
        "RECKLESS BURNING" => 2,
        "PURSE SNATCH" => 4
    );
    return $crimeWeightMap;
}

if (isset($_GET['json']) && $_GET['json'] == 1)
{
	$lat = isset($_GET['lat']) ? $_GET['lat'] : 0;
	$lon = isset($_GET['lon']) ? $_GET['lon'] : 0;
	
	$response = getClosestCrimeData($lat, $lon);
	
	header('Content-Type: application/json');
	echo json_encode($response);
}

?>
