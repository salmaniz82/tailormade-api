<?php

namespace Framework;

class Auth
{

    protected static $isLoggedIn;
    public $id;
    protected static $role = null;
    protected static $name = null;
    protected static $email = null;
    private static $_instance = null;
    protected static $user = [];


    public static function loginStatus()
    {

        if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
            return true;
        } else {
            return false;
        }
    }

    public static function setLogin()
    {
        self::$isLoggedIn = true;
    }

    public static function User()
    {
        if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
            //$this->id = $_SESSION['user']['id'];
            return $_SESSION['user'];
        }
    }

    public static function check()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public static function logout()
    {
        $_SESSION['isLoggedIn'] = false;
        unset($_SESSION['isLoggedIn']);
        unset($_SESSION['user']);
        return  self::$isLoggedIn = false;
    }

    public static function attemptLogin($creds)
    {

        $db = new Database();
        $db->table = 'users';
        $email = mysqli_real_escape_string($db->connection, $creds['email']);
        $password = mysqli_real_escape_string($db->connection, $creds['password']);

        /*
        i need to check the user via email and get this in variable
        */

        $user = $db->build('S')->Colums()->Where("email = '" . $email . "'")->go()->returnData();

        if ($user != NULL) {

            $storedPassword = $user[0]['password'];

            if (password_verify($password, $storedPassword)) {

                unset($user['password']);

                $_SESSION['isLoggedIn'] = true;
                self::$isLoggedIn = $_SESSION['isLoggedIn'];
                $_SESSION['user'] = $user[0];
                self::$user = $_SESSION['user'];
                return $user;
            } else {
                return false;
            }
        } else {

            return false;
        }
    }
}
