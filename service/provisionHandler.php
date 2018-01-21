<?php
require_once('../alarmConfig.php');

////
// Service to read the supplied browser fingerprint and allow access to the login page. 
////

$sFPrint = filter_input(INPUT_GET, 'fp', FILTER_SANITIZE_NUMBER_FLOAT);
$sUAString = filter_input(INPUT_GET, 'ua', FILTER_SANITIZE_STRING);
$sIPAddress = filter_input(INPUT_GET, 'ip', FILTER_SANITIZE_STRING);
$sDate = date("Y-m-d H:i:s");

$aInsertPrint = array(
		'print_value'  => $sFPrint,
		'user_agent' => $sUAString,
		'ip_address' => $sIPAddress,
		'date_registered' => $sDate 
		);

$aInsertOptions = array(
		'safe'    => true,
		'fsync'   => true,
		'timeout' => 10000
		);

$sUri = DB_URI;
$oClient= new MongoClient($sUri);
$oDb= $oClient->selectDB(DB_NAME);
$oItems = $oDb->items;

try {
	$oResults = $oItems->insert($aInsertPrint, $aInsertOptions);
	echo($aInsertPrint["_id"]);
}
catch (MongoCursorException $mce) {
	// Triggered when the insert fails
	echo("0");
}
catch (MongoCursorTimeoutException $mcte) {
	// Triggered when insert does not complete within value given by timeout
	echo("0");
} 
$oClient->close();

?>
