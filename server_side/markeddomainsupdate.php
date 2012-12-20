
<?php
/* script allows user to change values (date , time limit ) of a marked domain(one or more)
 * user can change values in statistics page and post data to this script
* Also inserts new markeddomains
* */
session_start();

require_once("dbdata.php");
require_once("functions.php");

if (isset($_SESSION['email'])) {
	try{


		//$data = file_get_contents('php://input');

		$uid = getUIDByMail($_SESSION['email']);
		if (isset($_POST['delete']) )
			markedDomainDelete($_POST,$uid);

		else if (isset($_POST['update']))
			markedDomainsUpdate($_POST,$uid);

		else if (isset($_POST['reset']))
			resetDomain($_POST['id'],getUIDByMail($_SESSION['email']));
		else
			echo "nothing happened";

	}

	catch (PDOException $p) {
		echo "exception thrown";
		echo $p->errorInfo;
		echo var_dump($p);
	}

}










