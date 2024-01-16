<?php

namespace App\Controllers;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

abstract class BaseController
{



	public function refinePath($path)
	{
		$path = str_replace('\\', '/', $path);
		$path = preg_replace('/\/+/', '/', $path);

		return $path;
	}


	public function getID()
	{

		if (isset(\Framework\Route::$params['id'])) {
			return \Framework\Route::$params['id'];
		} else {
			return null;
		}
	}

	public function appMethod()
	{
		return 'this is from the appcontroller';
	}


	public function canManageCourse()
	{
		if (\Framework\Auth::loginStatus() && (\Framework\Auth::User()['role_id'] == 1)) {
			return true;
		} else {
			return false;
		}
	}

	public function phpCurrentTimeStamp()
	{
		return date('Y-m-d H:i:s');
	}


	public function buildSlug($string)
	{

		return $string;
	}

	public function redirect($url)
	{

		return header('location:' . $url);
	}

	public function redirectBack()
	{
		$redirectUrl = $_SERVER['REDIRECT_URL'];
		return header('location:' . $redirectUrl);
	}


	public function isSameOrigin()
	{

		if ($_SERVER['HTTP_ORIGIN'] == substr(SITE_URL, 0, -1)) {
			return true;
		}
		return false;
	}

	public function crossOrignResponse()
	{
		$statusCode = 406;
		$data['message'] = "Request Origin not acceptable";
		return \Framework\View::responseJson($data, $statusCode);
	}

	public function invalidCSRFResponse()
	{
		$statusCode = 406;
		$data['message'] = "Invalid Request";
		return \Framework\View::responseJson($data, $statusCode);
	}

	public function hasCSRF()
	{
		if (isset($_SERVER['HTTP_CSRF']))
			return $_SERVER['HTTP_CSRF'];

		return false;
	}
}
