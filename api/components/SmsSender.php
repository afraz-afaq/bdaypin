<?php
/**
 * Created by PhpStorm.
 * User: Afraz
 * Date: 28/01/2020
 * Time: 11:28 AM
 */

namespace api\components;


use yii\httpclient\Client;

class SmsSender
{

    
    public static function sendSms($number, $message)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('')
            ->setData([
                'Username' => '',
                'Password' => 'SMS_PASS',
                'From' => '',
                'To' => $number,
                'Message' => $message
            ])
            ->send();

        return $response->content;
    }

        /**
     * Generates four digit code for verification sms
     * @return int
     */
    public static function generateSixDigitCode()
    {
        return rand(100000, 999999);
    }

}