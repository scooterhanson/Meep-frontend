![Logo](images/meep_header.png)
# Meep

Meep is a configurable alarm program based around a Raspberry Pi setup with existing sensors.  If you have a house wired for an alarm system, this makes a very cost-effective solution without having to subscribe to a service.

This is the php front-end of the alarm system.  Check out [Meep-backend](https://github.com/scooterhanson/Meep-backend)  for a python infrastructure, running on a Raspberry Pi with GPIO pins connected to door, window, and motion sensors.

## Installation
For the most part, this should be a plug and play installation by dropping this into the web-root on a Raspberry Pi (/var/www/html/) that has [Meep-backend](https://github.com/scooterhanson/Meep-backend) running on it.  Much like the backend project, this connects to the MongoDB *alarm* database.  It polls and monitors the *alarmHeartbeat.html* file that is generated by the backend project to show whether or not the system is running.


### Dependencies

- [Meep-backend](https://github.com/scooterhanson/Meep-backend) is required for this to really be useful.  Technically, the *control* tab can still be used when configured against the Pi with relay switched wired to specified GPIO pins.
- Apache2 (or lighttpd, or whatever else)
- [PiPHP](https://github.com/PiPHP/GPIO).
- [PHP Mongo Driver](https://docs.mongodb.com/ecosystem/drivers/php/)


## Configuration
*alarmConfig.php* hosts all of the configurations needed for Meep-frontend.
```
define('PIN','[INSERT PIN HERE]');
define('CONF_PATH','/home/pi/bin/meep/system.json');
define('DB_URI','mongodb://localhost:27017/');
define('DB_NAME','alarm');
define('HISTORY_LIMIT','50');
define('RELAY_PINS', '{"pin1":{"number":"13","name":"Garage Bay 1","default_state":"off","target_state":"on","toggle":"true","toggle_time_sec":"1"},"pin2":{"number":"26","name":"Garage Bay 2","default_state":"off","target_state":"on","toggle":"true","toggle_time_sec":"1"}}');
define('PROVISION_LIFETIME', '730'); //2 years
```

Because the frontend controls the alerting and alarm on/off preferences for the backend system, this config file requires the location of the backend *system.json* file.

## DB Requirements
### MongoDB
The default database connection string is assuming mongo is running on localhost:27017.

This connection string is configurable in *alarmConfig.php*.

The database name is 'alarm'.

## Logging In
The PIN configured in *alarmConfig.php* is just one piece of the authentication.  Because this alarm web console might be exposed outside of your home network, there is a second factor of authentication using *fingerprint.js* to create a device fingerprint.  This is stored in the database and verified along with the PIN when trying to log in.  *alarmConfig.php* also has a variable to define the time that a device fingerprint is valid.  After that period is over, the device must re-provision itself.

![Login](images/Meep_login.PNG)

For additional layers of security, I recommend ensuring a signed SSL certificate, and configuring apache to use TLS v1.2 (or higher), *especially* if this service is open on the web.  If it is contained within a LAN, a solid VPN (such as [OpenVPN](https://openvpn.net/)) is a secure option for remote access.

### Registering Device Fingerprint
In order to register a device's fingerprint for authentication, navigate to */registerFingerprint.php*.

## Navigation Tabs
### Config
#### *General*
Toggle the alarm on/off.
#### *Beep*
Turn on audio notifications for door and/or motion sensor events.
#### *Notify*
Turn on sms notifications for door and/or motion sensor events.
#### *Siren*
Turn on siren activation for door and/or motion sensor events. (Still in development)

![Config](images/Meep_config.PNG)

### Status
Shows all sensors (that have been triggered in the database) with current status (red for open/triggered, green for closed/inactive).

![Status](images/Meep_status.PNG)

### History
Shows the last 50 (configurable in *alarmConfig.php*) events, grouped by sensor name.

![History](images/Meep_history.PNG)

### Control
Allows triggering of configured (in *alarmConfig.php*) relays.  An example of this is garage door control.

![Control](images/Meep_control.PNG)

