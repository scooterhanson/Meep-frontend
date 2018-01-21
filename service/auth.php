<?php
require_once('../alarmConfig.php');

////
// Service to authenticate the provided pin number (defined in alarmConfig.php),
// and the device fingerprint.  We will check to make sure the device fingerprint is registered in the database
// and within the configured time range.
////

$nPin = filter_input(INPUT_POST, 'pin', FILTER_SANITIZE_NUMBER_INT);
$sFingerprint = filter_input(INPUT_POST, 'fingerprint', FILTER_SANITIZE_STRING);
$nProvisionLifetimeDays = PROVISION_LIFETIME;

$bPINValid = validatePIN($nPin);
$bFingerprintValid = validateFingerprint($sFingerprint, $nProvisionLifetimeDays);

////
// Only if the PIN and fingerprint are valid, return 1 and log the user in
////
if($bPINValid == true && $bFingerprintValid == true)
{
	start_session();
	echo(1);
}else{
	echo(0);
}



////
// Pin configured in alarmConfig.php
////
function validatePIN($nPin)
{
	$bReturn = false;
	if($nPin == PIN)
	{
		$bReturn = true;
	}else{
		error_log("[LOGIN FAILED] - Incorrect PIN",0);
	}
	return $bReturn;
}


////
// To provision a device and have its fingerprint stored in the DB, first browse to /provisionMeep.php
////
function validateFingerprint($sFingerprint, $nProvisionLifetimeDays)
{
        $bReturn = false;
        $aSearchCriteria = array();
        $dGTDate =  date("Y-m-d H:i:s", strtotime("-$nProvisionLifetimeDays day"));
        $aSearchCriteria = array('date_registered' => array('$gte' => $dGTDate), 'print_value' => $sFingerprint);
        $nRowCount = queryRowCount($aSearchCriteria);
        if($nRowCount > 0)
        {
                $bReturn = true;
        }else{
		error_log("[LOGIN FAILED] - No fingerprint match for $sFingerprint",0);
	}
        return $bReturn;
}

////
// If at least one document in the DB matches the device fingerprint within the 
// defined time limit, success.
////
function queryRowCount($aSearchCriteria)
{
        $nRowCount = 0;
        $sUri = DB_URI;
        $nLimit = HISTORY_LIMIT;
        $oClient= new MongoClient($sUri);
        $oDb= $oClient->selectDB(DB_NAME);
        $oItems = $oDb->items;
        $nRowCount = $oItems->find($aSearchCriteria)->count();
        $oClient->close();
        return $nRowCount;
}


function start_session()
{
	if(!isset($_SESSION)) {
		session_start();
	}
	$_SESSION['time'] = time();
	$_SESSION['user'] = 'alarm';
	$_SESSION['logged_in'] = true;
}
?>
