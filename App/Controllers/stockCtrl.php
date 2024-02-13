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

    public function save()
    {

        extract($_POST);
        $dataPayload = array(
            'title' => $title,
            'url' => $url,
            'alias' => $alias,
            'metaFields' => json_encode($metaFields)
        );

        try {

            if ($lastId = $this->stockModule->save($dataPayload)) {

                $data["message"] = "New stock added";

                if ($newStock = $this->stockModule->getStockByID($lastId)) {
                    $data["newStock"] = $newStock;
                }

                $statusCode = 200;
                return View::responseJson($data, $statusCode);
            } else {
                $data["message"] = "Operation failed";
                $statusCode = 500;
                return View::responseJson($data, $statusCode);
            }
        } catch (\Exception $error) {

            $data["message"] = "Invalid data, please check duplicate entries";
            $data["debug"] = $error->getMessage();
            $statusCode = 500;
            return View::responseJson($data, $statusCode);
        }
    }

    public function delete()
    {

        $id = \Framework\Route::$params['id'];

        if ($this->stockModule->delete($id)) {
            $data["message"] = "Stock Deleted!";
            return View::responseJson($data, 200);
        }

        $data["message"] = "Error while deleteing record";
        return View::responseJson($data, 200);
    }
}
