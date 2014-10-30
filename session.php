<?php
$domain = explode('121sys', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])[0];
switch ($domain) {
	case '121webhost/':
		define('ENVIRONMENT', 'production');
		$session_name = '121sys';
		break;

	case '121webhost/test_env/':
		define('ENVIRONMENT', 'testing');
		$session_name = '121sys_test';
		break;

	case '121webhost/accept_env/':
		define('ENVIRONMENT', 'acceptance');
		$session_name = '121sys_accept';
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
