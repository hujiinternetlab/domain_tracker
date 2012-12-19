<?php
/*scripts marks a domain by domainId 
 * sets its ismarked to 1 and adds it to markedDomains
 * GET[id]
 * */
session_start();
require_once("dbdata.php");
require_once("functions.php");

if (isset($_SESSION['email'])) {
	
	try {
		$query = "UPDATE domain SET is_marked=1 WHERE iddomain=" . $_GET['id'];
		$conn->query($query);
		$uid = getUIDByMail($_SESSION['email']);
		$query = "INSERT INTO markedDomains(iddomain,user_id) VALUES({$_GET['id']},$uid)";
		$conn->query($query);
	}
	catch (PDOException $e) {
		echo "exception thrown " . $e->getMessage();
		
	}
		
}
else {
	echo "you are not logged in ";
	
}