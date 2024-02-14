<?php

namespace App\Controllers;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

use App\Modules\swatchesModule;
use \Framework\View;



class swatchesCtrl
{


    private $swatchModule;


    public function __construct()
    {

        $this->swatchModule = new \App\Modules\swatchesModule();
    }

    private function abortUnAuthResponse()
    {

        $data['message'] = "Access Denied!";
        $statusCode = 401;

        return View::responseJson($data, $statusCode);
    }



    public function test()
    {


        $source = "foxflannel.com";

        $data = $this->swatchModule->buildFilterDynamic($source);


        var_dump($data);





        /*
        $data = $this->swatchModule->dynamicDBFilters($source);
        var_dump($data);
        die();
        */





        /*
        $stockModule = $this->swatchModule = new \App\Modules\stockModule();
        $data = $stockModule->swatchSources();
        var_dump($data);
        */





        // get the alias using url i.e source

        /*
        var_dump(\Framework\Auth::loginStatus());
        if ($result = $this->swatchModule->pluckAliasviaSource("foxflannel.com")) {
            var_dump($result);
        }
        */

        /*
        $swatch = $this->swatchModule->getSwatchByID(729);
        var_dump($swatch);
        $swatch['imageUrl'];
        $swatch['thumbnail'];
        */
    }

    public function unlinkSwatchImages($id)
    {

        if ($swatch = $this->swatchModule->getSwatchByID($id)) {

            extract($swatch);

            if (file_exists(ABSPATH . $imageUrl))
                unlink(ABSPATH . $imageUrl);


            if (file_exists(ABSPATH . $thumbnail))
                unlink(ABSPATH . $thumbnail);
        }
    }

    public function deleteSwatches()
    {

        if (!\Framework\Auth::loginStatus())
            return $this->abortUnAuthResponse();



        $id = \Framework\Route::$params['id'];

        $this->unlinkSwatchImages($id);

        if ($this->swatchModule->delete($id)) {
            $data["message"] = "Swatch Deleted!";
            return View::responseJson($data, 200);
        }

        $data["message"] = "Error while deleteing swatch";
        return View::responseJson($data, 200);
    }





    public function store()
    {


        $requiredFields = ['title', 'status', 'source', 'productMeta'];

        foreach ($requiredFields as $key => $field) {

            if (!isset($_POST[$field]) || empty($_POST[$field]) || $_POST[$field] == "" || $_POST[$field] == "undefined" || $_POST[$field] == 'false') {
                $data["message"] = "Invalid value for " . $field;
                $data["code"] = 406;
                return View::responseJson($data, 200);
                die();
            }
        }


        if (sizeof($_FILES) == 0) :

            $data["code"] = 406;
            return View::responseJson(["message" => "Image not provided"], 200);

            die();

        endif;




        if ($_FILES['file']['error'] === 0) {

            $doCompression = false;
            $fileSizeInMB = $_FILES['file']['size'] / (1024 * 1024);
            $fileInfo = pathinfo($_FILES['file']['name']);
            $fileBaseName = $fileInfo['filename'];
            $fileExtension = $fileInfo['extension'];

            $fileName = $_FILES['file']['name'];

            $maxFileSizeInBytes = 600 * 1024; // 600KB in bytes

            if ($_FILES['file']['size'] > $maxFileSizeInBytes) {
                $doCompression = true;
            }

            if (!$alias = $this->swatchModule->pluckAliasviaSource($_POST['source'])) {
                $data["message"] = " Not Valid Entry for stock Alias ";
                $data["code"] = 500;
                return View::responseJson($data, 200);
                die();
            }


            $aliasRootDirectory =  "uploads/images/$alias/";
            $aliasOriginalDirectory = "uploads/images/$alias/original/";
            $aliasThumbnailDirectory = "uploads/images/$alias/thumbnail/";

            $originalPath = $aliasOriginalDirectory . $fileName;

            if (!is_dir($aliasRootDirectory))
                mkdir($aliasRootDirectory, 0755, true);

            if (!is_dir($aliasOriginalDirectory))
                mkdir($aliasOriginalDirectory, 0755, true);


            if (!is_dir($aliasThumbnailDirectory))
                mkdir($aliasThumbnailDirectory, 0755, true);

            $rootPath = $aliasRootDirectory . $fileName;
            $thumbnailPath = $aliasThumbnailDirectory . $fileName;

            if (move_uploaded_file($_FILES['file']['tmp_name'],  $rootPath)) {

                $imageManager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver);
                $image = $imageManager->read($rootPath);
                $image->scale(width: 300, height: 300);
                $image->save($thumbnailPath);

                $payload['title'] = mysqli_real_escape_string($this->swatchModule->DB->connection, trim($_POST["title"]));
                $payload['source'] = mysqli_real_escape_string($this->swatchModule->DB->connection, trim($_POST["source"]));
                $payload['status'] = mysqli_real_escape_string($this->swatchModule->DB->connection, trim($_POST["status"]));
                $payload['productMeta'] = mysqli_real_escape_string($this->swatchModule->DB->connection, trim($_POST["productMeta"]));
                $payload['imageUrl'] = "/" . $rootPath;
                $payload['thumbnail'] = "/" . $thumbnailPath;
                $payload['productPrice'] = "n/a";

                if ($lastId = $this->swatchModule->addSwatch($payload)) {

                    $data["message"] = "Swatch added ";
                    $data["code"] = 200;
                    return View::responseJson($data, 200);
                } else {

                    $data["message"] = "Error occured while adding new swatch ";
                    $data["code"] = 500;
                    return View::responseJson($data, 200);
                }
            }
        } else {

            $data["message"] = "Fouund error in uploaded image ";
            $data["code"] = 406;
            return View::responseJson($data, 200);
        }
    }


    public function index()
    {
        echo SITE_URL;
    }

    public function swatchMeta()
    {

        if ($data["metadata"] = $this->swatchModule->getSwatchMeta()) {
            $data["message"] = "Success";
            return View::responseJson($data, 200);
            die();
        }



        $data["message"] = "Failed while fetching swatch meta data";
        return View::responseJson($data, 500);
        die();
    }



    private function handleStatusToggle($id, $status)
    {


        $dataPayload["status"] = ($status) ?  1 : 0;
        if ($this->swatchModule->update($dataPayload, $id)) {
            $data["message"] = "Status updated ";
            $data["operation-status"] = 'ok';
            $statusCode = 200;
        } else {

            $data["message"] = "Error while updating status";
            $data["operation-status"] = 'failed';
            $statusCode = 500;
        }

        return View::responseJson($data, 200);
        die();
    }

    private function handleUpdate($id, $request)
    {

        var_dump($request);

        die();

        $payload['title'] = mysqli_real_escape_string($this->swatchModule->DB->connection, trim($request["title"]));
        $payload['productMeta'] = json_encode($request["productMeta"]);
        $payload['source'] = mysqli_real_escape_string($this->swatchModule->DB->connection, trim($request["source"]));

        if ($this->swatchModule->update($payload, $id)) {
            $data["message"] = "Swatch updated successfully";
            $data["code"] = 200;
            $statusCode = 200;
            return View::responseJson($data, $statusCode);
            die();
        }
        $data["message"] = "Update operation error";
        $data["code"] = 500;
        $statusCode = 500;
        return View::responseJson($data, $statusCode);
        die();
    }



    public function update()
    {

        if (!\Framework\Auth::loginStatus())
            return $this->abortUnAuthResponse();


        $id = \Framework\Route::$params['id'];
        $request = \Framework\Route::$_PUT;


        if ($request["operation"] == "status-toggle")
            return $this->handleStatusToggle($id, $request["status"]);


        if ($request["operation"] == "content-update")
            return $this->handleUpdate($id, $request);


        /*
        $data["message"] = "this message is caught by the server";
        $data["POST"] = $_POST;
        $data["PuT"] = $_PUT;
        $statusCode = 200;
        return View::responseJson($data, $statusCode);
        $this->swatchModule->update($dataPayload, $id);
        */
    }


    public function batchStore()
    {

        /* saving NodeJS */

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
        $paramsQuery['source'] = (isset($_GET['source']) && $_GET['source'] != "") ? $_GET['source'] : 'all';
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
        $data['filters'] = $this->swatchModule->getCachedFilters($paramsQuery['source']) || [];

        $stockModule = $this->swatchModule = new \App\Modules\stockModule();
        $data['sources'] = $stockModule->swatchSources();

        return View::responseJson($data, 200);

        die();
    }

    public function writeToFile($data, $filepath)
    {

        $filePath = ABSPATH . $filepath;

        $directory = dirname($filePath);


        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }


        if (file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)))
            echo "file created successfully";
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



    public function updateFilters()
    {


        if (isset($_GET['source'])) {
            $source = $_GET['source'];

            $this->swatchModule->buildCachedFilters($source);
            $data["message"] = "Filter updated successfully";
            $statusCode = 200;
            return View::responseJson($data, $statusCode);
        }

        $data["message"] = "Error while updating filters";
        $statusCode = 500;
        return View::responseJson($data, $statusCode);
    }

    public function getSingle()
    {

        $id = \Framework\Route::$params['id'];

        if ($swatchData = $this->swatchModule->getSwatchByID($id)) {

            $data["message"] = "fetch Success";
            $data["swatch"] = $swatchData;
            $statusCode = 200;
            return View::responseJson($data, $statusCode);
        }

        $data["message"] = "Swatch not found";
        $statusCode = 404;
        return View::responseJson($data, $statusCode);
    }
}
