<?php
session_start();
require_once("dbdata.php");
require_once("functions.php");

/*script adds one domain to marked domains . the domain can either already exist , in that case
 * the script gets its id , or its a new domain (meaning it came from popup form) , in that
* case query is different.
* time_limit is mandatory (script dies with no time_limit)
* while max_date is optional
*/


if (isset($_SESSION['email'])) {
	try {
		$uid = getUIDByMail($_SESSION['email']);
		if (isset($_POST['exists']) &&  !empty($_POST['exists'])) {
			if ( empty($_POST['timeLimit']) || !isset($_POST['timeLimit'] )){
				echo "you must put a time limit to a domain";
				die;
			}

           if (!isset($_POST['name']) || empty($_POST['name']) || strlen($_POST['name'])>100)
           {
           	echo "invalid domain name";
           	die;
           }
			//domain already exists
			$query = "UPDATE domain SET is_marked=1 WHERE iddomain={$_POST['id']} AND
			user_id=$uid";
			echo $query;
			$result = $conn->query($query);
			if ($result->rowCount()==0)
				die;
			$queryPrefix = "INSERT INTO markeddomains VALUES(" . $_POST['id'] . "," .  $_POST['timeLimit'] . "," . 
					$_POST['date'] . "," . $uid . ")";
		
			echo $query;
			$conn->query($query);

		}
		//new domain (comes from popup form..)
		else {
			//insert domain to domain db
			echo "php!!";
			$query = "INSERT INTO domain(domain_name,user_id,start_date,is_marked) VALUES('{$_POST['name']}',$uid,NOW(),1)";
			echo "$query";
			$result = $conn->query($query);
			echo "here?";
			if ($result->rowCount()==0) {
				echo "script died!";
				die;
			}
			$query = "SELECT iddomain FROM domain WHERE domain_name='" . $_POST['name'] . "'  AND user_id=" . $uid;
			echo $query;
			$res = $conn->query($query);
			$domainId = $res->fetchColumn();
			$query = "INSERT INTO markedDomains(iddomain,time_limit,max_date,user_id) VALUES($domainId,'{$_POST['timeLimit']}','{$_POST['date']}',$uid)";
			echo "$query";
			$conn->query($query);

		}
	}

	catch (Exception $e) {
		echo "exception caught!";
		echo $e->getMessage();
	}


}