<?php
/**
 * Created by PhpStorm.
 * User: macmini2015
 * Date: 2019-10-14
 * Time: 18:45
 */

namespace api\components;


use Codeception\Util\HttpCode;
use yii\web\Request;

class RestResponses{

    public static function setSuccessResponse($data = array(), $message) {

        $response = array();
        $response['code'] = HttpCode::OK;
        $response['data'] = $data;
        $response['message'] = $message;

        return $response;
    }
}