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


// Set Environment variable dev OR live

if (SITE_URL == 'https://tailormade.local/') {

    define('SERVER', 'localhost');
    define('USER', 'root');
    define('DATABASE', 'dbname_local');
    define('PASSWORD', '');
    define('ENV', 'dev');
} else {
    define('SERVER', 'localhost');
    define('DATABASE', 'live_dbname');
    define('USER', 'live_db_user');
    define('PASSWORD', 'live_db_pas');
    define('ENV', 'live');
}


define('TIMEZONE', 'Europe/London');
date_default_timezone_set(TIMEZONE);
