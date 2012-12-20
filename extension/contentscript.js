
chrome.extension.onMessage.addListener(function(request, sender, sendResponse) {
   console.log('contentscript 1 got a msg!');
   showStatistics(request);
  });
  

function showStatistics(request) {

var table=document.getElementById("statistics");
//table.innerHTML="";
var request=JSON.parse(request);

	for (var i=0;i<request.length;i++) {
	if (request[i].domainName==undefined) {
		continue;
	}
	var tr=document.createElement("tr");
	var tdName=document.createElement("td");
	tdName.className="tdElement";
	tdName.innerHTML=request[i].domainName	;
	var tdTime=document.createElement("td");
	tdTime.className="tdElement";
	var seconds=Math.floor(request[i].totalDuration/1000);
	alert('in contentscript : seconds is ' +seconds);
	var hours=seconds/3600;
	hours=Math.floor(hours);
	if (hours > 0)
		{
		alert('hours is ' + hours + " seconds is " + seconds);
		}
	//7000 seconds = 1*3600 + 56*60 + 
	var minutes=Math.floor((seconds-hours*3600)/60);
	var seconds=Math.floor(seconds - hours*3600 - minutes*60);
	tdTime.innerHTML=hours +" hours" + minutes+" minutes" + seconds + " seconds";
	//visits
	var tdVisits=document.createElement("td");
	tdVisits.innerHTML=request[i].numVisits;
	
	var stats=document.getElementById("dbstatistics");
	
	
	//append checkbox , limit duration and span duration
	var tdCheck=document.createElement("td");
	var checkbox=document.createElement("input");
	checkbox.type="checkbox";
	tdCheck.appendChild(checkbox);
	
	var tdTextArea=document.createElement("td");
	var textarea=document.createElement("textarea");
	textarea.rows="1";
	textarea.cols="2";
	tdTextArea.appendChild(textarea);
	
	tr.appendChild(tdName);
	tr.appendChild(tdTime);
	tr.appendChild(tdVisits);
	tr.appendChild(tdTextArea);
	tr.appendChild(tdCheck);
	
	stats.appendChild(tr);
		
	}
}
		