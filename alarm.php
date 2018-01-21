<?php
require_once('alarmConfig.php');
require_once('session.php');

////
// Web control for Meep
////
try {
	//Gather the alarm settings
        $settingsFilePath = CONF_PATH;
        $sJsonString = file_get_contents($settingsFilePath);
        $oData = json_decode($sJsonString, true);

	//Set up the alarm On/Off switch	
 	$sAlarmOnSelected = "";
	$sAlarmOffSelected = "selected";
	if((boolean)($oData['system']['running']) == true) 
	{ 
		$sAlarmOnSelected = "selected"; 
		$sAlarmOffSelected = ""; 
	}

	//Set up the beep/notify/siren switches
	$sConfigSectionContent = "";
	$aAlertTypes = ["beep","notify","siren"];
	foreach($aAlertTypes as $sAlertType)
	{
		$sSection = ucfirst($sAlertType);
		$sConfigSectionContent .= <<< EOT
                    <h2>$sSection</h2>
EOT;
		$aAlertTypeConfigs = $oData[$sAlertType];
		foreach($aAlertTypeConfigs as $sConfigName => $sConfigValue)
		{
			$sConfigName = ucfirst($sConfigName);
			$sConfigId = strtolower(str_replace(" ","_",$sAlertType . "_" . $sConfigName));
		        $sOnSelected = "";
		        $sOffSelected = "";
		        if((boolean)$sConfigValue == true)
		        {
				$sOnSelected = "selected";
				$sOffSelected = "";
			} else{
				$sOffSelected = "selected";
				$sOnSelected = "";
			}
			$sConfigSectionContent .= <<<EOT
                    <div class="flip-section" style="width: 95%">
                        <div style="display:inline; width: 60%; float:left; padding-top:10px;">
                            <h3 style=" display:inline; " >$sConfigName </h3>
                        </div>
                        <div style="display:inline">
                            <select class="flip" style="po" name="$sConfigId" id="$sConfigId" data-role="flipswitch" data-theme="b">
                                <option value="" $sOffSelected>Off</option>
                                <option value="1"  $sOnSelected>On</option>
                            </select>
                        </div>
                    </div>
EOT;

		}
		$sConfigSectionContent .= "<div><p>&nbsp;</p></div>";

	}
	
	//alarmConfig.php has json configs for the relay pins that take input from the front end
	// (e.g. garage doors, lights, etc.)
	$sControlHtml = "";
	$sRelayJson = RELAY_PINS;
	$oRelayConfigs = json_decode($sRelayJson, true);
	foreach($oRelayConfigs as $sPin => $aPinConfigs)
	{
		$sPinNum = $aPinConfigs['number'];
		$sPinName = $aPinConfigs['name'];
		$sPinDefaultState = $aPinConfigs['default_state'];
		$sPinTargetState = $aPinConfigs['target_state'];
		$sPinToggle = $aPinConfigs['toggle'];
		$sPinToggleTimeSec = $aPinConfigs['toggle_time_sec'];
		$sInputId = strtolower(str_replace(' ','_',$sPinName));
		$sControlHtml .= "<input id=\"$sInputId\" type=\"button\" value=\"$sPinName\" onClick=\"javascript: triggerRelay($sPinNum, '$sPinDefaultState', '$sPinTargetState', '$sPinToggle', '$sPinToggleTimeSec');return false;\">";
	}
}catch(Exception $e)
{

}


//Provide a random version number to the local js/css files to prevent cache
$sNoCacheRandStr = rand(1,99999);

$sHTML = <<< EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="css/jquery.mobile-1.4.5.min.css">
		<link rel="stylesheet" type="text/css" href="css/main-alarm.css?rndstr=$sNoCacheRandStr">
		<script src="js/jquery-1.11.3.min.js"></script>
		<script src="js/jquery.mobile-1.4.5.min.js"></script>
		<script src="js/main-alarm.js?rndstr=$sNoCacheRandStr"></script>
	</head>
	<body onload="setScale();poll();initiateTimer();setTimeout('startCountDown()',150000);" onmousemove="resetTimer();">
		<form name="counter"><input type="text" size="5" name="timer" disabled="disabled" /></form> 
		<div id="alert"></div>
		<div data-role="page">
			<div data-role="main" class="ui-content">
				<div class="wrap">
					<img src="images/meep_header.png" style="width:280px; margin-bottom:-20px; margin-top:-10px;">
					<img name="statusicon" src="images/on_sm.png">
				</div>
				<div data-role="tabs" id="tabs">
					<div data-role="navbar">
						<ul>
							<li><a href="#config" data-ajax="false">Config</a></li>
							<li><a href="#status" data-ajax="false">Status</a></li>
							<li><a href="#history" data-ajax="false">History</a></li>
							<li><a href="#control" data-ajax="false">Control</a></li>
						</ul>
					</div>
					<div id="config" class="ui-body-d ui-content">
						<form method="post" action="service/updateAlarmConfig.php">
							<h2>General</h2>
							<div class="flip-section" style="width: 95%">
								<div style="display:inline; width: 60%; float:left; padding-top:10px;">
									<h3 style=" display:inline; " >Alarm </h3>
								</div>
								<div style="display:inline">
									<select class="flip" style="po" name="alarm_running" id="alarm_running" data-role="flipswitch" data-theme="b">
										<option value="" $sAlarmOffSelected>Off</option>
										<option value="1" $sAlarmOnSelected>On</option>
									</select>
								</div>
							</div>
							<div><p>&nbsp;</p></div>
							$sConfigSectionContent
						</form>
					</div>
					<div id="status" class="ui-body-d ui-content">
						<div id="status-content" data-role="content">
							<!-- STATUS CONTENT DYNAMICALLY LOADED -->
						</div>
					</div>
					<div id="history" class="ui-body-d ui-content">
						<div id="history-content" data-role="content">
							<!-- HISTORY CONTENT DYNAMICALLY LOADED -->
						</div>
					</div>
					<div id="control" class="ui-body-d ui-content">
						<div data-role="content">
							<form>
								$sControlHtml
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>


EOT;
echo($sHTML);

?>
