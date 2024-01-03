<?php 

class ACL {

	
	public static function refinePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('/\/+/', '/', $path);

        return $path;
    }

	public static function load($loadType, $Loadentity)
	{

		if($loadType == 'module')
		{

			$path = ABSPATH.'app/modules/'.$Loadentity.'Module.php';
			$path = self::refinePath($path);
			require_once $path;
			$ModuleClass =  $Loadentity.'Module';
			return new $ModuleClass();
		}

		elseif($loadType == 'external')
		{
			
			$path = ABSPATH.'app/external/'.$Loadentity.'.php';
			require_once($path);
			
		}

	}


	public static function Message()
	{
		echo "A message form ACL";
	}


	public static function isPermitted($resourceName, $scope = null)
	{
			/*

			$scope null will check for both has to be true;
			private will check only private
			public will check only public
			
			*/

			if(Auth::loginStatus())
			{
			
			
				$authRoleId = Auth::User()['role_id'];
				$resId = self::getResourceId($resourceName);
				$permissionModule = self::load('module', 'permissions');
	            $postData['role_id'] = $authRoleId;
	            $postData['resource_id'] = $resId;
	            if($permission = $permissionModule->checkDuplicate($postData))
	            {
	            	
					return true;

	            }	            
	            else {

	            	return false;

	            }

        	}

            else {

            	return false;
			}


	}


	public static function getResourceId($resourceName)
	{
		$resModule = self::load('module', 'resource');

		return $resModule->pluckIdByName($resourceName);


	}


	public static function authUserId()
	{
		return Auth::User()['id'];
	}

	public static function isAdmin()
	{
		if(Auth::loginStatus())
		{
			return (Auth::User()['role_id'] == 1) ? true : false;	
		}
		else {
			return false;
		}
		
	}

	public static function isLoggedIn()
	{
		return (Auth::loginStatus()) ? true : false;
	}


}