<?php 

class userCtrl extends appCtrl {

	public $layout;


	public function __construct(){

		$layout = new Template();

		$template = new Template();
        $this->layout = $template->layout('auth-tpl.php');
	}


	public function dashboardUserList(){


		$template = new Template();
        $dashboardLayout = $template->layout('dashboard.php');
		$data['title'] = 'Dashboard - Users';
		$userModule = $this->load('module', 'users');
		$users = $userModule->fetchUserDashboard();
		$data['users'] = $users;


        return $dashboardLayout->compile('dashboard/users.php', $data);

	}




	public function showLogin() 
	{

		if(Auth::loginStatus())
		{
            return $this->redirect('/dashboard/todos');
		}

		$data['title'] = 'Login';

		$this->layout->compile('login.php', $data);

		return;

		/*
    	View::render('login', $data);
		*/

	}

	public function doLogin() 
	{



		if( (isset($_POST['email']) && $_POST['email'] != NULL) && (isset($_POST['password']) && $_POST['password'] != NULL) ) {
        

        $creds['email'] = $_POST['email'];
        $creds['password'] = $_POST['password'];

	        if (filter_var($creds['email'], FILTER_VALIDATE_EMAIL)) 
	        {          
	            // do the login stuff

	            if( $user = Auth::attemptLogin($creds) ) 
	            {
                        if(Auth::User()['isActive'] == 0 && Auth::User()['id'] != 1){
                            Auth::logout();
                            $_SESSION['flashMsg'] = 'User is disabled';
                            $_SESSION['fClass'] = 'error';
                            return header('location: /login');
                        }

	            		Auth::check()->id = Auth::User()['id'];
		                header('location: /dashboard/todos');
	                
	            } 
	            else 
		        {
		                $_SESSION['flashMsg'] = 'Invalid Credentials';
		                $_SESSION['fClass'] = 'error';
		                header("location: /login");

		        }


	        } 
	        else 
		    {
		        $data['message'] = 'Invalid Email';
		    }
	     
    	} 
    	else 
	    {
	    	 $data['message'] = 'creds not found';    
	    }

		   
	    View::render('page', $data);

	

	}

	public function logout() 
	{
		
		Auth::logout();
		header('location: /login');
		
    	
	}

	public function showProfile()
	{
		

	if( !Auth::loginStatus() ) 
     {
        
    	$data['message'] = 'This is protected you are not allowed to see details unless your are logged IN';
    	return View::render('page', $data);
     }

  
     	$data['title'] = 'Profile';
		$data['message'] = 'User Profile Details can be seen here ...';
		$data['profile'] =  Auth::User();

		unset($data['profile']['password']);

		
    	View::render('page', $data);
	}


	public function showRegister()
	{
		
		$data['title'] = 'Registration';
		$data['message'] = 'Please enter your details for signing up';

		$this->layout->compile('register.php', $data);

	}

	public function doRegister()
	{


        require_once ABSPATH . '/vendor/autoload.php';
        $gump = new GUMP();
        $_POST = $gump->sanitize($_POST);


        $gump->validation_rules(array(
            'name' => 'required|min_len,4',
            'email'  => 'required|valid_email',
            'password'    => 'required',
            'c_password'    => 'required'
        ));

        $pdata = $gump->run($_POST);

        if($pdata === false){
            $validationErrors = $gump->get_errors_array();
            $_SESSION['flashMsg'] = array_values($validationErrors)[0];
            $_SESSION['fClass'] = 'error';
            return $this->redirectBack();
        }

        if($_POST['password'] != $_POST['c_password'] ){
            $_SESSION['flashMsg'] = 'Mismatched confirm password';
            $_SESSION['fClass'] = 'error';
            return $this->redirectBack();
        }


		$db = new Database();
		$db->table = 'users';


		$user['name'] = $_POST['name'];
		$user['email'] = $_POST['email'];


        $roleModule = $this->load('module', 'role');
        $user['role_id'] = $roleModule->pluckIdRole('content creator');

		$password = $_POST['password'];


        $userModule = $this->load('module', 'users');

        if($userModule->emailExists($user['email'])) {

            $_SESSION['flashMsg'] = 'Email Already Exists';
            $_SESSION['fClass'] = 'error';
            return $this->redirectBack();
            die();

        }
		
		$password = password_hash($password, PASSWORD_BCRYPT, array(
		'cost' => 12
		));

		$user['password'] = $password;

		if( $db->insert($user) ) 
		{

			$_SESSION['flashMsg'] = 'Registration is Successful';
		    $_SESSION['fClass'] = 'success';

			$this->redirect('/register');
			
		} else 
			{

			$data['message'] = 'Some thing went wrong during registration';
			$_SESSION['fClass'] = 'error';
			$this->redirect('/register');

			}
	}

	public function checkReturnAuthenticatedUser()
	{

		if(Auth::loginStatus())
		{
			
			$response['status'] = true;
			$response['userCount'] = 1;
			$response['user'] = Auth::User();
			
		}
		else
		{

			$response['status'] = false;
			$response['userCount'] = 0;
			

		}

	View::responseJson($response);

	}



	public function udpatePasswordHash()
	{


		$db = new Database();
        $db->table = 'users';

        $users = $db->listall()->returnData();

        foreach($users as $key => $user)
        {


        	$oldPassword = $user['password'];

        	$hashedPassword = password_hash($oldPassword, PASSWORD_BCRYPT, array(
				'cost' => 12
			));

			$data['password'] = $hashedPassword;

			$id = (int) $user['id'];


			if($db->update($data, $id))
			{
				echo $user['name'] . "updated hash success" . "<br>";
			}
			else {

				echo "Failed " . $user['name'] . "<br>";

			}

        }


	}


	public function removeUser(){

		$id = $this->getID();

        if($id == 1) {
            $data['message'] = 'Root Admin delete not allowed';
            $statusCode = 406;
            return View::responseJson($data, $statusCode);
        }

		$userModule = $this->load('module', 'users');


		if($user =  $userModule->getUserById($id) ) {

			if($user['isActive'] == 1) {

				$data['message'] = 'User with active status cannot be removed';
				$statusCode = 406;
				return View::responseJson($data, $statusCode);	
			}

		}

		else {

			$data['message'] = 'User not found';
			$statusCode = 406;
			return View::responseJson($data, $statusCode);

		}

		if($userModule->remove($id)) {
			$data['message'] = 'User has been removed';
			$statusCode = 200;
			return View::responseJson($data, $statusCode);
		}

		$data['message'] = 'Error while removing user';
		$statusCode = 500;
		return View::responseJson($data, $statusCode);

	}


    public function stateToggle(){

        $id = $this->getID();

        if(!isset($_POST['isActive'])){

            $data['message'] = 'User state not provided';
            $statusCode = 406;
            return View::responseJson($data, $statusCode);
        }

        if($id == 1 && $_POST['isActive'] == 0){
            $data['message'] = 'Root Admin cannot be deactivated';
            $statusCode = 406;
            return View::responseJson($data, $statusCode);
        }



        $userModule = $this->load('module', 'users');

        $payload = array(
            'isActive' => $_POST['isActive']
        );

        if($userModule->updateUser($payload, $id)){

            $userState = ($_POST['isActive'] == 1) ? 'Active' : 'Inactive';

            $data['message'] = 'User state updated to : ' . $userState;
            $data['isActive'] = $userState;
            $statusCode = 200;
            return View::responseJson($data, $statusCode);
        }
        $data['message'] = 'Error, Cannot update user state';
        $statusCode = 500;
        return View::responseJson($data, $statusCode);

    }

}