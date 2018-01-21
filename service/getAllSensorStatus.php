<?php
require_once('../alarmConfig.php');
require_once('../session.php');

$aConfigs = parse_ini_file("/etc/alarm.cfg", true);
$aSystemConfigs = $aConfigs['Alarm'];

////
// Service to retrieve the current (most recent in the db) status of all sensors.
////

$aStatusContent = getStatusContent();
echo(json_encode($aStatusContent));


function getStatusContent()
{
        try {
                $aRet = [];
                $sUri = DB_URI;
                $oClient = new MongoClient($sUri);
                $oDb = $oClient->selectDB(DB_NAME);
                $oItems = $oDb->items;
                $oCollection = new MongoCollection($oDb, 'items');
                $aSensors = $oItems->distinct('sensor');
		sort($aSensors);
                foreach($aSensors as $sSensor)
                {
                        $aSensorQuery = array("sensor" => "$sSensor");
                        $oCursor = $oCollection->find($aSensorQuery)->sort(array('_id' => -1))->limit(1);
                        foreach ($oCursor as $aDoc) {
                                $sState = $aDoc['state'];
                                $sDate = $aDoc['date'];
                                $sHighlight = "#E6FFEC";
                                if(strtoupper($sState) == 'OPEN')
                                {
                                        $sHighlight = "#FFE6E6";
                                }
                                $aRet[$sSensor]['state'] = $sState;
                                $aRet[$sSensor]['date'] = $sDate;
                        }
                }
                $oClient->close();
        } catch (MongoConnectionException $e) {
                die('Error connecting to MongoDB server');
        } catch (MongoException $e) {
                die('Error: ' . $e->getMessage());
        }
        return $aRet;
}
?>
