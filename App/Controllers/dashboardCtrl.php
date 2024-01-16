<?php

namespace App\Controllers;



if (!defined('ABSPATH')) die('Direct Access File is not allowed');

use App\Controllers\BaseController;
use \Framework\Auth;
use \Framework\View;

class dashboardCtrl extends BaseController
{

    public $DB;


    public function __construct()
    {

        /*
        if (!Auth::loginStatus()) {
            return header("Location: /login");
        }
        */
    }



    public function dasboardLanding()
    {
        

       return View::render('/dashboard/react-dashboard/dist/index.html', []);
    }
}
