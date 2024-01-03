<?php 

class resourceCtrl extends appCtrl
{

    public $layout;

    public function __construct()
    {

        $template = new Template();
        $this->layout = $template->layout('dashboard.php');
        
    }


	public function resource()
    {

        $resourceModule = $this->load('module', 'resource');
        $data['actions'] = $resourceModule->returnAllResource();
        $data['title'] = 'Resource Actions';
        return $this->layout->compile('/dashboard/resource.php', $data);

    }


    public function saveResource()
    {

        $redirectUrl = $_SERVER['REDIRECT_URL'];

        if(isset($_POST['name']) && strlen($_POST['name']) > 3)
        {

            $resource = $_POST['name'];       
            $resourceModule = $this->load('module', 'resource');
            if(!$resourceModule->pluckByName($resource))
            {
                

                if($resourceModule->insert($resource))
                {

                    $_SESSION['flashMsg'] = "New Resource has been added";
                    $_SESSION['fClass'] = 'success';
                    header("location: $redirectUrl");


                } else {

                    $_SESSION['flashMsg'] = "Failed whie adding new Resource";
                    $_SESSION['fClass'] = 'error';
                    header("location: $redirectUrl");
                }

            } else {
                    
                    $_SESSION['flashMsg'] = "Resource: $resource already exists";
                    $_SESSION['fClass'] = 'error';
                    header("location: $redirectUrl");
            }

        } else {

                $_SESSION['flashMsg'] = "Resource must be more than 4 character";
                $_SESSION['fClass'] = 'error';
                header("location: $redirectUrl");
        }


    }


    public function removeResource(){

        $id = $this->getID();

        $resourceModule = $this->load('module', 'resource');

        $permissionModule = $this->load('module', 'permissions');

        if($permissionModule->actionHasPermission($id) == null){

            if($resourceModule->remove($id)){
                $data['message'] = 'Resource actions removed successfully';
                $statusCode = 200;
                return View::responseJson($data, $statusCode);
            } else {
                $data['message'] = 'Error found while removing actions';
                $statusCode = 406;
                return View::responseJson($data, $statusCode);
            }


        }

        else {

            
            $data['message'] = 'Permssion assinged to role cannot be remove';
            $statusCode = 406;
            return View::responseJson($data, $statusCode);
        }

        /*
        if this attached to a role this should be permitted to be removed
        */


        

        

    }

}