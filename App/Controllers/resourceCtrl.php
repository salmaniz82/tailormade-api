<?php

namespace App\Controllers;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

class resourceCtrl extends BaseController
{

    public $layout;
    private $resourceModule;

    public function __construct()
    {

        $template = new \Framework\Template();
        $this->layout = $template->layout('dashboard.php');
        $this->resourceModule = new \App\Modules\resourceModule();
    }


    public function resource()
    {

        $data['actions'] = $this->resourceModule->returnAllResource();
        $data['title'] = 'Resource Actions';
        return $this->layout->compile('/dashboard/resource.php', $data);
    }


    public function saveResource()
    {

        $redirectUrl = $_SERVER['REDIRECT_URL'];

        if (isset($_POST['name']) && strlen($_POST['name']) > 3) {

            $resource = $_POST['name'];

            if (!$this->resourceModule->pluckByName($resource)) {


                if ($this->resourceModule->insert($resource)) {

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


    public function removeResource()
    {

        $id = $this->getID();

        $permissionModule = new \App\Modules\permissionsModule();

        if ($permissionModule->actionHasPermission($id) == null) {

            if ($this->resourceModule->remove($id)) {
                $data['message'] = 'Resource actions removed successfully';
                $statusCode = 200;
                return \Framework\View::responseJson($data, $statusCode);
            } else {
                $data['message'] = 'Error found while removing actions';
                $statusCode = 406;
                return \Framework\View::responseJson($data, $statusCode);
            }
        } else {

            $data['message'] = 'Permssion assinged to role cannot be remove';
            $statusCode = 406;
            return \Framework\View::responseJson($data, $statusCode);
        }
    }
}
