<?php

// Import all rental housing data available
require("Data/RentalHousing/import-housing-data.php");

// Import all seattle school data available
require("Data/School/import-schools-to-mysql-db.php");

// Import all Family Size Income Limits
require("Data/FamilySizeIncomeLimits/import-familysizeIncomeLimits-db.php");

// Import all HA Contact Information
require("Data/Contacts/import-HousingAuthority-Contacts-db.php");

?>