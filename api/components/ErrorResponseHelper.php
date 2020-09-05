<?php


namespace api\components;

use yii\base\Event;

class ErrorResponseHelper
{
    public static function beforeResponseSend(Event $event)
    {
        /**
         * @var \yii\web\Response $response
         */
        $response = $event->sender;
        if (isset($response->data['status']) && $response->data['status'] == 401) {
            $response->data['message'] = "Your session on this devices has expired. Please re login to continue using app.";
        }
    }
}
