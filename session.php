<?php

$full_url = explode('121system.com', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$domain = explode("/",$full_url[0]);
$domain = $domain[0];

$ukfast_url = explode('one2one.leadcontrol.co.uk', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$domain_ukfast = explode("/", $ukfast_url[0]);
$domain_ukfast = $domain_ukfast[0];

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
		$theme = "smartprospector";
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
