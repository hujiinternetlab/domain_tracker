<?php 
/*
 *connects to db and gathers statistics info , returns answer to ajax request that comes
*from index.php
*/

session_start();
require_once("dbdata.php");
require_once("functions.php");
//user is logged in
if (isset($_SESSION['email'])) {
	
	//get all marked domains for certain user
	/*
	login($_SESSION['email']);
	header('"Content-Type":application/json');
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

	unset($dbresult);
	unset($query);
	//get stats from db and echo them back ...
    //join between user and domain on userid
	$query = "SELECT domain.domain_name as dname,domain.time as dtime,domain.visits as dvisits, domain.iddomain as did,domain.start_date as dstartdate, domain.is_marked as dismarked " . 
	" FROM user INNER JOIN domain on user.userid=domain.user_id WHERE user.userid=" . $id ." AND domain.is_marked=0 " ; 
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

	echo json_encode($js);
	exit;
	  
}


else
	echo "statisticsview : email not set in session";
*/
	 getMarkedDomains();

}

?>