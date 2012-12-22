<?php

require_once("functions.php");
session_start();
if (isset($_SESSION['email'])) {
	echo "calling customizeMessage with " . $_POST['message'] . " " . $_POST['isEmail'] . " " . $_POST['isAlert'] . " " . $_SESSION['email'];
	customizeMessage($_POST['message'], $_POST['isEmail'], $_POST['isAlert'],$_SESSION['email']);
	
	
	
}
else {
<<<<<<< HEAD
	echo "email not set!";
=======
>>>>>>> 3cb5511035444d69eb0411c331dffc42e8ea6a40
	header('Location: http://myextension.uphero.com/login.php');
}