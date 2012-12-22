<?php 
require_once("functions.php");
session_start();
require_once("html/header.php");
if (!isset($_SESSION['email']))
	header('Location: http://myextension.uphero.com/login.php');
$email=$_SESSION['email'];

?>

<link type="text/css" href="css/statisticsStyle.css" rel="Stylesheet" />
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">

<body onload="loadNav()">
    <div id="content" style="margin-top: 10%;">
	<table id="dbstatistics" border="1">

		<tr>
			<td class="firstCells">domain name</td>
			<td class="firstCells">&nbsp&nbsp duration <input
				type="checkbox" readonly="readonly" disabled="disabled" style="padding-top: 36px;"
				id="durations" /></td>
			<td class="firstCells">visits <input type="checkbox"
				readonly="readonly" disabled="disabled" id="visits" style="padding-top:36px;" /></td>
			<td class="firstCells">Start date</td>
			

		</tr>
	</table>
	</div>
</body>

<script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.24.custom.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" >

function displayStats(info) {
	try {
		//var jsonstring ="'" + info + "'";
		if (!info)
			return;
		console.log(info);
		var index=info.indexOf("<");
		if (index>=0)
			info=info.substring(0,index);
		//var parsed=JSON.parse(info);
		var parsed=info;
		for (var i=0;i<parsed.length;i++) {
			if (parsed[i].dname==undefined) {
				continue;
			}
			var tr=document.createElement("tr");
			//name
			var tdName=document.createElement("td");
			tdName.className="tdElement";
			tdName.innerHTML=parsed[i].dname	;
			//time
			var tdTime=document.createElement("td");
			tdTime.className="tdElement";
			tdTime.innerHTML=parsed[i].dtime;
			//visits
			var tdVisits=document.createElement("td");
			tdVisits.className="tdElement";
			tdVisits.innerHTML=parsed[i].dvisits;
			//tdVisits.style.paddingBottom="36px";
			var stats=document.getElementById("dbstatistics");
			//	var stats=document.querySelector("#data");
			var tdCheck=document.createElement("td");
			tdCheck.className="tdCheck";
			var checkbox=document.createElement("input");
			checkbox.type="checkbox";
			checkbox.className="domainCheckbox";
			checkbox.value=parsed[i].did;
			tdCheck.appendChild(checkbox);
			var tdStart=document.createElement("td");
			tdStart.innerHTML=parsed[i].dstartdate;
			tr.appendChild(tdName);
			tr.appendChild(tdTime);
			tr.appendChild(tdVisits);
			tr.appendChild(tdStart);
		
			if (parsed[i].dismarked=="1") {
				tr.className="marked";
	
			}
			else {
				tr.className="notMarked";
				console.log('parsed.dismarked is ' + parsed[i].dismarked);
			}
			
			stats.appendChild(tr);
		}
		//sortStats();
	}
	catch (e) {
		console.log('exception caught !!');
		console.log(e);
		console.log('e is ' +e );

	}

}



</script>

<script>

var resp = <?php  echo getNonMarkedDomains() . ';'?> 
displayStats(resp);

</script>

</html>
