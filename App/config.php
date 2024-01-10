<?php
/* rename this file as config.php and remove -sample from it */
if (!defined('ABSPATH')) die('Direct Access File is not allowed');

function siteURL()
{
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$domainName = $_SERVER['HTTP_HOST'] . '/';
	return $protocol . $domainName;
}


/*
Note : 
- example.com OR subdomain.example.com
- define( 'SITE_URL', siteURL() . '' );
subdomain.example.com/project01
define( 'SITE_URL', siteURL() . 'project01' );
*/

if (!defined('SITE_URL'))
	define('SITE_URL', siteURL() . '');



define('TIMEZONE', 'Europe/London');


// Set Environment variable dev OR live

if (SITE_URL == 'http://tailormade.local/') {

	define('SERVER', 'localhost');
	define('USER', 'root');
	define('DATABASE', 'tailormade');
	define('PASSWORD', '');
	define('ENV', 'dev');
} else {
	define('SERVER', 'localhost');
	define('DATABASE', 'enginec790_tailormade');
	define('USER', 'enginec790_tailormade');
	define('PASSWORD', 'Y_9:)V%Eb#a2^qumkv%r');
	define('ENV', 'live');
}
