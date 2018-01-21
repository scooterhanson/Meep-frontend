<?php
define('PIN','[INSERT PIN HERE]');
define('CONF_PATH','/home/pi/bin/meep/system.json');
define('DB_URI','mongodb://localhost:27017/');
define('DB_NAME','alarm');
define('HISTORY_LIMIT','50');
define('RELAY_PINS', '{"pin1":{"number":"13","name":"Garage Bay 1","default_state":"off","target_state":"on","toggle":"true","toggle_time_sec":"1"},"pin2":{"number":"26","name":"Garage Bay 2","default_state":"off","target_state":"on","toggle":"true","toggle_time_sec":"1"}}');
define('PROVISION_LIFETIME', '730'); //2 years
?>
