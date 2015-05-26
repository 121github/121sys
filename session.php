<?php
$domain = explode('121system.com', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])[0];
switch ($domain) {
	case 'www.':
		define('ENVIRONMENT', 'production');
		$session_name = '121sys';
		break;

	case 'test.':
		define('ENVIRONMENT', 'testing');
		$session_name = '121sys_test';
		break;

	case 'accept.':
		define('ENVIRONMENT', 'acceptance');
		$session_name = '121sys_accept';
		break;

	case 'eldon.':
		define('ENVIRONMENT', 'production');
		$session_name = '121sys_eldon';
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
