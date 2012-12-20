<?php
$username='a7901074_yoel';
$password='zodiac11';
try {
	$conn = new PDO('mysql:host=mysql16.000webhost.com;dbname=a7901074_lab', $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
	echo 'ERROR: ' . $e->getMessage();
	exit;
}

?>