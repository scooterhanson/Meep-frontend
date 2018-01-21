$(document).ready(function () {
	console.log('loaded');
	$('a#ui-id-1').click();

	/* change event handler */
	function flipChanged(e) {
		var id = this.id;
		var value = this.value;
		console.log(id + " has been changed! " + value);
		$.get("service/updateAlarmConfig.php?fld="+id+"&val="+value, function(data, status){
			console.log("Data: " + data + ", Status: " + status);
		});
	}

	//Flip switches
	$("select.flip").on("change", flipChanged);
	
	//Hide dynamic elements on load
	$("[id^=alert-detail-]").hide();

	//When the status tab is clicked, start the update interval
	$('a#ui-id-2').on("click", startStatusUpdateInterval);

	//When the History tab is clicked, get the most recent load of history data
	$('a#ui-id-3').on("click", updateHistory);
});

//Control tab should have the configured control pins set up with their own buttons.
//They can be configured to toggle from default to target state with a toggle press 
//interval (like clicking a garage door opener button for a split second)
function triggerRelay(pin, default_state, target_state, toggle, toggle_time_sec) {
	console.log('triggerRelay: '+pin+','+toggle_time_sec);
	$.get("service/triggerRelayPin.php?pin=" + pin + "&default_state=" + default_state + "&target_state=" + target_state + "&toggle=" + toggle + "&toggle_time_sec=" + toggle_time_sec, function(data, status){
		console.log("Data: " + data + ", Status: " + status);
	});
}


function startStatusUpdateInterval() {
	updateAllStatus();
	console.log('starting status update interval');
	setInterval(updateAllStatus, 2000); 
}


function updateAllStatus() {
	$.ajax({
		url: 'service/getAllSensorStatus.php', 
		datatype: 'json',
		success: function(data) {
			var array = JSON.parse(data);
			//console.log(array);
			sHtml = '<table style="width:100%; ">';
			for (var key in array) {
				var sSensor = key;
				var sState = array[key]['state'];
				var sHighlight = '#FFE6E6';
				if(sState == 'CLOSED')
				{
					sHighlight = '#E6FFEC';
				}
				sHtml = sHtml + '<tr style="background-color: ' + sHighlight + '">';
				sHtml = sHtml + '<td style="width:100%">';
				sHtml = sHtml + '<div style="border: 1px solid black; line-height: 2.7em; width: 100%; padding 10px; "><div style="line-height: 2.7em; float:left; width: 50%; text-align:center">' + sSensor + ' </div> <div style="line-height: 2.7em; margin-left: 15%; text-align: center">' + sState + '</div>';
				sHtml = sHtml + '</td></tr>';
			}
			sHtml = sHtml + '</table>';
			$('#status-content').html(sHtml);
			console.log('updated status');
		},
		cache: false    
	});
}


function updateHistory() {
	$.ajax({
		url: 'service/getHistory.php',
		datatype: 'json',
		success: function(data) {
			var array = JSON.parse(data);
			console.log(array);

			sHtml = '        <ul data-role="listview" class="ui-listview">';
			for (var key in array) {
				var subArray = array[key];
				var nSubCount = subArray.length;
				var sSensor = key;
				var sAlertGroupKey = replaceAll(key.toLowerCase()," ","_");
console.log(sAlertGroupKey);
				sHtml = sHtml + '            <li data-theme="c" class="ui-btn ui-li ui-btn-up-c" style="line-height: 1.3em; font-weight: 700; white-space: nowrap; background-color: #f6f6f6; padding: .7em 1em;" id="alert-group-'+sAlertGroupKey+'" onclick="$(\'li#alert-detail-'+sAlertGroupKey+'\').toggle(\'fast\');">';
				sHtml = sHtml + '                <div class="ui-btn-inner ui-li">';
				sHtml = sHtml + '                    <div class="ui-btn-text" style="text-align:left">';
				sHtml = sHtml + '                        '+sSensor;
				sHtml = sHtml + '                            <span class="ui-li-count ui-btn-up-c ui-btn-corner-all" style="border-color: #ddd">';
				sHtml = sHtml + '                                '+nSubCount;
				sHtml = sHtml + '                            </span>';
				sHtml = sHtml + '                    </div>';
				sHtml = sHtml + '                </div>';
				sHtml = sHtml + '            </li>';
				sHtml = sHtml + '            <li id="alert-detail-'+sAlertGroupKey+'" >';
				sHtml = sHtml + '                <ul style="list-style-type: none">';
				for (var key2 in subArray) {
					var sState = subArray[key2]['state'];
					var sMsg = subArray[key2]['msg'];
					var sHighlight = subArray[key2]['highlight'];
					sHtml = sHtml + '                    <li class="individual-alert">';
					sHtml = sHtml + '                        <div style="line-height: 2em; width: 95%; background-color: ' + sHighlight + '">'+sMsg+'</div>';
					sHtml = sHtml + '                    </li>';
				}
				sHtml = sHtml + '                </ul>';
				sHtml = sHtml + '            </li>';
			}		
			sHtml = sHtml + '        </ul>';
			$('#history-content').html(sHtml);
			console.log('updated history');
			$("[id^=alert-detail-]").hide();

		},
		cache: false
	});
}




//Poll the heartbeat file and update the status image to show if the system is running or not
function poll()
{
	setTimeout( function() {
			$.ajax("/alarmHeartbeat.html").success( function(data) {
					console.log('HB:'+data);
					var milliseconds = Math.floor( Date.now() / 1000 );
					console.log('MS:'+milliseconds);
					var diff = milliseconds - data;
					console.log('DIFF:'+diff);
					if(diff < 20)
					{
						document.statusicon.src="images/on_sm.png";
						console.log("running");
					}else{
						document.statusicon.src="images/off_sm.png";
						console.log("stalled");
					}
					poll();
				});
			}, 2000);
}



function initiateTimer()
{
	var startSeconds = 180;
	document.counter.timer.value=startSeconds;
}

var startSeconds = 30;
var milisec = 0;
var seconds=startSeconds;
var countdownrunning = false
var idle = false;


function CountDown()
{
	if(idle == true)
	{
		if (milisec<=0)
		{
			milisec=9
				seconds-=1
		}
		if (seconds<=-1)
		{
			document.location='logout.php';
			milisec=0
				seconds+=1
				return;
		}
		else
			milisec-=1;
		document.counter.timer.value=seconds+"."+milisec;
		setTimeout("CountDown()",100);
	}
	else
	{
		return;
	}
}
function startCountDown()
{
	document.counter.timer.value=startSeconds;
	seconds = startSeconds;
	milisec = 0

		document.counter.timer.style.display = 'block';
	idle = true;
	CountDown();
	document.getElementById("alert").innerHTML = 'You are idle. you will be logged out after ' + startSeconds + ' seconds.';
	countdownrunning = false;
}

function resetTimer()
{
	document.counter.timer.style.display = 'none';
	idle = false;
	document.getElementById("alert").innerHTML = '';


	if(!countdownrunning)
		setTimeout('startCountDown()',150000);

	countdownrunning = true;

}

function setScale()
{
	var scale = 'scale(1)';
	document.body.style.webkitTransform =  scale;    // Chrome, Opera, Safari
	document.body.style.msTransform =   scale;       // IE 9
	document.body.style.transform = scale;     // General
}

function escapeRegExp(str) {
    return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}

function replaceAll(str, find, replace) {
    return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}
