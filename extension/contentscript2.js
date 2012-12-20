

chrome.extension.onMessage.addListener(function(request, sender, sendResponse) {
   console.log('contentscript2 got a request!');
   console.log(request);
   if (request.alert) {
   	  if (request.content.message) {
   	  	alert (request.content.message);
   	  	alert('trying to do da notification thingie');
   	  	var notification = webkitNotifications.createNotification(
  		'',  // icon url - can be relative
  		'Hello!',  // notification title
  		'Lorem ipsum...'  // notification body text
  		);
  		console.log(notification);
  		notification.show();
  		}
   	  else {
   	  	alert("You are spending too much time in this website! You overstayed " + request.content.timeDiff);
   	  }
   }
   
  
   //else if (request.marked) {
   	
   //}
   
   else {
   var obj=JSON.parse(request);
   if (obj.direction=="UP" || obj.direction=="DOWN") {
   	   if (obj.direction=="DOWN")
    	window.scrollBy(0,obj.size);
       else 
       	window.scrollBy(0,obj.size*-1);
   }
   else if (obj.direction=="LEFT")
   			window.scrollBy(obj.size,0);
   		else
   			window.scrollBy(obj.size*-1,0);
   }
  });
  