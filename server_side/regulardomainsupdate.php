<?php

/*this script is responsible to update (either insert new domains or update times of 
 * existing domains for the user ) regular domains (not marked) .
 *  data is received by post from background.js .
 * background.js sends data to this script periodically. 
 */

require_once("dbdata.php");
require_once("functions.php");
session_start();
if (isset($_SESSION['email'])) {
	try {
       // echo "in regulardomainsupdate";
		header('"Content-Type":application/json');
		$data = json_decode(file_get_contents('php://input'),true);
		//var_dump($data);
		$email = urldecode($data[count($data)-1]["email"]);
		$userId =getUIDByMail($email);
		//echo "user id is " . var_dump($userId);

		//if firsttime user add user to db
		if (!$userId) {
			$insert="INSERT INTO user(email,receives_mail,receives_alert) VALUES('$email',1,0)" ;
			$conn->query($insert);
		}

		$userId = getUIDByMail($email);
		for  ($i=0;$i<count($data)-1;$i++) {
			$domainName=$data[$i]["domainName"];
			$duration=$data[$i]["totalDuration"];
			$duration=secondsToTime($duration);
			$numVisits=$data[$i]["numVisits"];
			//choose the domain record that equal to domain with same userId
			$query= " SELECT * FROM domain WHERE domain_name='" .
					$domainName . "' AND user_id="  . $userId ;
			$stmt=$conn->query($query);
			//if new domain , insert it and move to next iteration
			if ($stmt->rowCount()==0) {

				$query="INSERT INTO domain(domain_name,user_id,start_date,time,visits) " .
						" VALUES('$domainName',$userId ,NOW(),'$duration',1)";
				//echo $query;
				$conn->query($query);
				continue; //continue to next iteration
			}
			//domain already exists
			else {
				//TODO write code to aggregate durations
				$resultset=$stmt->fetch(PDO::FETCH_ASSOC);
				$domainId=$resultset["iddomain"];
				$query = "UPDATE domain SET time=ADDTIME(time , '$duration') , visits=visits+1
				WHERE iddomain=$domainId";
				//echo "before exception..$query";
				$conn->query($query);

			}

			//TODO make sure mysql durations are correct
			//update stats:
		}
		//echo "calling to checktimeLimit";
		checkTimeLimit($email);
	}
	catch (PDOException $p) {
		echo "exception in regluardomainsupdate!";
		echo $p->errorInfo;
		echo var_dump($p);
	}
	catch (Exception $e) {
		echo "exception caught" ;
		$e->getMessage();
	}
}
else {
	echo "statisticsdb : user is not logged in";

}