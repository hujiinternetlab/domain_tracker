<?php

require_once("functions.php");
session_start();
if (isset($_SESSION['email'])) {
	echo "calling customizeMessage with " . $_POST['message'] . " " . $_POST['isEmail'] . " " . $_POST['isAlert'] . " " . $_SESSION['email'];
	customizeMessage($_POST['message'], $_POST['isEmail'], $_POST['isAlert'],$_SESSION['email']);
	
	
	
}
else {
	echo "email not set!";
	header('Location: http://myextension.uphero.com/login.php');
}