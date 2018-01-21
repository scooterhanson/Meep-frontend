<?php
require('alarmConfig.php');

////
// Record the device fingerprint as a provisioned client.
// I recommend adding something else to this to make it more secure, otherwise 
// people can just look for the url and self-provision.
////

$sIPAddress = $_SERVER['REMOTE_ADDR'];
$sUAString = urlencode($_SERVER['HTTP_USER_AGENT']);

$sHTML = <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<script src="js/jquery-3.1.1.min.js"></script>
		<script type="text/javascript" src="js/fingerprint/fingerprint.js"></script>
		<script type="text/javascript">
		var fp = new Fingerprint().get();
		console.log('fingerprint:'+fp);
		$.ajax({
			url: 'service/provisionHandler.php?fp='+fp+'&ip=$sIPAddress&ua=$sUAString',
			datatype: 'json',
			success: function(data) {
				if(data != "0")
				{
					$('#response').html("SUCCESSFULLY PROVISIONED DEVICE");
				}else{
					$('#response').html("FAILED TO PROVISION DEVICE");	
				}
			}
		});
		</script>
	</head>
	<body>
		<span id="response"></span>
	<body>
</html>
EOT;
echo($sHTML);
?>
