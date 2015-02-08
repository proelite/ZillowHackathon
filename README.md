# FINDR (Find your '8')
A single service that enlightens low income renters of renting assistance options and searches for affordable homes within transit time and neighborhood safety constraints.

This webservice was developed as part of the weekend (Feb 6 '15 - Feb 8 '15) hackathon 'Hack Housing' sponsored by Zillow, UW & HUD.

![Homepage](Homepage.png)
![Housing listing page](Screenshot.jpg)

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

TO USE CRIME DATA API
=====================
1. As a REST API (returns json) - Eg: http://localhost/apis/getClosestCrimeData.php?json=1&lat=47.62085748029149&lon=-122.3361831197085
2. In your code, <?php include('apis/getClosestCrimeData.php'); ?> and then call getClosestCrimeData($lat, $lon);
3. In both cases, an array is returned with key as the Offense description and the count of that offense as the array value.

#hackhousing
