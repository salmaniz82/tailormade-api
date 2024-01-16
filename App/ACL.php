<?php

namespace App;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

use \Framework\Auth;

class ACL
{


	public static function refinePath($path)
	{
		$path = str_replace('\\', '/', $path);
		$path = preg_replace('/\/+/', '/', $path);

		return $path;
	}







	public static function isPermitted($resourceName, $scope = null)
	{


		if (Auth::loginStatus()) {


			$authRoleId = Auth::User()['role_id'];
			$resId = self::getResourceId($resourceName);
			$permissionModule = self::load('module', 'permissions');
			$postData['role_id'] = $authRoleId;
			$postData['resource_id'] = $resId;
			if ($permission = $permissionModule->checkDuplicate($postData)) {

				return true;
			} else {

				return false;
			}
		} else {

			return false;
		}
	}


	public static function getResourceId($resourceName)
	{
		$resModule = new \App\Modules\resourceModule();

		return $resModule->pluckIdByName($resourceName);
	}


	public static function authUserId()
	{
		return Auth::User()['id'];
	}

	public static function isAdmin()
	{
		if (Auth::loginStatus()) {
			return (Auth::User()['role_id'] == 1) ? true : false;
		} else {
			return false;
		}
	}

	public static function isLoggedIn()
	{
		return (Auth::loginStatus()) ? true : false;
	}
}
