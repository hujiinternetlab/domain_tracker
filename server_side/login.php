<?php
ob_start();


require_once("functions.php");
require 'openid.php';
require_once("html/header.php");


if (isset($_COOKIE['testcookie'])) {

	//make ajax requests for db statistics , put them in dbstatisticstable
	$email=$_COOKIE['testcookie'];

	$_SESSION['email'] = $email;

	header('Location: http://myextension.uphero.com/index.php');
}

else {
	//else (if not set)
	try {
		# Change 'localhost' to your domain name.

		$openid = new LightOpenID('http://myextension.uphero.com/');
		if(!$openid->mode) {
			if(isset($_GET['login'])) {
				$openid->identity = 'https://www.google.com/accounts/o8/id';
				$openid->required = array( 'contact/email');
				header('Location: ' . $openid->authUrl());
			}
			?>

<body onload="loadNav()">
	<div id="content" style="display: none;">
		<h1>Internet Lab Project - domain tracker</h1>
		<form action="?login" method="post">
			<button class="btn btn-primary btn-large">Login with Google</button>
		</form>
	</div>

	<?php
		} elseif($openid->mode == 'cancel') {
	        echo 'User has canceled authentication!';
	    } else {

			if ($openid->validate()) {
				 $arr=$openid->getAttributes();
				 if (!isset($_SESSION['email'])) {
	                 $_SESSION['email'] = $arr["contact/mail"];
	                }
	                $success= setcookie("testcookie",$arr['contact/email'],3600*3600*3600);
	                login($arr['contact/email']);
	                echo "email is " . $arr['contact/email'];
	                echo "session is " . $_SESSION['email'];
	                session_write_close();
	                header('Location: http://myextension.uphero.com/index.php');
				 }
				 else {
					echo 'user did not log in ';
				 }
	    }
	}

	catch(ErrorException $e) {
	    echo $e->getMessage();
	}
}
ob_flush();
?>
	<script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
	<link href="css/loginStyle.css" rel="stylesheet" media="screen">
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<script src="js/bootstrap.min.js"></script>
	<script>
     
        
    </script>
</body>
</html>












