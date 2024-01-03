<?php 
class jwtauthCtrl extends appCtrl {


	public function check()
	{
		

		if(JwtAuth::hasToken())
		  {
		      
	  		if(JwtAuth::validateToken())
	  		{

	  			$data['message'] = "Token Authenticated";
		    	$data['status'] = true;
		    	View::responseJson($data, 200);
	  		}
	  		else {

	  			$data['message'] = "Invalid Token";
		    	$data['status'] = false;
		    	View::responseJson($data, 401);

	  		}
		    
		  }
		  else
		      {
		          $data['message'] = "Un Authenticated token was not provided";
		          $data['status'] = false;
		          View::responseJson($data, 403);
		      }
	}

	public function login()
	{


    $creds = array(
    	'email'=> $_POST['email'],
    	'password' => $_POST['password']
    );



	    if( $payload = JwtAuth::findUserWithCreds($creds) )
	    {

	        
	        $token = JwtAuth::generateToken($payload);
	        $data['token'] = $token;



	        $data['status'] = true;
	        $data['message'] = 'user found';
	        
	        $data['user'] = $payload;
	        return View::responseJson($data, 200);
	    }
	    else
	        {
	            $data['status'] = false;
	            $data['message'] = 'user not found';
	            return View::responseJson($data, 400);
	        }

	}

	public function validateToken()
	{

		if( JwtAuth::validateToken() )
	    {

	        $data['status'] = true;
	        $data['message'] = 'user found';
	        $data['user'] = JwtAuth::$user;
	        return View::responseJson($data, 200);
	    }
	    else
	    {
	        $data['status'] = false;
	        $data['message'] = 'not a valid token';
	        return View::responseJson($data, 401);
	    }

	}

	public function adminOnlyProtected()
	{
		if( JwtAuth::validateToken() && JwtAuth::$user['role_id'] == 1)
    	{
        	$data['message'] = "you are admin you can access this route";
        	return View::responseJson($data, 200);
    	}

    	else {
        	$data['message'] = "Un Authorize attempt you don not have permission to access this route";
        	return View::responseJson($data, 401);
    	}
	}

}