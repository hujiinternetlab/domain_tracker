<?php
require_once("dbdata.php");
require_once("functions");
session_start();
/*functions goes over all marked domains in db . those that exceed their limit will email user
 * and\or nag him.
*/







/*mark a user's domain */
function updateMarkedDomains($domainId,$timeLimit,$maxDate) {
	if (!isset($_SESSION('email'))) {

	}
	$query = "INSERT INTO markedDomains(iddomain,time_limit,max_date) VALUES("
			. $domainId . ", " . $timeLimit . "," . $maxDate . ")";
	$conn->query($query);

}