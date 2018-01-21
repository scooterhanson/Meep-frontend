<?php
require_once('../alarmConfig.php');
require_once('../session.php');

$aConfigs = parse_ini_file("/etc/alarm.cfg", true);
$aSystemConfigs = $aConfigs['Alarm'];

////
// Service to retrieve the history of all sensors and group them for display. 
////

$aHistoryContent = getHistoryContent();
echo(json_encode($aHistoryContent));

function getHistoryContent()
{
	try {
		$aSensorArray = [];
        	$sUri = DB_URI;
	        $nLimit = HISTORY_LIMIT;
        	$oClient= new MongoClient($sUri);
		$oDb= $oClient->selectDB(DB_NAME);
        	$oItems = $oDb->items;
	        $aRows = $oItems->find(array('state' => array('$in' => array('OPEN','CLOSED'))))->sort(array('_id' => -1))->limit($nLimit);
        	foreach($aRows as $oRow){
                	$aRow = object_to_array($oRow);
	                $sSensor = $aRow['sensor'];
        	        $sState = $aRow['state'];
			$sHighlight = "#E6FFEC";
			if(strtoupper($sState) == 'OPEN')
			{
				$sHighlight = "#FFE6E6";
			}
                	$sMsg = $aRow['msg'];
	                $sDate = $aRow['date'];
        	        $aSensorArray[$sSensor][] = array("state"=>$sState, "date"=>$sDate, "msg"=>$sMsg, "highlight"=>$sHighlight);
	        }
        	$oClient->close();
	} catch (MongoConnectionException $e) {
        	die('Error connecting to MongoDB server');
	} catch (MongoException $e) {
        	die('Error: ' . $e->getMessage());
	}
        return $aSensorArray;
}

function object_to_array($oObject) {
	if (is_object($oObject)) {
		return array_map(__FUNCTION__, get_object_vars($oObject));
	} else if (is_array($oObject)) {
		return array_map(__FUNCTION__, $oObject);
	} else {
		return $oObject;
	}
}

?>
