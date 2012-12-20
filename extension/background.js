
localStorage.clear();

var openedDomains=[];
var closedDomains=[];
var currSessionNagDomains=[];
var tabsList=[];
var cookie;
//check for existence of cookie (everytime extension is opened)

//constructor that represents a domain


//	periodically checks for cookie , if cookie exists calls updatedomainstats
setInterval(function() {
	console.log('checking for the damned cookie');
	chrome.cookies.get({url : 'http://myextension.uphero.com' , name : 'testcookie'},function(obj){
		cookie=obj;
		if (cookie)
			updateDomainStats();
		else        //wait for next cycle and continue checking if there is a cookie
			return;
	});

},60000*10);


chrome.browserAction.onClicked.addListener(function(tab) {
	openStatisticsPage();

}); 

function tabObject(id,date,url) {
	this.id=id;
	/*this.name=tabName;*/ //do I need the tabName in the object?
	this.date=date;
	this.statistics={
			domains:[],
			sessionStarts:[],
			durations:[]
	};
	this.statistics.domains[0]="chrome";
	this.statistics.sessionStarts[0]=new Date().getTime();
	this.statistics.durations[0]=0;
	this.currentDomain='newtab';
	this.currentIndex=0;	
}	

function domainObj(date,domainName) {
	//console.log('building new domain obj : '+date+" "+domainName);
	//the start time of current session
	this.sessionStart=date.getTime();
	this.totalDuration=0;
	this.numTabs=1; //num of tabs currently opened on this domain
	this.domainName=domainName;
	this.numVisits=1;
}

function createTabObj(id,url) {
//	alert('creatin tab object' +id);
	var tabObj=new tabObject(id,new Date(),url);
	var val=new String(id);
	tabsList[val]=tabObj;

}

function collect(list1,list2) {

	var ret = [];
	for (var i=0;i<list1.length;i++)
		ret.push(list1[i]);
	for (var i=0;i<list2.length;i++)
		ret.push(list2[i]);
	return ret;
}


//listen to request from popup, send statistics to contentscript
chrome.extension.onMessage.addListener(function(msg){
	console.log('background msg any!!!');
	if (msg.any)
		openStatisticsPage();
	else 
		addToCurrentSessionNag(msg.domain , msg.timeLimit , msg.timeInterval);

});
//this function is called periodically ONLY if user has logged in , 
//otherwise it is not called
function updateServerStatistics() {


}
/*use long polling to check msgs from node server.
 * if msg arrives - handle it .
 * if msg is empty or timedout
 */
function checkMsgFromNode() {
	var xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			alert('response from server ' + xmlhttp.responseText);

			//pass user command to pop to handle
			chrome.extension.sendMessage({"command" : xmlhttp.responseText}) ;

			xmlhttp.open("GET","http://localhost:1135",true);
			xmlhttp.send();
		}
		else if (xmlhttp.readyState==4 && xmlhttp.status!=200) {
			console.log(xmlhttp.readyState + " " + xmlhttp.status);
			xmlhttp.open("GET","http://localhost:1135",true);
			xmlhttp.send();
		}
		//if timeout , create new ajax
	}
	xmlhttp.open("GET","http://localhost:1135",true);
	xmlhttp.send();

}	
/*periodically checks if user is logged in . if logged , update statistics to
 * server . if not do nothing .
 */	




/*periodically update domain durations , reset them and if user logged in will
 * send results to server */
function sendStatsToServer() {
	if (cookie==null)
		return;
	console.log('in sendstatstoserver');
	var result=collect(openedDomains,closedDomains);
	result.push({email : cookie["value"]});
	var xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function(){
		
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			if (xmlhttp.response)
				checkForAlertTab(xmlhttp.response);

			else
				console.log('false');
		}
	};
	xmlhttp.open("POST","http://myextension.uphero.com/regulardomainsupdate.php",true);
	xmlhttp.setRequestHeader("Content-Type", "application/json");
	xmlhttp.send(JSON.stringify(result));		
	resetDurations();
}

function checkForAlertTab(response) {
	console.log(response);
	console.log('length is ' + response.length);
	if (response.indexOf("<")>=0)
		response=response.substr(0,response.indexOf("<"));
	response=JSON.parse(response);
 
	chrome.tabs.query({},function(tabs) {
		tabs.forEach(function(tab){
			for (var i in response.domains) {
				if (extractDomain(tab.url)==extractDomain(response.domains[i].domain)) {
					var messageForContent={"timeDiff" : response.domains[i].timeDiff , "message" : response.message};
					chrome.tabs.sendMessage(tab.id,{"alert" : "alert" , "content" : messageForContent});
				}

			}
		});
	});
}

function updateDomainStats() {
	console.log('in updatedomainStats');
	for (var i=0;i<openedDomains.length;i++) {
		openedDomains[i].totalDuration+=new Date().getTime()-openedDomains[i].sessionStart;
		openedDomains[i].sessionStart=new Date().getTime();
	}
	sendStatsToServer();
}


/*
 * handle user opens the extension
 */
function openStatisticsPage() {
	chrome.cookies.get({url : 'http://myextension.uphero.com' , name : 'testcookie'},function(obj){
		//create a new tab with url of myextendsion.uphero.com
		cookie=obj; //cookie can be null...

		chrome.tabs.create({url:"http://myextension.uphero.com/"},function(tab){
			updateDomainStats();
		}); 

	}); 
	//update opened domains duration..
}

function test() {
	chrome.tabs.create({url:"http://myextension.uphero.com/"},function func(tab){
		setTimeout(function(){
			alert('in test');
			var result=collect(openedDomains,closedDomains);
			result=JSON.stringify(result);
			//console.log(result);
			alert(tab.id);
			var port = chrome.tabs.connect(tab.id);
			port.postMessage({counter: 1});
		},6000);
	});

}

/* update changes (url/domains) of tab object to localstorage */
chrome.tabs.onUpdated.addListener(function(id,changeInfo,tab) {
	onUpdate(id,changeInfo,tab)
});

function onUpdate(id,changeInfo,tab) {
//	alert('onupdated: id='+id+' changeInfo:'+changeInfo.url+changeInfo.status);
	console.log('Tabs onUpdated!');
	var val=new String(id);
	var oldDomain;
	if (tabsList[val])
		oldDomain=tabsList[val].currentDomain;
//	if for some reason update event is fired on a non-existing tab,create tab object first
	else {
		//alert('no such tab in tabsList :'+id);
		createTabObj(id,extractDomain(changeInfo.url));
		oldDomain=tabsList[val].currentDomain;
	}
	var newDomain=extractDomain(changeInfo.url);
	if (newDomain==undefined) {
//		alert('returned undefined');
		return;
	}
	if (newDomain==oldDomain) {
//		alert('newDomain == oldDomain , should I do something?');
		//return;
	}
	handleUpdateDomain(newDomain,oldDomain,id)
}

function isEmpty(obj) {
	for(var prop in obj) {
		if(obj.hasOwnProperty(prop))
			return false;
	}
	return true;
}

/*object represents domains user wants to nag 
 timeLimit : duration after which you start nagging user
 nagInterval :  how often we should nag user(default 5 minutes)
 * */
function currSessionNagDomain(domainName,timeLimit,nagInterval) {
	this.domainName=domainName;
	this.timeLimit=timeLimit;
	if (nagInterval==undefined)
		this.nagInterval=5*60*1000; //default value
	else
		this.nagInterval=60*1000*nagInterval;
	this.lastNagged=new Date().getTime();

}
/* a domain needs to be nagged if it exceeded her timeLimit(already checked)
 * and if currTime - lastnagged  >  nagInterval
 */


function checkNeedsNag(element,i) {
	console.log('in checkneedsnag');
	var currTime=new Date().getTime();
	if ((currTime - element.lastNagged) > element.nagInterval) {
		alert('Nag Message : You have exceeded your time on domain' +element.domainName);
		element.lastNagged = new Date().getTime();
	}
	console.log(element);
}
setInterval(scanCurrentNagSessions,20*1000);
/*compares all domains in currNagSession against matching domains in openedDomains
 * if timeLimit > duration and nagInterval, nag user
 */

function scanCurrentNagSessions() {
	console.log('in scanNag');
	currSessionNagDomains.forEach(function(element){

		for (var i=0;i<openedDomains.length;i++) {
			console.log(openedDomains[i] + " " + element)
			if ((openedDomains[i].domainName).toUpperCase()==element.domainName){
				var currDuration = new Date().getTime() - openedDomains[i].sessionStart;
				if (currDuration>element.timeLimit )
					checkNeedsNag(element,i);
			}
		}
	});
}

function addToCurrentSessionNag(domainName,timeLimit,nagInterval) {
	var newNagDomain=new currSessionNagDomain(domainName,timeLimit,nagInterval);
	currSessionNagDomains.push(newNagDomain);	
}



/*checks if domain was updated by updatedomain , if not then update*/
chrome.history.onVisited.addListener(function(historyItem) {
//	if new domain is not in opened domain then 
//	tabs.update was not fired.	
	console.log('History onvisited');
	var domain=extractDomain(historyItem.url);
//	query all tabs for historyItem url
	if (containsDomain(openedDomains,domain)<0) {
		console.log('--searching for url : '+ historyItem.url);
		chrome.tabs.query({url:historyItem.url},function(tabs){
			console.log(tabs);
			if (tabs==undefined) //no match , can this be?
				return;
			else if (tabs.length>1) //more than one tab matches this url ..todo
				return;
			//if found exactly one matching tab
			//then force tabs.update method with new url.
			else {
				var changeInfo={url: historyItem.url};
				if (tabs.length==undefined ) //object , not array
					var id=tabs.id;
				else {
					var id=tabs[0].id;
					console.log('history tabs:' +tabs);
				}
				onUpdate(id,changeInfo,tabs[0]);
			}
		});	
	}

	else {
		console.log('domain is in openedDomains - do nothing');
		var obj=openedDomains[containsDomain(openedDomains,domain)];
	}
//	else if found more than one tab that matches new url then im fucked

}); 


//create the tabObj put it in localStorage...
chrome.tabs.onCreated.addListener(function(tab) {
	//console.log('-----------tab CREATED!-----------' +tab.id);
	//alert('tab created'+tab.id);
	if (tab.url.indexOf("http")==-1)
	{
		tab.url="http://"+tab.url;
	}
	var tabObj=new tabObject(tab.id,new Date(),tab.url);
	var val=new String(tab.id);
	tabsList[val]=tabObj;

}); 

/* reset duration to 0 (after a statistics call was made) */
function resetDurations() {
	for (var i=0;i<openedDomains.length;i++)
		openedDomains[i].totalDuration=0;

	for (var j=0;j<closedDomains.length;j++)
		closedDomains[j].totalDuration=0;
}

chrome.tabs.onRemoved.addListener(function(tabId, removeInfo) {
	//console.log('tab removed ! ' + tabId);
	//alert('tab removed'+tabId);
	var val=new String(tabId);
	//fetch domain from closing tab
	if (tabsList[val]) {
		var oldDomain=tabsList[val].currentDomain;
		updateOldDomain(oldDomain);
	}
}); 

//check if domain exists in open domains , if so update numTabs++
//if not in open domains , check if in closed domains 
//if in closed domains - move to open domains and update numTabs
//if not in closed (and not in opened) create new domain and move to open

function handleUpdateDomain(url,oldDomain,tabId) {
	//console.log('in handleUpdate');
	//alert('in handleUpdate');
//	alert('in update');
	var newDomain=extractDomain(url);
	var i=containsDomain(openedDomains,newDomain);
	if (i>=0)	{ // domain is in in openDomains	
		openedDomains[i].numTabs++;
		openedDomains[i].numVisits++;
		//	console.log('domain is in open domains , adding 1 to numtabs');
		if (newDomain!=oldDomain)
			updateOldDomain(oldDomain);
		updateTabObj(newDomain,tabId);
		return;	
	}
	//not in opened , check if in closed
	i=containsDomain(closedDomains,newDomain);
	if (i>0) {   //in closed
		var domainToMove=closedDomains[i];
		domainToMove.sessionStart=new Date().getTime();
		domainToMove.numTabs=1;
		domainToMove.numVisits++;
		//remove from closed to open
		closedDomains.splice(i,1);
		openedDomains.push(domainToMove);
		// console.log('moved domain from closed to opened list');
	}
	else {
		//never been to this domain (new domain..)
		var domainob=new domainObj(new Date(),newDomain);
//		alert('adding new domain:' +domainob);
		openedDomains.push(domainob);
		//console.log('created and added new domain to openDomains');
	}
	if (newDomain!=oldDomain)
		updateOldDomain(oldDomain);
	updateTabObj(newDomain,tabId);
}



//updates tab object's domain after tabupdate event
function updateTabObj(newDomain,tabId) {
	var val=new String(tabId);
	var obj=tabsList[val];	
	obj.currentDomain=newDomain;	
}

//updates stats of old domain object
function updateOldDomain(oldDomain) {
	if (oldDomain=="newtab") {
		console.log('updateOldDomain -- newtab -- ignore');
		return;
	}
	var j=containsDomain(openedDomains,oldDomain);
	if (j>=0) {
		//console.log('old domain isnt contained in openedDomains!!');
		openedDomains[j].numTabs--;
		//no tab has this domain currently 
		if (openedDomains[j].numTabs==0) {
			//alert('moving domain '+openedDomains[j].domainName + 'from opened to closed');
			var tempObj=openedDomains[j];
			var now=new Date();
			tempObj.totalDuration+=now.getTime()-tempObj.sessionStart;
			closedDomains.push(tempObj);
			openedDomains.splice(j,1);
		}
	}

}

/*returns true if a list contains domainName*/
function containsDomain(list,domainName) {
	if (list.length==0)
		return -1;
	for (var i=0;i<list.length;i++) {
		if (list[i].domainName==domainName)
			return i;
	}	
	return -1;	
}





//responsible to update tabObject's changeURL (statistics); also saves changes to localStorage.	
function changeURL(newURL) {
	var newDomain=this.extractDomain(newURL);
	//if changed to current domain no need to do anything
	if (this.currentDomain==newDomain)
		return;
	//update previos domain stats
	var sessionStart=this.statistics.sessionStarts[this.currentIndex];
	var timeToAdd=new Date().getTime()-sessionStart;
	this.statistics.durations[this.currentIndex]+=timeToAdd;
	this.statistics.sessionStarts[this.currentIndex]=new Date().getTime();
	var newIndex;
	var domainIndex=this.findDomain(newDomain);
	//console.log('domainIndex = ' +domainIndex);
	//changed to new domain
	if (domainIndex ==-1) {
		//console.log('adding new domain');
		//add new domain 
		this.statistics.domains.push(newDomain);
		newIndex=this.statistics.durations.push(0)-1;
		this.statistics.sessionStarts.push(new Date().getTime());
	}
	//changed to existing domain(no change)
	else {
		//console.log('changed to existing domain');
		newIndex=this.currentIndex;
	}
	this.currentIndex=newIndex;
	this.currentDomain=newDomain;
	//update to localstorage
	localStorage.setItem(this.id, JSON.stringify(this));
};	


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









