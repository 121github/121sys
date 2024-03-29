<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/* End of file constants.php */
/* Location: ./application/config/constants.php */


$fcpath = str_replace('\\','/',substr(FCPATH,0, strlen(FCPATH)-1));
define('DOCROOT', substr($fcpath,0,strripos ($fcpath,'/')));
define('BACKUP_PATH', DOCROOT.'/backup/121sys/');


//SMS STATUS
define('SMS_STATUS_PENDING', 1);
define('SMS_STATUS_SENT', 2);
define('SMS_STATUS_UNKNOWN', 3);
define('SMS_STATUS_UNDELIVERED', 4);
define('SMS_STATUS_ERROR', 5);


//ICAL METHOD
define('ICAL_REQUEST', 'REQUEST');
define('ICAL_CANCEL', 'CANCEL');

//ICAL STATUS
define('ICAL_STATUS_SENT', 1);
define('ICAL_STATUS_PENDING', 2);
define('ICAL_STATUS_ABORTED', 3);

//PLANNER TYPE
define('PLANNER_TYPE_WAYPOINT', 2);

//PLANNER STATUS
define('PLANNER_STATUS_LIVE', 1);
define('PLANNER_STATUS_CANCEL', 0);

//APPOINTMENT STATUS
define('APPOINTMENT_STATUS_LIVE', 1);
define('APPOINTMENT_STATUS_CANCEL', 0);

//APPOINTMENT TYPE
define('APPOINTMENT_TYPE_FACE_TO_FACE', 1);
define('APPOINTMENT_TYPE_TELEPHONE', 2);
define('APPOINTMENT_TYPE_SURVEY', 3);
define('APPOINTMENT_TYPE_INSTALLATION', 4);

//define('EXPORT_PATH', '/var/www/html/tmp/');
define('EXPORT_PATH', '/mnt/managementnew/Figures/Distribution Models/');


//Access TOKEN
define("ACCESS_TOKEN", "Djh34jsJRlWWAsd5432Ss3dsr34");