<?php
require_once("dbdata.php");
//turn seconds from javascript to mysql time
function secondsToTime($duration) {
	$seconds=$duration/1000; //turn milliseconds to seconds;
	$hours = round($seconds/3600);
	if ($hours < 10 )
		$hours = "0" . $hours;
	$minutes = ($seconds- $hours*3600)/60;
	$minutes = round($minutes);
	if ($minutes<10)
		$minutes ='0' . $minutes;
	$seconds=$seconds%60;
	$seconds = round($seconds);
	if ($seconds<10)
		$seconds='0' . $seconds;
	$time=$hours . ":" . $minutes . ":" .  $seconds;

	return $time;
}
function getUIDByMail($email) {
	global $conn;
	if (!isset($conn))
		echo "getUID got empty email!";
	$query="SELECT userid FROM user WHERE email='" . $email . "'";
	$result = $conn->query($query);
	if ($result->rowCount()==0)
		return false;
	$row = $result->fetchColumn(0);
	//echo "------------------getuid returning---------\n " . var_dump($row);
	return intval($row);

}

function checkAlert($email) {


}


function checkTimeLimit($email) {
	ob_start();
	//echo"in checktimelimit";
	global $conn;
	$uid = getUIDByMail($email);
	$isEmail=false;
	$isAlert=false;
	$query = "SELECT receives_mail FROM user WHERE userid=$uid";
	$res=$conn->query($query);
	if ($res->fetchColumn(0) == 1) {
		$isEmail=true;
	}
	$query="SELECT receives_alert FROM user WHERE userid=$uid";
	$res=$conn->query($query);
	if ($res->fetchColumn(0) == 1) {
		$isAlert=true;
	}
	if (!$isAlert && !isEmail) { //should i allow this ?
		echo "not isemail not isalert";
		die;
	}
	$isFirstTime=false;
	$query="SELECT last_email_date FROM user WHERE userid=$uid";
	$res=$conn->query($query);
	if ($res->fetchColumn(0) == null)
		$isFirstTime=true;

	if (!$isFirstTime)   {
		$query = " SELECT IF( DATEDIFF( CURDATE() , last_email_date ) >0, 1, 0 ) as time_diff FROM user WHERE userid =$uid ";
		$res=$conn->query($query);
		$timeDiff = $res->fetch(PDO::FETCH_ASSOC);

		if ($timeDiff['time_diff'] <=0 )  //do not send more than one email a day
			$isEmail=false;
	}
	$domains=array();
    //echo "sending mail";
	//not first time and more than one day difference
	$query = "SELECT domain.iddomain, domain.domain_name, TIMEDIFF(markedDomains.time_limit,domain.time ) as timeDiff " .
			"FROM domain " .
			"INNER JOIN markedDomains ON domain.iddomain = markedDomains.iddomain " .
			"WHERE domain.user_id =$uid " .
			" LIMIT 0 , 30 ";
	$msg = "Hello ,  you have overstayed in the following domains ";
	foreach($conn->query($query) as $row) {
		//echo "sending email {$row['domain_name']} {$row['timeDiff']}";
		if ($row['timeDiff'][0]=='-') {
		$msg = $msg .  "domain " . $row['domain_name'] . " overstayed " . $row['timeDiff'] . "minutes \n";
		$domains[]=array('domain'=>$row['domain_name'],'timeDiff'=> $row['timeDiff']);
		}
   }
	if ($isEmail) {
		mail($email,"domain overuse",$msg)	;
		$query = "UPDATE user SET last_email_date=CURDATE() WHERE userid=$uid";
		$conn->query($query);
	}


	if ($isAlert) {
		$query = "SELECT message FROM user WHERE userid=$uid";
		$res=$conn->query($query);
		$response=array("domains" =>$domains , "message" => $res->fetchColumn(0));
		$response=json_encode($response);
		echo $response;
	}
	else {

	}
	ob_flush();
}

function getMarkedDomains() {
	//get all marked domains for certain user
	session_start();
	if ((isset($_SESSION['email']) && !empty($_SESSION['email']))) {
	header('"Content-Type":application/json');
	global $conn;
	$id = getUIDByMail($_GET['email']);
	
	$query =  "SELECT domain.domain_name as dname,domain.time as dtime,domain.visits as dvisits," .
			" domain.iddomain as did,domain.start_date as dstartdate, domain.is_marked as dismarked, domain.time as dtime ," .
			" markedDomains.time_limit as dtimelimit,TIMEDIFF(markedDomains.time_limit , domain.time )  as dtimeleft " .
			" FROM user INNER JOIN domain ON user.userid=domain.user_id  INNER JOIN markedDomains ON user.userid=markedDomains.user_id AND domain.iddomain=markedDomains.iddomain WHERE domain.is_marked=1 AND user.userid=" . $id ;
	
	$dbresult = $conn->query($query);
	$js=array();
	foreach ($dbresult as $row) {
		$temp = array(
				'dname'   => $row['dname'],
				'dtime'   => $row['dtime'],
				'dvisits' => $row['dvisits'],
				'did'	  => $row['did'],
				'dstartdate' =>$row['dstartdate'],
				'dtimelimit'  =>$row['dtimelimit'],
				'dismarked'  =>$row['dismarked'] ,
				'dtimeleft'  =>$row['dtimeleft']
					);
	$js[] = $temp;
	}
	$js=json_encode($js);
	echo $js;
	}
	else {
		echo "user not logged in";
	}
		
}


function getNonMarkedDomains() {
	//get stats from db and echo them back ...
	session_start();
	header('"Content-Type":application/json');
	if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
	global $conn;
	$id = getUIDByMail($_SESSION['email']);
	//join between user and domain on userid
	$query = "SELECT domain.domain_name as dname,domain.time as dtime,domain.visits as dvisits, domain.iddomain as did,domain.start_date as dstartdate, domain.is_marked as dismarked " .
			" FROM user INNER JOIN domain on user.userid=domain.user_id WHERE user.userid=" . $id ." AND domain.is_marked=0 AND DATEDIFF(CURDATE(),domain.start_date)<8" ;
	//echo $query;
	//echo "query is $query";
	$dbresult = $conn->query($query);
	foreach ($dbresult as $row) {
		$temp = array(
				'dname'   => $row['dname'],
				'dtime'   => $row['dtime'],
				'dvisits' => $row['dvisits'],
				'did'	  => $row['did'],
				'dstartdate' =>$row['dstartdate'],
				'dismarked'  =>$row['dismarked'] ,
					);
	$js[] = $temp;
	}
	$js = json_encode($js);
	echo $js;
	}
	else
		echo "user is not logged in";
}




function login($email) {
	global $conn;
	$userId=getUIDByMail($email);
	if (!$userId) {
		$insert="INSERT INTO user(email,receives_mail,receives_alert) VALUES('$email',1,0)" ;
		$conn->query($insert);
	}

}



function customizeMessage($message,$isEmail,$isAlert,$email) {
	global $conn;
	$uid = getUIDByMail($email);
    echo "in customize : uid is $uid";
	if ($isEmail==false && $isAlert==false)
		die;
	if ($isEmail==true) {
		$query="UPDATE user SET receives_mail=1 WHERE userid=$uid";
		echo "query: $query";
		$conn->query($query);
	}
	if (!empty($message)) {
		$query="UPDATE user SET message='$message' WHERE userid=$uid";
		echo "query: $query";
		$conn->query($query);
	}

	if ($isAlert) {
		$query="UPDATE user SET receives_alert=1 WHERE userid=$uid";
		echo "query: $query";
		$conn->query($query);
	}
}

function resetDomain($domainId,$uid)   {
	
	if (!is_int($domainId) || !is_int($uid)) {
		
		$domainId=intval($domainId);
		$uid=intval($uid);
	}
	global $conn;
	$query="UPDATE domain SET start_date=CURDATE() , time='00:00:00',  visits=0 WHERE iddomain=$domainId AND user_id=$uid";
	echo $query;
	$conn->query($query);
	$query="UPDATE markedDomains SET time_limit='00:00:00' WHERE iddomain=$domainId AND user_id=$uid";
	echo $query;
	$conn->query($query);
	
}


function markedDomainsInsert($data,$date) {
	global $conn;

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
			$conn->query($query);
			$query = "UPDATE domain SET is_marked=1 WHERE iddomain=" . $domainId;
			echo "$query";
			$conn->query($query);
		}
	}
	catch (PDOException $p) {
		echo $p->errorInfo;
		echo "exception!!";
		echo var_dump($p);
	}
	catch (Exception $e) {
		echo "exception caught" ;
		$e->getMessage();
	}

}
//updates a single marked domain
function markedDomainsUpdate($data,$uid) {

	global $conn;
	$hours=$data["hours"];
	$minutes=$data["minutes"];
	$date=$data["date"];
	$domainId=$data["id"];
	$sqlTime = secondsToTime($hours*3600*1000 + $minutes*60*1000);
	$query = "UPDATE markedDomains " .
			"SET time_limit='" . $sqlTime . "',max_date='" . $date .
			"' "  .   "WHERE iddomain=" . $domainId . " AND user_id=$uid" ;
	echo "$query";
	$conn->query($query);

}
//deletes a single marked domain (both from domain and marked)
function markedDomainDelete($data,$uid) {
	global $conn;
	var_dump($data);
	echo "count is" . count($data);
	for ($i=0;$i<count($data)-1;$i++) {
		$query = "DELETE FROM markedDomains WHERE markedDomains.iddomain=" . (int)$data[$i] . " AND markedDomains.user_id=$uid" .
		" LIMIT 1" ;
		echo $query . " ";
		$conn->query($query);
		$query = "DELETE FROM domain WHERE domain.iddomain=" . (int)$data[$i] . " AND domain.user_id=$uid LIMIT 1";
		echo $query;
		$conn->query($query);

	}
}


	function markedDomainExists($domainId) {
		global $conn;
		echo "domainId is $domainId";
		$query="SELECT * FROM markedDomains WHERE iddomain=" . (int)$domainId ;
		$result = $conn->query($query);
		$row = $result->fetchColumn();
		if (empty($row))
			return false;
		else {
			//echo "returning not empty";
			return $row;
		}



	}

