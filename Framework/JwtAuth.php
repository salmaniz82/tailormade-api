<?php

namespace Framework;


class JwtAuth
{

    public static $isLoggedIn;
    public static $user;


    public static function hasToken()
    {
        if (isset($_SERVER['HTTP_TOKEN']) && $_SERVER['HTTP_TOKEN'] != null) {

            return $_SERVER['HTTP_TOKEN'];
        } else {
            return false;
        }
    }

    public static function findUserWithCreds($creds)
    {
        $db = new Database();
        $db->table = 'users';
        $email = mysqli_real_escape_string($db->connection, $creds['email']);
        $password = mysqli_real_escape_string($db->connection, $creds['password']);

        $result = $db->build('S')->Colums()->Where("email = '" . $email . "'")->go()->returnData();

        if ($result != null) {

            $storedPassword = $result[0]['password'];

            if (password_verify($password, $storedPassword)) {
                unset($result[0]['password']);
                self::$isLoggedIn = true;
                self::$user = $result[0];

                return $user = $result[0];
            } else {
                return false;
            }
        } else {
            self::$isLoggedIn = false;
            return false;
        }
    }

    public static function generateToken($payload)
    {

        $user_id = $payload['id'];

        $payload['stamp'] = Date('h:m:s');

        $header = ["alg" => "HS256", "typ" => "JWT"];

        $header = base64_encode(json_encode($header));

        $payload = base64_encode(json_encode($payload));

        $key = self::getKey();

        $signature = hash_hmac('sha256', "$header.$payload", $key, true);

        $signature = base64_encode($signature);

        $token = "$header.$payload.$signature";

        return self::tokenDbProcedure($token, $user_id);
    }

    public static function tokenDbProcedure($token, $user_id = null)
    {
        $db = new Database();
        $db->table = 'user_token';

        $data = array('user_id' => $user_id, 'token' => $token);


        if ($storedToken = self::findExistingToken(null, $user_id)) {

            $user_id = self::$user['id'];

            if (self::updateToken($token, $user_id)) {
                return $token;
            } else {
                return "Error while updateing token";
            }
        } else if ($newToken = $db->insert($data)) {
            return $token;
        } else {
            return false;
        }
    }

    public static function findExistingToken($token = null, $user_id = null)
    {
        $db = new Database();
        $db->table = 'user_token';

        if ($user_id != null) {
            $searchCondition = "user_id = " . $user_id;
        } else {
            $searchCondition = "token = '" . $token . "'";
        }


        if ($storedToken = $db->build('S')->Colums()->Where($searchCondition)->go()->returnData()) {

            $db->table = 'users';

            $user_id = $storedToken[0]['user_id'];

            $user = $db->getbyId($user_id, ['id', 'name', 'email', 'role_id'])->returnData();

            self::$user = $user[0];

            return $storedToken[0]['token'];
        } else {
            return false;
        }
    }


    public static function validateToken()
    {
        if ($userToken = self::hasToken()) {

            if (self::findExistingToken($userToken)) {
                return true;
            }
        }
    }


    public static function updateToken($token, $user_id)
    {

        $db = new Database();
        $db->table = 'user_token';

        $data['token'] = $token;


        $id = $db->pluck('id')->where("user_id = $user_id");



        if ($db->update($data, $id)) {
            return true;
        } else {
            return false;
        }
    }




    private static function getKey()
    {
        return 'Phantom';
    }
}
