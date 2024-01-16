<?php

namespace App\Controllers;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

class rolesCtrl extends BaseController
{

    private $layout;
    private $roleModule;

    public function __construct()
    {

        $template = new \Framework\Template();
        $this->layout = $template->layout('dashboard.php');
        $this->roleModule = new \App\Modules\roleModule();
    }

    public function roles()
    {

        $data['roles'] = $this->roleModule->returnAllRoles();
        $data['title'] = 'Dashboard - Roles';
        $this->layout->compile('dashboard/roles.php', $data);

        return;
    }


    public function saveRole()
    {

        $redirectUrl = $_SERVER['REDIRECT_URL'];

        $gump = new \GUMP();

        $_POST = $gump->sanitize($_POST);


        $gump->validation_rules(array(
            'role' => 'required|min_len,5'
        ));


        $pdata = $gump->run($_POST);


        if ($pdata == false) {
            $validationErrors = $gump->get_errors_array();
            $_SESSION['fClass'] = 'error';
            $_SESSION['flashMsg'] = array_values($validationErrors)[0];
            return $this->redirectBack();
        }


        $rolename = $_POST['role'];
        $roleModule = $this->roleModule;
        if (!$roleModule->pluckByRole($rolename)) {

            if ($roleModule->insert($rolename)) {

                $_SESSION['flashMsg'] = "New Role has been added";
                $_SESSION['fClass'] = 'success';
                return $this->redirectBack();
            } else {

                $_SESSION['flashMsg'] = "Failed whie adding new role";
                $_SESSION['fClass'] = 'error';
                return $this->redirectBack();
            }
        } else {

            $_SESSION['flashMsg'] = "Role Duplicate : $rolename already exists";
            $_SESSION['fClass'] = 'error';
            return $this->redirectBack();
        }
    }



    public function removeRole()
    {

        /*
        get the rold id to be removed
        check if that has permissions
        */
        $roleId = $this->getID();
        if ($this->roleModule->roleHasPermissions($roleId) == null) {
            if ($this->roleModule->remove($roleId)) {
                $data['message'] = 'atttempt to remove role';
                $statusCode = 200;
                return \Framework\View::responseJson($data, $statusCode);
            } else {
                $data['message'] = 'Error found while removing role';
                $statusCode = 500;
                return \Framework\View::responseJson($data, $statusCode);
            }
        } else {
            /*
            send response that role has roles with permission cannot be deleted.           
            */
            $data['message'] = 'Roles with permissions attached cannot be removed';
            return \Framework\View::responseJson($data, 406);
        }
    }

    public function test()
    {
        echo "hello from role ctrl";
    }
}
