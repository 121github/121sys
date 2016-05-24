<?php
//git test
/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
//define('ENVIRONMENT', 'development');
$redirect = str_replace("121sys/121sys","121sys",$_SERVER['REQUEST_URI']);
if(strpos($_SERVER['REQUEST_URI'],"121sys/121sys")!==false){
	header("location: ".$redirect); 
	exit;
}

$no_https = array("accept.", "demo.", "test.");

$full_url = explode('121system.com', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$domain = explode("/",$full_url[0]);
$domain = $domain[0];

$ukfast_url = explode('one2one.leadcontrol.co.uk', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$domain_ukfast = explode("/", $ukfast_url[0]);
$domain_ukfast = $domain_ukfast[0];

$theme = "default";
if(!isset($_SESSION['timeout'])){
$timeout = 1800; //30min timeout
}
//if the site has no certificate but they are trying to use https then we redirect to the non-https url
if (in_array($domain, $no_https)) {
    if (isset($_SERVER['HTTPS']) || $_SERVER['SERVER_PORT'] == 443) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]);
        exit();
    }
}

switch ($domain) {
    case 'www.':
        define('ENVIRONMENT', 'production');
        $session_name = '121sys';
        break;

    case 'test.':
        define('ENVIRONMENT', 'testing');
        $session_name = '121sys_test';
        break;

    case '10.10.1.15':
        define('ENVIRONMENT', 'testing');
        $session_name = '121sys_test';
        break;

    case 'accept.hsl.':
        define('ENVIRONMENT', 'acceptance');
        $session_name = '121sys_accept_hsl';
        $theme = "hsl";
		$timeout = 6000; //100 minutes
        break;

    case 'accept.':
        define('ENVIRONMENT', 'acceptance');
        $session_name = '121sys_accept';
        break;

    case 'accept.lhs.':
        define('ENVIRONMENT', 'acceptance');
		$theme = "lhs";
        $session_name = '121sys_accept_lhs';
        break;

    case 'eldon.':
        define('ENVIRONMENT', 'production');
        $session_name = '121sys_eldon';
		$theme = "eldon";
		$timeout = 6000; //100 minutes
        break;

    case 'jonwall.':
        define('ENVIRONMENT', 'production');
        $session_name = '121sys_jonwall';
        break;

    case 'hsl.':
        define('ENVIRONMENT', 'production');
        $session_name = '121sys_hsl';
		$theme = "hsl";
		$timeout = 6000; //100 minutes
        break;

    case 'hcs.hslchairs.com':
        define('ENVIRONMENT', 'production');
        $session_name = '121sys_hsl';
		$theme = "hsl";
        break;

    case 'lhs.':
        define('ENVIRONMENT', 'production');
        $session_name = '121sys_lhs';
        $theme = "lhs";
        //$timeout = 6000; //100 minutes
        break;

    case 'demo.':
        define('ENVIRONMENT', 'demo');
        $session_name = '121sys_demo';
		$theme = "smartprospector";
        break;

    case 'pro.':
        define('ENVIRONMENT', 'production');
        $session_name = '121sys_prosales';
		$theme = "leadsontap";
        break;
		
	case 'localhost:8082':
        define('ENVIRONMENT', 'development');
        $session_name = '121sys_local';
		$theme="leadsontap";
    break;

    default:
        switch ($domain_ukfast) {
            case 'prosales.':
                define('ENVIRONMENT', 'production');
                $session_name = '121sys_prosales_ukfast';
                break;
            default:
                define('ENVIRONMENT', 'development');
                $session_name = '121sys_dev';
                break;
        }
}

//sets the expirey time of the cookie
//ini_set('session.gc_maxlifetime', 2 * 60 * 60);
session_name($session_name);
session_start();
$_SESSION['session_name'] = session_name();
$_SESSION['environment'] = ENVIRONMENT;
if(!isset($_SESSION['theme_color'])||empty($_SESSION['theme_color'])){
$_SESSION['theme_images'] = $theme;
$_SESSION['theme_color'] = $theme;
}
if(!isset($_SESSION['timeout'])){
$_SESSION['timeout'] = $timeout;
}
//clear the previous flashalert
if(isset($_SESSION['flashalert'])){
unset($_SESSION['flashalert']);	
}
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */

if (defined('ENVIRONMENT')) {
    switch (ENVIRONMENT) {
        case 'development':
            error_reporting(0);
            break;
        case 'testing':
            error_reporting(0);
            break;
        case 'demo':
            error_reporting(0);
            break;
        case 'acceptance':
            error_reporting(0);
            break;
        case 'production':
            error_reporting(0);
            break;

        default:
            exit('The application environment is not set correctly.');
    }
	

	
}

/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same  directory
 * as this file.
 *
 */
$system_path = 'system';

/*
 *---------------------------------------------------------------
 * APPLICATION FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder then the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server.  If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 *
 */
$application_folder = 'application';

/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here.  For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT:  If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller.  Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 *
 */
// The directory name, relative to the "controllers" folder.  Leave blank
// if your controller is not in a sub-folder within the "controllers" folder
// $routing['directory'] = '';

// The controller class file name.  Example:  Mycontroller
// $routing['controller'] = '';

// The controller function you wish to be called.
// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 *
 */
// $assign_to_config['name_of_config_item'] = 'value of config item';


// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

// Set the current directory correctly for CLI requests
if (defined('STDIN')) {
    chdir(dirname(__FILE__));
}

if (realpath($system_path) !== FALSE) {
    $system_path = realpath($system_path) . '/';
}

// ensure there's a trailing slash
$system_path = rtrim($system_path, '/') . '/';

// Is the system path correct?
if (!is_dir($system_path)) {
    exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: " . pathinfo(__FILE__, PATHINFO_BASENAME));
}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
// The name of THIS file
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// The PHP file extension
// this global constant is deprecated.
define('EXT', '.php');

// Path to the system folder
define('BASEPATH', str_replace("\\", "/", $system_path));

// Path to the front controller (this file)
define('FCPATH', str_replace(SELF, '', __FILE__));

// Name of the "system folder"
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));


// The path to the "application" folder
if (is_dir($application_folder)) {
    define('APPPATH', $application_folder . '/');
} else {
    if (!is_dir(BASEPATH . $application_folder . '/')) {
        exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: " . SELF);
    }

    define('APPPATH', BASEPATH . $application_folder . '/');
}

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
require_once BASEPATH . 'core/CodeIgniter.php';

/* End of file index.php */
/* Location: ./index.php */


