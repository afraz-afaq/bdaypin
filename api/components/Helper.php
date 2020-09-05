<?php

/**
 * Created by PhpStorm.
 * User: macmini2015
 * Date: 2019-10-14
 * Time: 19:07
 */

namespace api\components;



use common\models\User;


class Helper
{


    public static function processModelError(array $errors, $default_message = "")
    {

        if (sizeof($errors) > 0) {
            $message = implode(', ', $errors);
        } else
            $message = $default_message;

        return $message;
    }



    public static function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public static function getUserIdFromHeader()
    {
        $access_token = '';
        $headers = Helper::getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $access_token = $matches[1];
            }
        }

        $user_id = User::findIdentityByAccessToken($access_token)->id;
        return $user_id;
    }
}
