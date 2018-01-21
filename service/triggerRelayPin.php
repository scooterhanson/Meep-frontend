<?php
require_once('../vendor/autoload.php');
require_once('../session.php');

use PiPHP\GPIO\GPIO;
use PiPHP\GPIO\Pin\PinInterface;

////
// Service to trigger relays attached to external devices (for example, garage bay doors).
// Note, this works with "active low" relays.
////

$nTargetPin = filter_input(INPUT_GET, 'pin', FILTER_SANITIZE_NUMBER_INT);
$sDefaultState = filter_input(INPUT_GET, 'default_state', FILTER_SANITIZE_STRING);
$sTargetState = filter_input(INPUT_GET, 'target_state', FILTER_SANITIZE_STRING);
$sToggle = filter_input(INPUT_GET, 'toggle', FILTER_SANITIZE_STRING);
$nToggleTimeSec = filter_input(INPUT_GET, 'toggle_time_sec', FILTER_SANITIZE_NUMBER_INT);

//TODO: Right now, this just toggles on then off.  Use $sToggle as a boolean flag to allow just turning something on

try {
	$oGpio = new GPIO();
	$oPin = $oGpio->getOutputPin($nTargetPin);
	$oPin->setValue(resolveStateValue($sTargetState));
	sleep($nToggleTimeSec);
	$oPin->setValue(resolveStateValue($sDefaultState));
	echo("1");
}
	catch(Exeption $e)
{
	echo("0");
}

function resolveStateValue($sState)
{
	$retVal = PinInterface::VALUE_LOW;
	if(strtoupper($sState) == "OFF")
	{
		$retVal = PinInterface::VALUE_HIGH;
	}
	return $retVal;
}
?>
