<?php

namespace App\Controllers;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

class permissionsCtrl extends BaseController
{

    public $baseModule;

    public $layout;

    public function __construct()
    {
        $this->baseModule = new \App\Modules\permissionsModule();
        $template = new \Framework\Template();
        $this->layout = $template->layout('dashboard.php');
    }


    public function index()
    {


        $data['permissions'] = $this->baseModule->returnAll();


        $roleModule = new \App\Modules\roleModule();
        $data['roles'] = $roleModule->returnAllRoles();

        $resourceModule = new \App\Modules\resourceModule();
        $data['resources'] = $resourceModule->returnAllResource();


        $data['title'] = 'Dashboard - Permission';

        $this->layout->compile('dashboard/permissions.php', $data);
    }


    public function save()
    {

        $redirectUrl = $_SERVER['REDIRECT_URL'];


        if ((isset($_POST['role_id']) && is_numeric($_POST['role_id'])) && (isset($_POST['resource_id']) && is_numeric($_POST['resource_id']))) {

            $postData['role_id'] = $_POST['role_id'];
            $postData['resource_id'] = $_POST['resource_id'];

            if (!$this->baseModule->checkDuplicate($postData)) {


                $postData['public'] = (isset($_POST['public'])) ? 1 : 0;
                $postData['private'] = (isset($_POST['private'])) ? 1 : 0;


                if ($this->baseModule->insert($postData)) {

                    $_SESSION['flashMsg'] = "New Permission Added";
                    $_SESSION['fClass'] = 'success';
                    header("location: $redirectUrl");
                } else {

                    $_SESSION['fClass'] = 'error';
                    $_SESSION['flashMsg'] = "Failed while adding new permission";
                    header("location: $redirectUrl");
                }
            } else {

                $_SESSION['fClass'] = 'error';
                $_SESSION['flashMsg'] = "Duplicate Permissions cannot be added";
                header("location: $redirectUrl");
            }
        } else {

            $_SESSION['fClass'] = 'error';
            $_SESSION['flashMsg'] = " Invalid Request ";
            header("location: $redirectUrl");
        }
    }


    public function remove()
    {

        $id = $this->getID();

        $rolePermisisonModule = new \App\Modules\permissionsModule();

        if ($rolePermisisonModule->removeRolePermission($id)) {

            $data['message'] = 'Permission removed succesfully';
            return \Framework\View::responseJson($data, 200);
        }

        $data['message'] = 'Error while removing permission';
        return \Framework\View::responseJson($data, 500);
    }
}
