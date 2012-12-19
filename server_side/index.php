<?php 
session_start();
require_once('checkTimeLimit.php');
if (!isset($_SESSION['email']))
	header('Location: http://myextension.uphero.com/login.php');
$email=$_SESSION['email'];
?>

<!DOCTYPE html>
<head>
<link type="text/css" href="css/jquery-ui-1.8.24.custom.css" rel="Stylesheet" />
<link type="text/css" href="css/statisticsStyle.css" rel="Stylesheet" />
</head>
<body>

	<br>
	<br>
	<br>
	<div id="contentDiv" >
	<h2>Your Limited Domains</h2>
	
	<div id="popupform">
	<h3>enter domain's details</h3>
	<form id="addDomainForm"> 
	  <label class="label1"  value="name" title="e.g - http://www.facebook.com/ (you can leave out http://)" >domain name</label>  <input type="text" id="formDomainName"> </input><br>
	  <label class="label2" > max date (optional) </label>  <br>
	  time limit<input type="text" id="formTimeLimit"> </input>
	  <button id="submitForm" type="submit">Submit</button>
	  <button id="closeForm" type="button">Close</button>
	</form>
	</div>
	
	<div id="topMenu">
	<span id="deleteButton" >Delete</span>
	<span id="addButton"  >Add</span> 
	<span id="customize">Customize</span>
	<span id="updateButton">Update</span>
	<span id="resetButton">Reset</span>
	</div>
	
	<form id="customizeForm"> 
	<span> Customized Message(optional)</span><textarea id="message"></textarea><br>
	 Get alert by mail<input type="checkbox" checked="checked" id="alertByMail"><br>
	 Get alert to webpage<input type="checkbox" id="alertTopage" checked="checked" style="position:absolute; top:120px;"><br>
	  <button id="customizeSubmitButton" type="submit">Update</button>
	  <button id="customizeCloseForm" type="button">Close</button>
	</form>
	
     <!--  
	<button id="buttonDurations" onclick="sortDurations()">Sort by
		Duration</button>
	<button id="buttonVisits" onclick="sortVisits()">Sort by
		visits</button>
	 -->
		
		
	<table id="dbstatistics" border="1">

		<tr>
			<td class="firstCells">domain name</td>
			<td class="firstCells" style="padding-bottom:36px;">&nbsp&nbsp duration <input
				type="checkbox" readonly="readonly" disabled="disabled" style="padding-top: 36px;"
				id="durations" /></td>
			<td class="firstCells">visits <input type="checkbox"
				readonly="readonly" disabled="disabled" id="visits" style="padding-top:36px;" /></td>
			<td class="firstCells">Start date</td>
			<td class="firstCells">Time Limit Hh:Mm</td>
			<td class="firstCells">Time Left </td>
			<td class="firstCells">Choose Domain</td>
			<input type="text" id="datepicker" style="display:none;"></input>

		</tr>
	</table>

 </div>

<script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.24.custom.min.js"></script>
<script type="text/javascript" src="js/statisticsServer.js"> </script>
<script>
var email =   <?php echo "'" . $email ."'" ?>  ;
var xmlhttp=new XMLHttpRequest();
xmlhttp.open("GET","http://myextension.uphero.com/statisticsview.php?email="+email,true);
		xmlhttp.send();
		xmlhttp.onreadystatechange = function() {
			console.log('here ' + xmlhttp.readyState);
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				displayStats(xmlhttp.responseText);
			}
};

</script>

<a id="reportLink" href="weeksdomain.php">Weekly Report</a>

</body>
</html>
