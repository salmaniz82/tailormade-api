<?php ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined('ABSPATH'))
	define('ABSPATH', dirname(__FILE__) . '/');


$bootstrapFilePath = ABSPATH . 'bootstrap.php';

if (file_exists($bootstrapFilePath)) {

	require_once($bootstrapFilePath);
} else {
	echo $bootstrapFilePath . 'DOES NOT EXISTS';
}
