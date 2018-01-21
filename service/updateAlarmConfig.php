<?php
require_once('../alarmConfig.php');
require_once('../session.php');

////
// Service to update a field's value in system.json
////

$sFld = filter_input(INPUT_GET, 'fld', FILTER_SANITIZE_STRING);
$sVal = filter_input(INPUT_GET, 'val', FILTER_SANITIZE_NUMBER_INT);

try {
	if((int)$sVal == 1)
	{
		$sVal = true;
	}else{
		$sVal = false;
	}
	$aFieldParams = split("_",$sFld);
	$sSection = $aFieldParams[0];
	$sField= $aFieldParams[1];
	$sSettingsFilePath = CONF_PATH;
	$sJsonString = file_get_contents($sSettingsFilePath);
	$sData = json_decode($sJsonString, true);
	$sData[$sSection][$sField] = $sVal;
	$sNewJsonString = json_encode($sData,JSON_PRETTY_PRINT);
	file_put_contents($sSettingsFilePath, $sNewJsonString);
}catch(Exception $e)
{

}

?>
