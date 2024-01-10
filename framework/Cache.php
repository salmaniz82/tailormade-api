<?php

namespace Framework;

class Cache
{


    static $Request;
    static $request;
    static $cachefile;
    static $cache_time = 3600;


    private function __construct()
    {
    }


    static function init()
    {

        $cache_dir = 'cache/';
        $cache_time = 3600; // 1 hour
        self::$Request = SITE_URL . $_SERVER['REQUEST_URI'];
        self::$cachefile = $cache_dir . md5(self::$Request) . '-cached.html';
    }


    static function exists()
    {

        self::init();

        if (file_exists(self::$cachefile) && (time() - self::$cache_time < filemtime(self::$cachefile))) {

            return true;
        }

        return false;
    }


    static function responseWithCache()
    {

        return readfile(self::$cachefile);
    }


    static function putResponse($contents)
    {

        file_put_contents(self::$cachefile, $contents);
        // Send the content to the browser


    }
}
