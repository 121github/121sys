<?php
$full_url = explode('121system.com', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$domain = explode("/",$full_url[0]);
$domain = $domain[0];
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
		break;

	case 'accept.':
		define('ENVIRONMENT', 'acceptance');
		$session_name = '121sys_accept';
		break;

	case 'eldon.':
		define('ENVIRONMENT', 'production');
		$session_name = '121sys_eldon';
		break;

	case 'jonwall.':
		define('ENVIRONMENT', 'production');
		$session_name = '121sys_jonwall';
		break;

	case 'hsl.':
		define('ENVIRONMENT', 'production');
		$session_name = '121sys_hsl';
		break;

	case 'hcs.hslchairs.com':
		define('ENVIRONMENT', 'production');
		$session_name = '121sys_hsl';
		break;

	case 'demo.':
		define('ENVIRONMENT', 'production');
		$session_name = '121sys_demo';
		break;

	default:
		define('ENVIRONMENT', 'development');
		$session_name = '121sys_dev';
		break;
}

session_name($session_name);
session_start();
$_SESSION['session_name'] = session_name();
$_SESSION['environment'] = ENVIRONMENT;

if(isset($_GET['logout'])){
 session_destroy();
}

if(isset($_GET['login'])){
 $_SESSION['login']= $_GET['login'];
}

echo "<pre>";
print_r($_SESSION);
echo "</pre>";


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
