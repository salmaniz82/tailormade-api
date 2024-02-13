<?php

namespace App\Controllers;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

use App\Controllers\BaseController;
use \Framework\Auth;
use \Framework\View;

class stockCtrl extends BaseController
{

    private $stockModule;


    public function __construct()
    {

        $this->stockModule = new \App\Modules\stockModule();
    }

    public function listCollections()
    {

        if ($stocks = $this->stockModule->getStocks()) {

            $data["stocks"] = $stocks;
            $data["message"] = "success";
            $statusCode = 200;
            return View::responseJson($data, $statusCode);
        }

        $data["message"] = "Record not found";
        $statusCode = 404;
        return View::responseJson($data, $statusCode);
    }
}
