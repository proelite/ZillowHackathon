<?php

$input = $_GET;

// Validate inputs, if nothing set use defaults
if( !isset($input['householdIncome']) || empty($input['householdIncome']) )
{
	$input['householdIncome'] = 40000;	
}

// Strip commas from householdIncome (if it exists)
$input['householdIncome'] = str_replace(',', '', $input['householdIncome']);
$input['householdIncome'] = intval($input['householdIncome']);

// Based on householdIncome, numberOfResidents, location, veteran and disabled, return an array of houses
// XXX TODO FIND SOME HOUSES!

$output = array( 0 => Array('Property Name' => 'Westside property', 'Typical Rent' => '1200 for your HH size'), 1 => Array('Property Name' => 'Westside property', 'Typical Rent' => '1200 for your HH size') );

?>