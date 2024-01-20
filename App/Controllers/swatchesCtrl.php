<?php

namespace App\Controllers;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

use \Framework\View;


class swatchesCtrl
{


    private $swatchModule;


    public function __construct()
    {

        $this->swatchModule = new \App\Modules\swatchesModule();
    }


    public function index()
    {
        echo SITE_URL;
    }

    public function handleStatusToggle($id, $status) {

        $data["message"] = "working on status toggle";
        $dataPayload["status"] = $status;
        if($this->swatchModule->update($dataPayload, $id)) {
           $data["message"] = "Status updated ";
           $data["operation-status"] = 'ok';
           $statusCode = 200;
        }
        else {

            $data["message"] = "Error while updating status";
            $data["operation-status"] = 'failed';
            $statusCode = 500;

        }

        return View::responseJson($data, 200);
        die();

    }



    public function update()
    {

        $id = \Framework\Route::$params['id'];
        $request = \Framework\Route::$_PUT;
       

        if($request["operation"] == "status-toggle")
            return $this->handleStatusToggle($id, $request["status"]);


        /*
        $data["message"] = "this message is caught by the server";
        $data["POST"] = $_POST;
        $data["PuT"] = $_PUT;
        $statusCode = 200;
        return View::responseJson($data, $statusCode);
        $this->swatchModule->update($dataPayload, $id);
        */

    }


    public function store()
    {

        $key = array('title', 'productPrice', 'imageUrl', 'productMeta', 'source');
        $error = 0;
        foreach ($key as $key => $value) {

            if (!isset($_POST[$value]) || $_POST[$value] == "") {

                $error++;
            }
        }


        if ($error > 0) {
            $dataPayload['message'] = "Required fields are missing";
            return View::responseJson($dataPayload, 406);
        }

        $swatchPayload['title'] = $_POST['title'];
        $swatchPayload['productPrice'] = $_POST['productPrice'];
        $swatchPayload['imageUrl'] = $_POST['imageUrl'];
        $swatchPayload['source'] = $_POST['source'];
        $swatchPayload['productMeta'] = json_encode($_POST['productMeta']);


        if ($lastid = $this->swatchModule->addSwatch($swatchPayload)) {
            $statusCode = 201;
            $response['message'] = "Swatch added successfully";
            return View::responseJson($response, $statusCode);
        }

        $statusCode = 500;
        $response['message'] = "Failed while adding a record";
        return View::responseJson($response, $statusCode);
    }


    public function listSwatches()
    {


        $paramsQuery['page'] = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
        $paramsQuery['limit'] = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 12;
        $paramsQuery['offset'] = ($paramsQuery['page'] - 1) * $paramsQuery['limit'];
        $paramsQuery['filteringActivate'] = (isset($_GET['filteringActivate']) && $_GET['filteringActivate'] != "") ? $_GET['filteringActivate'] : 'off';
        $paramsQuery['source'] = (isset($_GET['source']) && $_GET['source'] != "") ? $_GET['source'] : 'foxflannel.com';
        $paramsQuery['status'] = isset($_GET['status']) ?: '1';

        if ($paramsQuery['filteringActivate'] == 'on') :
            $filteryParamKeys = $this->swatchModule->getSourceFilterStaticKeys($paramsQuery['source']);
            foreach ($filteryParamKeys as $key => $filterParamKey) :
                if (isset($_GET[$filterParamKey]) && $_GET[$filterParamKey] != "")
                    $paramsQuery[$filterParamKey] = trim($_GET[$filterParamKey]);
            endforeach;
        endif;


        $data["meta"] = array(
            "page" => $paramsQuery['page'],
            "limit" => $paramsQuery['limit'],
            "offset" => $paramsQuery['offset'],
            "total" => 0
        );

        $data['collections'] = $this->swatchModule->getSwatces($paramsQuery);
        $totalMatched = $this->swatchModule->getTotalMatched();
        $data["meta"]["total"] = $totalMatched;
        $data["meta"]['pages'] = ceil($totalMatched / $paramsQuery['limit']);
        $data["meta"]['source'] = $paramsQuery['source'];
        // refactored dynamic filter
        /*
        $data['filters'] = $this->swatchModule->buildFilterDynamic($paramsQuery['source']);
        */
        $data['filters'] = $this->swatchModule->getCachedFilters($paramsQuery['source']);

        return View::responseJson($data, 200);

        die();
    }






    public function processRequestSwatches()
    {


        if (sizeof($_POST) == 0) return View::responseJson(["message" => "empty request"], 406);
        $email = (isset($_POST['customerEmail'])) ? trim($_POST['customerEmail']) : NULL;
        if (!$email) return View::responseJson(["message" => "Missing Email"], 406);


        /*
        $swatchesTemplatePath = ABSPATH . '/pages/swatches-email.php';
        include_once($swatchesTemplatePath);
        */


        $data = $_POST;

        ob_start();
        View::render('swatches-email', $data);
        $bodyMessage = ob_get_contents();
        ob_end_clean();


        try {
            $emailModule = new \App\Modules\emailModule();
            $mail = $emailModule->getConfigMailer();
            $mail->Subject = "Testing local for tailormade : React Swatches";
            $mail->FromName = 'Tailormde shopify';
            $mail->addAddress("sa2kdev@gmail.com");
            $mail->Body = $bodyMessage;
            $mail->send();
            return View::responseJson(["message" => "Swatch request sent successfully"], 200);
        } catch (\Exception $error) {

            return View::responseJson(["message" => "Error sending request please try again or later"], 500);
        }
    }


    public function testEmail()
    {

        $data = [
            'customerEmail' => 'hello@domain.com',
            'simulate' => 'error',
            'swachtes' => [
                [
                    'id' => 229,
                    'title' => 'Bottle Green, Admiral Blue & Stout Black Check',
                    'imageUrl' => '/uploads/images/fxf/SP1.jpg',
                    'productPrice' => 'Price £158',
                    'productMeta' => '{"COLOUR": "Multi", "PATTERN": "Check", "CLOTH CODE": "SP1", "METRIC WEIGHT": "400/430g", "IMPERIAL WEIGHT": "14/15oz"}',
                    'source' => 'foxflannel.com',
                    'status' => 1,
                    'isRemoving' => false,
                ],
                [
                    'id' => 230,
                    'title' => 'Bottle Brown & Admiral Blue Check',
                    'imageUrl' => '/uploads/images/fxf/SP2.jpg',
                    'productPrice' => 'Price £158',
                    'productMeta' => '{"COLOUR": "Multi", "PATTERN": "Check", "CLOTH CODE": "SP2", "METRIC WEIGHT": "400/430g", "IMPERIAL WEIGHT": "14/15oz"}',
                    'source' => 'foxflannel.com',
                    'status' => 1,
                    'isRemoving' => false,
                ],
                [
                    'id' => 228,
                    'title' => 'Classic Flannel Plain Denim Blue',
                    'imageUrl' => '/uploads/images/fxf/CL2-12.jpg',
                    'productPrice' => 'Price £163',
                    'productMeta' => '{"COLOUR": "Blue", "PATTERN": "Plain", "CLOTH CODE": "CL2-12", "METRIC WEIGHT": "370/400g", "IMPERIAL WEIGHT": "13/14oz"}',
                    'source' => 'foxflannel.com',
                    'status' => 1,
                    'isRemoving' => false,
                ],
                [
                    'id' => 233,
                    'title' => 'Classic Flannel Plain Slate Blue',
                    'imageUrl' => '/uploads/images/fxf/CL2-11.jpg',
                    'productPrice' => 'Price £163',
                    'productMeta' => '{"COLOUR": "Blue", "PATTERN": "Plain", "CLOTH CODE": "CL2-11", "METRIC WEIGHT": "370/400g", "IMPERIAL WEIGHT": "13/14oz"}',
                    'source' => 'foxflannel.com',
                    'status' => 1,
                    'isRemoving' => false,
                ],
            ],
        ];

        try {
            $emailModule = new \App\Modules\emailModule();
            $mail = $emailModule->getConfigMailer();
            $mail->Subject = "Testing local for tailormade";
            $mail->FromName = 'iSkillmetrics';
            $mail->addAddress("sa2kdev@gmail.com");

            /*
            $swatchesTemplatePath = ABSPATH . '/pages/swatches-email.php';
            include_once($swatchesTemplatePath);
            */

            ob_start();

            View::render('swatches-email', $data);
            $bodyMessage = ob_get_contents();
            ob_end_clean();

            $mail->Body = $bodyMessage;
            $mail->send();
            echo "sent";
        } catch (\Exception $error) {
            echo var_dump($error);
        }
    }


    public function loadSwatchTemplate()
    {

        $data = [
            'customerEmail' => 'hello@domain.com',
            'simulate' => 'error',
            'swachtes' => [
                [
                    'id' => 229,
                    'title' => 'Bottle Green, Admiral Blue & Stout Black Check',
                    'imageUrl' => '/uploads/images/fxf/SP1.jpg',
                    'productPrice' => 'Price £158',
                    'productMeta' => '{"COLOUR": "Multi", "PATTERN": "Check", "CLOTH CODE": "SP1", "METRIC WEIGHT": "400/430g", "IMPERIAL WEIGHT": "14/15oz"}',
                    'source' => 'foxflannel.com',
                    'status' => 1,
                    'isRemoving' => false,
                ],
                [
                    'id' => 230,
                    'title' => 'Bottle Brown & Admiral Blue Check',
                    'imageUrl' => '/uploads/images/fxf/SP2.jpg',
                    'productPrice' => 'Price £158',
                    'productMeta' => '{"COLOUR": "Multi", "PATTERN": "Check", "CLOTH CODE": "SP2", "METRIC WEIGHT": "400/430g", "IMPERIAL WEIGHT": "14/15oz"}',
                    'source' => 'foxflannel.com',
                    'status' => 1,
                    'isRemoving' => false,
                ],
                [
                    'id' => 228,
                    'title' => 'Classic Flannel Plain Denim Blue',
                    'imageUrl' => '/uploads/images/fxf/CL2-12.jpg',
                    'productPrice' => 'Price £163',
                    'productMeta' => '{"COLOUR": "Blue", "PATTERN": "Plain", "CLOTH CODE": "CL2-12", "METRIC WEIGHT": "370/400g", "IMPERIAL WEIGHT": "13/14oz"}',
                    'source' => 'foxflannel.com',
                    'status' => 1,
                    'isRemoving' => false,
                ],
                [
                    'id' => 233,
                    'title' => 'Classic Flannel Plain Slate Blue',
                    'imageUrl' => '/uploads/images/fxf/CL2-11.jpg',
                    'productPrice' => 'Price £163',
                    'productMeta' => '{"COLOUR": "Blue", "PATTERN": "Plain", "CLOTH CODE": "CL2-11", "METRIC WEIGHT": "370/400g", "IMPERIAL WEIGHT": "13/14oz"}',
                    'source' => 'foxflannel.com',
                    'status' => 1,
                    'isRemoving' => false,
                ],
            ],
        ];

        View::render('swatches-email', $data);
    }



    public function testBuildFilter()
    {
        $source = 'harrisons1863.com';
        $this->swatchModule->buildCachedFilters($source);
    }
}
