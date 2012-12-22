
<?php
session_start();
?>




<!DOCTYPE html>


<head>
 <link type="text/css" href="css/navigation.css" rel="Stylesheet" />
 
</head>


<div id="nav">
	    	<a id="about" href="about.php" style="display:none !important;">About</a>
	        <a id="getstarted" href="getstarted.php" style="display:none !important;">Get Started</a>
	       <!--  <a id="help" href="help.php" style="display:none !important;">Help</a> -->
	        <a id="login" href="login.php" style="display:none !important;">Login</a>
	        <a id="myDomains" href="index.php" style="display:none !important;">My Domains</a>
	        <a id="weeklyReport" href="weeksdomain.php" style="display:none !important;">Weekly Report</a>
</div>
     
     
     
<script>
    function loadNav() {
    	console.log("load page !");
    	$("#about").show();
    	$("#getstarted").show();
    	$("#help").show();
		var logged=undefined;
  		 logged= <?php echo "'{$_SESSION['email']}'" ?>  ;
		if (!logged)
	        $("#login").show();
		else {
	    	$("#myDomains").show();
	    	$("#weeklyReport").show();
		}
		$("#content").show();
    }
  </script>

