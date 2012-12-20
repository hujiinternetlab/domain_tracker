<?php
session_start();
require_once("dbdata.php");

/*this script inserts into marked domains an already existing domain or domains */

require_once("functions.php");
//require_once("statisticsdb.php");

if (isset($_SESSION['email'])) {
	$data = json_decode(file_get_contents('php://input'),true);
	$date = $data[date];
	try{
		for ($i=0;$i<count($data)-1;$i++) {
			$domainId = $data[$i][id];
			$hours    = $data[$i][timeLimit][0];
			$minutes  = $data[$i][timeLimit][1];
			//	echo "id is " . $domainId .  " hours is " . $hours  . " minutes is " . $minutes;
			$sqlTime = secondsToTime($hours*3600*1000 + $minutes*60*1000);
			$uid = getUIDByMail($_SESSION['email']);
			$query = "INSERT INTO markedDomains(iddomain,time_limit,max_date,user_id) VALUES("
					. $domainId . ", " . "'" .  $sqlTime ."'"  . ",'" . $date . "'," . $uid . ")";
				
			echo"$query";
			$conn->query($query);
			$query = "UPDATE domain SET is_marked=1 WHERE iddomain=" . $domainId;
			$conn->query($query);
				
		}

	}

	catch (PDOException $p) {
		echo $p->errorInfo;
		echo var_dump($p);
	}
	catch (Exception $e) {
		echo "exception caught" ;
		$e->getMessage();
	}
}
else
	echo "markeddomains : email is not set in session"



			//print_r(array_keys($_COOKIE));




?>