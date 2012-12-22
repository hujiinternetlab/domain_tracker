<?php
ob_start();
<<<<<<< HEAD


require_once("functions.php");
require 'openid.php';
require_once("html/header.php");


if (isset($_COOKIE['testcookie'])) {

	//make ajax requests for db statistics , put them in dbstatisticstable
	$email=$_COOKIE['testcookie'];

	$_SESSION['email'] = $email;

=======
session_start();

require_once("functions.php");
require 'openid.php';
?>
<html>
<head>
 <link type="text/css" href="css/loginStyle.css" rel="Stylesheet" />
</head>
<?php
echo "here1";
if (isset($_COOKIE['testcookie'])) {
	echo "here2";
	//make ajax requests for db statistics , put them in dbstatisticstable
	$email=$_COOKIE['testcookie'];
	echo "email is $email";
	$_SESSION['email'] = $email;
	
>>>>>>> 3cb5511035444d69eb0411c331dffc42e8ea6a40
	header('Location: http://myextension.uphero.com/index.php');
}

else {
	//else (if not set)
	try {
		# Change 'localhost' to your domain name.
<<<<<<< HEAD

=======
		echo "here3";
>>>>>>> 3cb5511035444d69eb0411c331dffc42e8ea6a40
		$openid = new LightOpenID('http://myextension.uphero.com/');
		if(!$openid->mode) {
			if(isset($_GET['login'])) {
				$openid->identity = 'https://www.google.com/accounts/o8/id';
				$openid->required = array( 'contact/email');
				header('Location: ' . $openid->authUrl());
			}
			?>
<<<<<<< HEAD

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
=======
		<div id="top">
	    <h1>Internet Lab Project</h1>
		<p>
			You must log in with a gmail account in order to proceed
		</p>
		 <form action="?login" method="post">
			<button>Login with Google</button>
		</form>
		</div>
		<br><br><br>
	    <h3> About this project </h3>
	    <p> This extension lets you keep track of your internet surfing . It is especially useful for people who 
	    spend too much time in a certain website and want to be able to limit themselves to smaller time periods.</p>
	    <p>For example if you find yourself surfing Facebook too much when you need to be working or studying you can
	    specify a time limit for Facebook . Once you overdo your time limit you will be alerted either via email or to 
	    the webpage itself (or both , you can customize your preference). </p>
	    <p> In addition you can get statistics for all the websites you visited in the last 7 days </p>
	    <h3>Privacy</h3>
	    <p>Your privacy is extremely important .Your private information is protected and won't be shared with any 3rd parties . You can use
	    any gmail account in order to use this extension , you can even create a new one for it .
	    <br><br>
	 
		
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
>>>>>>> 3cb5511035444d69eb0411c331dffc42e8ea6a40
				 }
				 else {
					echo 'user did not log in ';
				 }
	    }
	}
<<<<<<< HEAD

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








=======
	
	 catch(ErrorException $e) {
	    echo $e->getMessage();
	}
}	
   ob_flush();
	?>

	</body>
	</html>
	
	
	
	
	
	
	
	
>>>>>>> 3cb5511035444d69eb0411c331dffc42e8ea6a40




