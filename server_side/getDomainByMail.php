<?php
require_once("dbdata.php");
if (isset($_GET["email"])) {

	$query = "SELECT domain_name FROM domain WHERE domain._name=" . $domainName
	. " AND domain.user_id IN (SELECT user_id FROM user WHERE email=" . $email . ")";
	$record = $conn->query($query);
	foreach ($record as $row) {
		echo $row['domain_name'];

	}
}

else {

	echo "user is not logged in !";

}