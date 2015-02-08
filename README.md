# ZillowHackathon
Repository for Zillow Hackathon Feb 2014

Getting started
==================
1. Run ./setup-db.php
2. http://localhost 
3. ??/

TO USE SCHOOL API
==================
1. As a REST API (returns json)- Eg; http://localhost/apis/getNearestSchool.php?json=1?lat=15?lon=14
2. In your code, <?php include('apis/getNearestSchool.php'); ?> and then call getNearestSchoolScore($lat, $lon);
3. In both cases, an array is return with only two values, the name of the school and the parentRating (0-5)

#hackhousing
