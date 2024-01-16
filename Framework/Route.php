<?php

namespace Framework;

class Route
{
    public $req_uri; /* incomming server uri in arrays */
    public $registered = array();
    public $segments;
    static $params = null;
    public $method;
    public $appURI;
    public $serverRawURI;
    static $activeRoute;
    static $_PUT;
    static $_DELETE;

    public function __construct()
    {

        $this->enableCORS();
        /*
        $this->enableSSEheader();
        $this->enableCache();
        */
        $this->serverRawURI = $_SERVER['REQUEST_URI'];
        $uri = explode('/', $this->serverRawURI);
        $uri[0] = '/';
        $uri = array_values(array_filter($uri));
        $this->req_uri = $uri;
        $this->method = $_SERVER['REQUEST_METHOD'];
    }


    public static function refinePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('/\/+/', '/', $path);
        return $path;
    }

    public function enableCache()
    {

        header('Cache-Control: max-age=31536000');
    }

    public function enableCORS()
    {


        if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != "") {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");

            /*AMP specific*/

           
            /*
            header("AMP-Access-Control-Allow-Source-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");
            header("Access-Control-Expose-Headers: AMP-Redirect-To, AMP-Access-Control-Allow-Source-Origin");
            */
        } else {
            header("Access-Control-Allow-Origin: *");
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:  {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }




    public function breakStringToArray($string)
    {
        $string = explode('/', $string);
        $string[0] = '/';
        $string = array_values(array_filter($string));
        return $string;
    }

    public function joinArrayToUrlString($string)
    {
        $string = implode('/', $string);
        $string = preg_replace('#/+#', '/', $string);
        return $string;
    }


    public function getRoute()
    {
        return $this->uriSegment();
    }



    public function get($appUri, $callback)
    {

        if ($this->method == 'GET') {
            $this->execute($appUri, $callback);
            return $this;
        }
    }

    public function post($appUri, $callback)
    {

        if ($this->method == 'POST') {

            if (isset($_SERVER["CONTENT_TYPE"])) {
                if (strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                    $this->setAcceptJson();
                }
            }

            $this->execute($appUri, $callback);
        }
    }

    public function put($appUri, $callback)
    {

        if ($this->method == 'PUT') {


            if (isset($_SERVER["CONTENT_TYPE"])) {
                if (strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {

                    /*
                                $_PUT = file_get_contents("php://input");
                                string 
                                '{"user":"salman","email":"hello"}'
                                
                        */

                    $_PUT = json_decode(file_get_contents('php://input'), true);
                } else {

                    /*
                        $_PUT = file_get_contents("php://input");
                        String
                        'user=salman&email=hello'
                    */

                    parse_str(file_get_contents("php://input"), $_PUT);
                }

                self::$_PUT = $_PUT;
            }

            $this->execute($appUri, $callback);
        }
    }

    public function delete($appUri, $callback)
    {

        if ($this->method == 'DELETE') {
            if (isset($_SERVER["CONTENT_TYPE"])) {
                if (strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {

                    /*
                                $_PUT = file_get_contents("php://input");
                                string 
                                '{"user":"salman","email":"hello"}'
                                
                        */

                    $_DELETE = json_decode(file_get_contents('php://input'), true);
                } else {

                    /*
                        $_PUT = file_get_contents("php://input");
                        String
                        'user=salman&email=hello'
                    */

                    parse_str(file_get_contents("php://input"), $_DELETE);
                }

                self::$_DELETE = $_DELETE;
            }

            $this->execute($appUri, $callback);
        }
    }

    protected function uriSegment()
    {


        $string = rtrim($this->serverRawURI, '/');
        if ($string == '') {
            return '/';
        } else {
            return $string;
        }
    }

    static function Params()
    {
        return new self;
    }


    public function execute($appUri, $callback)
    {

        $this->appURI = $appUri;
        /*
        1. check if appURI has Params if no then continue if yes 
        2. if found modify appURI
       */

        $countAppUriSEgment = sizeof($this->breakStringToArray($appUri));
        $serverUriSegment = sizeof($this->req_uri);

        if ($countAppUriSEgment == $serverUriSegment) {

            if (!in_array($appUri, $this->registered)) {

                // checking if appuri has some patterns signatures
                if (preg_match_all('/\{(.*?)\}/', $appUri, $matchedParamItemsValues)) {



                    $appUriPieces = $this->breakStringToArray($appUri);


                    foreach ($matchedParamItemsValues[0] as $key => $value) {
                        // checking the indexes of params signatures in array provides in appURI

                        $paramIndexes[] = array_search($value, $appUriPieces);
                    }

                    // replace params signature with actual macthed values from incomming server URI 



                    foreach ($paramIndexes as $key => $value) {

                        $appUriPieces[$value] = $this->req_uri[$value];
                        $paramTemp[$matchedParamItemsValues[1][$key]] =    $this->req_uri[$value];
                    }

                    self::$params = $paramTemp;

                    $newUrlString = $this->joinArrayToUrlString(array_values($appUriPieces));

                    $appUri = $newUrlString;
                }


                /*start ?*/
                if (strpos($appUri, '?') && !strpos($this->serverRawURI, '?')) {
                    $appUri = rtrim($appUri, '?');
                } elseif (strpos($appUri, '?') && strpos($this->serverRawURI, '?')) {

                    $apendURIQuery = substr($this->serverRawURI, strpos($this->serverRawURI, "?") + 1);
                    $appUri = $appUri . $apendURIQuery;
                }
                /*end ?*/
            }
        }


        $this->registered[] = $appUri;


        if ($appUri == $this->uriSegment($appUri)) {


            if (is_callable($callback)) {
                //return $callback();
                $callback();
                die();
                return $this;
            }

            if (is_array($callback)) {

                echo "Refactor to PSR-4 : pending";
            }

            if (is_string($callback) == 'string') {


                $callback = explode('@', $callback);

                $controllerClass = 'App\\Controllers\\' . $callback[0];


                if (class_exists($controllerClass)) :

                    $controller = new $controllerClass();
                    $controllerMethod = $callback[1];

                    if (method_exists($controller, $controllerMethod)) :

                        return $controller->$controllerMethod();
                        die();

                    else :

                        echo "Method don't Exists";

                    endif;

                else :

                    echo "Controller Not found";



                endif;
            }
        }
    }


    static function Current($menuLink)
    {

        if ($menuLink == $_SERVER['REQUEST_URI']) {
            self::$activeRoute = true;
        } else {
            self::$activeRoute = false;
        }
        echo (self::$activeRoute == true ? 'active' :  '');
    }


    static function setPostJson()
    {
        $_POST = json_decode(file_get_contents('php://input'), true);
    }

    public function setAcceptJson()
    {

        $_POST = json_decode(file_get_contents('php://input'), true);
    }

    public function filter($callback)
    {

        if ($callback())
            return $this;
    }


    public static function crossFire($routeAddr, $method = null, $pushData = null)
    {

        $url = siteURL() . $routeAddr;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);


        if ($method == 'PUT') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($pushData));
        } elseif ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($pushData));
        } elseif ($method == 'DELETE') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($pushData));
        } else {
        }

        $headers = [
            "Content-Type: application/json; charset=utf-8",
        ];


        if (isset($pushData)) {
            $data_string = strlen(json_encode($pushData));
            $data_string = "Content-Length: {$data_string}";
            array_push($headers, $data_string);
        }


        if ($token = jwtAuth::hasToken()) {

            $string = "token: {$token}";

            array_push($headers, $string);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        return $response;
    }



    public function otherwise($callback)
    {
        if (!in_array($this->getRoute(), $this->registered)) {
            $callback();
            die();
            return false;
        }
    }


    public function enableSSEheader()
    {
        if (isset($_SERVER["HTTP_LAST_EVENT_ID"])) {
            header("Content-Type: text/event-stream");
            header("Cache-Control: no-cache");
            header("Connection: keep-alive");
        }
    }
}
