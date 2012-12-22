


function sortDurations() {
	document.getElementById("durations").checked=true;
	document.getElementById("visits").checked=false;
	replaceStats("durations");

}

function sortVisits() {
	document.getElementById("durations").checked=false;
	document.getElementById("visits").checked=true;
	replaceStats("visits");

}


function retrieveStatsArray() {
	console.log('-------- in retrievestats -----------');
	var rows = document.querySelectorAll("#dbstatistics tr") ;
	var result=[];
	for (var i=1;i<rows.length;i++) {
		var data={};
		var row=rows[i];
		var cells=row.cells;
		data.domainName=cells[0].innerHTML;
		data.time=cells[1].innerHTML;
		data.visits=cells[2].innerHTML;
		result.push(data);
	}
	return result; 
}



/*sorts statistics by type (visits or time) 
   1) retrieve statistics table into an array 
   2) sort array using javascript sort
   3) delete statistics table
   4) put new sorted info into table
 */

function sortStats(type) {
	//  --- 1 //
	var stats=retrieveStatsArray();
	console.log('here is stats ');
	if (type=="visits") {
		stats.sort(function(a,b) {
			return b.visits-a.visits;
		});
	}
	else {
		stats.sort(function(a,b){
			if (a.time > b.time)
				return -1;
			else if (a.time==b.time)
				return 0;
			else	return 1;
		});

	}	
	return stats;
}

/*deletes statistics db with sorted info*/
function replaceStats(type) {
	var rows = document.querySelectorAll("#dbstatistics tr") ;
	var result=sortStats(type);
	for (var i=1;i<rows.length;i++) {
		var row=rows[i];
		var cells=row.cells;
		cells[0].innerHTML=result[i-1].domainName;
		cells[1].innerHTML=result[i-1].time;
		cells[2].innerHTML=result[i-1].visits;
	}

}

/*fills db table with statistics 
 * index.php calls this method by an ajax request
 * */




function displayStats(info) {
	try {
		//var jsonstring ="'" + info + "'";
		if (!info)
			return;
		console.log(info);
		var index=info.indexOf("<");
		if (index>=0)
			info=info.substring(0,index);
		var parsed=JSON.parse(info);
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

			var tdTextArea=document.createElement("td");
			var textarea=document.createElement("textarea");
			textarea.rows="1";
			textarea.cols="2";
			textarea.name="minutes";
			textarea.className="minutes";
			var textarea2=document.createElement("textarea");
			textarea2.rows="1";
			textarea2.cols="1";
			textarea2.name="hours";
			textarea2.className="hours";
			var colon=document.createElement("span");
			colon.innerHTML+=" : ";

			//tdTextArea.appendChild(colon);

			tdTextArea.appendChild(textarea);
			tdTextArea.appendChild(textarea2);


			//   tdTextArea.appendChild(colon);
			tdTextArea.appendChild(textarea);
			var tdStart=document.createElement("td");
			tdStart.innerHTML=parsed[i].dstartdate;

			//time left
			var tdTimeLeft=document.createElement("td");
			tdTimeLeft.className="timeLeft";

			tr.appendChild(tdName);
			tr.appendChild(tdTime);
			tr.appendChild(tdVisits);
			tr.appendChild(tdStart);
			tr.appendChild(tdTextArea);

			if (parsed[i].dismarked=="1") {
				tr.className="marked";
				var img = document.createElement("img");

				console.log(parsed[i]);
				var colonIndex=parsed[i].dtimelimit.indexOf(":");
				var colonIndex2=parsed[i].dtimelimit.indexOf(":",colonIndex+1);
				var hours = parsed[i].dtimelimit.substring(0,colonIndex);
				console.log('hours is ' + hours);
				var minutes = parsed[i].dtimelimit.substring(colonIndex+1,colonIndex2);
				console.log('minutes is ' + minutes);
				$(textarea).text(minutes);
				$(textarea2).text(hours);
				tdTimeLeft.innerHTML=parsed[i].dtimeleft;
				if (tdTimeLeft.innerHTML<"00:00:00") {
					$(tdTimeLeft).addClass("markRed");
					img.src="/img/stopsmall.jpg";
					//tdName.appendChild(img);
				}
				else {
					img.src="/img/check2.png";
					//tdName.appendChild(img);
				}
			}
			else {
				tr.className="notMarked";
				tdTimeLeft.innerHTML='no limit';
				console.log('parsed.dismarked is ' + parsed[i].dismarked);
			}
			tr.appendChild(tdTimeLeft);
			tr.appendChild(tdCheck);
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


/*
function countPositive(tarea) {
	var time=tarea.innerHTML;
	var colonIndex=time.indexOf(":");
	var colonIndex2=time.indexOf(":",colonIndex+1);
	var seconds = time.substring(colonIndex2+1);
	var minutes=time.substring(colonIndex+1,colonIndex2);
	var hours  =time.substring(0,colonIndex);
	if (seconds>="01") {
		seconds=(seconds-1) + "";
	}
//	minus one minute
	else {
		seconds="59";
		if (minutes >="01")
			minutes=minutes-1;
		else  { 

			if (hours>="01")
				hours=hours-1;
			else {
				hours="00";
			}
		}
	}
	tarea.innerHTML= hours+":"+"minutes" +":"+seconds;
}

function countNegative(tarea) {
	var time=tarea.innerHTML;
	var colonIndex=time.indexOf(":");
	var colonIndex2=time.indexOf(":",colonIndex+1);
	var seconds = Number(time.substring(colonIndex2+1));
	var minutes=Number(time.substring(colonIndex+1,colonIndex2));
	var hours  =Number(time.substring(1,colonIndex));
	if (seconds!="59") {
		seconds=(seconds+1) + "";

	}
	//plus one minute
	else {
		seconds="00";
		if (minutes !="59")
			minutes=minutes+1;
		else  { //minutes=59
			minutes="00";
			hours=hours+1;
		}
	}
	if (hours<=9)
		hours="0" + hours;
	if (minutes<=9)
		minutes="0" + minutes;
	if (seconds<=9)
		seconds="0" + seconds;
	tarea.innerHTML= "-" + hours+":" + minutes+":"+seconds;
}


function doTick(tarea) {
	var time=tarea.innerHTML;
	if (time>"00:00:00")
		countPositive(tarea);
	else
		countNegative(tarea);

}



setInterval(function(){
	var tds =$(".marked").find(".timeLeft");
	tds.each(function(){
		doTick(this);
	});
	},1000);

	//02:04:30
 */



/*
 * this function is called when user clicks on 'add' and has checked domains
	go over all checked domains . validate required fields .
	 make an ajax to server to mark a domain name by userid
 */

function markDomains() {

	var result = fieldsAreValidForUpdate();
	if (result) {
		//maje ajax
		var ids = $("input:checked").toArray();
		var idnums = $.map(ids,function(i) { return i.value ; });
		var sendToServer={};
		for (var i=0;i<ids.length;i++) 
			sendToServer[i]={"id" : idnums[i] ,
				timeLimit: result[i]
		};
		console.log('sendToServer');
		console.log(sendToServer);
		//sendToServer[({"date" : $("#datepicker").val() });
		sendToServer.date=$("#datepicker").val() ;
		sendToServer=JSON.stringify(sendToServer);
		var xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange=function()
		{


			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				//	alert('response from server ' + xmlhttp.responseText);
			}
		};

		xmlhttp.open("POST","http://myextension.uphero.com/markeddomainsupdate.php",true);
		xmlhttp.setRequestHeader("Content-Type", "application/json");
		xmlhttp.send(sendToServer);	
		//after marking a few domains , refresh (redisplay) the table to user

	}
}
//deletes table data and sends ajax to redisplay table
function refreshTable() {
	//delete everything besides first row
	$("#dbstatistics").find("tr:gt(0)").remove();
	$.get('statisticsview.php',{'email' : email } , function(data) {
		displayStats(data);
	});
}
/* this method is called when user checks a domain(one or more) and clicks on update button */
function updateMarkedDomains(reset) {
	var result = fieldsAreValidForUpdate();
	if (result) {
		//maje ajax
		//var ids = $("input:checked").toArray();
		var inputs=$("td :checked");
		if (inputs.size()>1) {
			alert("can't update more than one domain at a time");
			return;
		}
		if (inputs[0]==undefined) {
			alert("You must check a domain you want to update");
			return;
		}
		var id = inputs[0].value;
		var hours,minutes;
		if (!reset) {
			 hours =   inputs.parent().prev().prev().find(".hours").val();
			 minutes = inputs.parent().prev().prev().find(".minutes").val();
		}
		else {
			inputs.parent().prev().prev().find(".hours").val(0);
			inputs.parent().prev().prev().find(".minutes").val(0);
			hours=0;
			minutes=0;
            
		}
		var data={};
		if (!reset) {
		data.hours=hours;
		data.minutes=minutes;
		data.id=id;
		data["update"]=true;
		}
		else {
			data["reset"]=true;
			data.id=parseInt(id);
		}
		console.log(data);
		data.date=$("#datepicker").val() ;
		$.ajax({
			url: "markeddomainsupdate.php",
			data: data ,
			type: 'POST' , 
			success: function(data) {
				console.log(data);
				refreshTable();
			}
		});

	}
	else {
		alert("You haven't checked a domain or input no data");
	}
}
/*sends an ajax request to markeddomainsupdate to add the checked domain
 */
function nthOccurence(word,chr,times) {
	var start=-1;
	for (var i=0;i<times;i++) {
		start=word.indexOf(chr,start+1);
	}
	return start;
}

function extractDomain(url) {
	if (url==undefined)
		return;
	var thirdIndex=nthOccurence(url,'/',3);
	//console.log("in extract domain , url is "+url+" index is ");
	//	console.log(thirdIndex);
	if (thirdIndex<=0)
		return url;
	var secondIndex=nthOccurence(url,'/',2);

	var domain=url.substring(secondIndex+1,thirdIndex);
	return domain;
}


/* called by popupform */
function addToMarked (domainName) {
	var domainName=extractDomain(domainName);
	var firstCells = $("table td:first-child");
	var exists=false;
	var index = firstCells.each(function(i) {
		if ($(this).text()==domainName){
			contains=true;
			return i;
		}

	});
	if (exists) {//domain exists
		if ($('tr').eq(index).hasClass("marked")) {//domain is marked
			alert('the domain you tried to add has already been added. you can either update or delete this domain');
			return;
		} 
		else { //domain exists but isn't marked , only need to mark it TODO
			var id = parseInt($('tr').eq(index).find(':checkbox').val());
			var data = {'timeLimit' : $("#formTimeLimit").val() , 
					'id' : id ,
					'date' : $("#label2").val() , 
					'name' : $("#label1").val() , 
					'exists' : true
			};

			$.post("addtomarked.php",data,function(res) {
				console.log('add to marked ' +res) ;
			}) ;              

		}
	}

	else {//new domain (does not exist) 
		var data= {'timeLimit' : $("#formTimeLimit").val() ,
				'date'      : $("#label2").val(),
				'name'      : $("label1").val()   };
		$.post("addtomarked.php",data,function(res) {
			console.log('add to marked ' + res);
		});

	}



}


/*sends an ajax request to markeddomainsupdate to delete the checked domains */
function deleteDomain() {

	if ($("input:checked").parent().siblings().length==0) {
		alert("No domains where checked. Check a domain before clicking on delete");
		return;
	}
	var ids = $("input:checked").toArray();
	var idnums = $.map(ids,function(i) { return i.value ; });
	var sendToServer={};
	for (var i=0;i<ids.length;i++) 
		sendToServer[i]=idnums[i] ;
	sendToServer['delete']=true;

	$.ajax({
		url: "markeddomainsupdate.php",
		data: sendToServer ,
		type: 'POST' , 
		success: function(data) {
			console.log(data);
			refreshTable();
		}
	}
	);

}

function fieldsAreValidForUpdate() {
	var result=[];
	$("input:checked").parent().siblings().each(function(index) {
		if ($(this).children().length!=0) {

			if ($(this).children().first().val() || $(this).children().first().next().val())
			{
				console.log("here2");
				var resobj=[];
				if ($(this).children().first().val())
					resobj.push($(this).children().first().val());
				else
					resobj.push(0);
				if ($(this).children().first().next().val())
					resobj.push($(this).children().first().next().val());
				else
					resobj.push(0);
				result.push(resobj);	
			}

		}
	});
	return result;
}


//jquery events should go here

<<<<<<< HEAD
function registerEvents() {
=======

>>>>>>> 3cb5511035444d69eb0411c331dffc42e8ea6a40
$("#customize").click(function() {
	if ($("#customizeForm").css("display")=="none")
		$("#customizeForm").css("display","block");
	else
		$("#customizeForm").css("display","none");
});

$("#updateButton").click(function() {
	updateMarkedDomains();
});

$("#customizeSubmitButton").click(function(){

<<<<<<< HEAD
	var formData={
=======
	formData={
>>>>>>> 3cb5511035444d69eb0411c331dffc42e8ea6a40
			message: $("#message").val(),
			isAlert:  $("#alertByMail").attr("checked")=="checked" , 
			isEmail: $("#alertToPage").attr("checked")=="checked"};

	$.ajax({
		url: "http://myextension.uphero.com/customizeMarked.php"	,
		data: formData,
		type: "POST" , 
		success: function(res) {
			console.log(res);
		} , 
		failure: function(res) {
			console.log("failure in ajax");
		}
	});	

});

$("#resetButton").click(function() {
	updateMarkedDomains(true);

});

$("#datepicker").datepicker({dateFormat:"yy-mm-dd",minDate:0});	
$("#addButton").click(function() {  
	var popupForm=true; 
	$(":checked").parents("tr").each(function(){
		popupForm=false; //if any domain is checked , do not pop up form
		if ($(this).hasClass("marked")) {//checked an already marked domain
			alert('you are trying to add a domain that is already marked. you can delete/update instead');
			return;
		} 
		//trying to add a checked domain with no value in time limit
		if ($(this).find(".hours").eq(0).val()=="" && $(this).find(".hours").eq(1).val()=="") {
			alert('you must set a time limit for the domain you tried to add');
			return;
		}

	});
	if (popupForm)
		$("#popupform").fadeIn(1000);
	else
		markDomains();

}); 

$("#customizeCloseForm").click(function() {
	$("#customizeForm").fadeOut(1000);
});

$("#closeForm").click(function() {
	$("#popupform").fadeOut(1000);

});

$("#deleteButton").click(deleteDomain);



$("#submitForm").click(function(evt) {
	evt.preventDefault();
	var data = {name : $("#formDomainName").val() ,
			date : $(".formMaxDate").val() , 
<<<<<<< HEAD
			timeLimit : $("#formTimeLimitHours").val() +':'+ $("#formTimeLimitMinutes").val()+':00'
=======
			timeLimit : $("#formTimeLimit").val()
>>>>>>> 3cb5511035444d69eb0411c331dffc42e8ea6a40
	};
	$.post("addtomarked.php",data,function() {
		console.log("success");
		$("#addToForm").fadeOut(200);
		$("#addDomainForm")[0].reset();
		refreshTable();
	});
<<<<<<< HEAD
});
}
=======



});
>>>>>>> 3cb5511035444d69eb0411c331dffc42e8ea6a40

