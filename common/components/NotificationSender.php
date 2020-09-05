<?php

namespace common\components;


use common\models\User;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;


class NotificationSender
{
    //Android Make Notification Types
    const DATA_NOTIFICATION = "data";
    const NOTIFICATION = "notification";



    //General Notification Types

    const NOTIFICATION_BIRTHDAY_TODAY = "1";
    const NOTIFICATION_BIRTHDAY_TOMORROW = "2";
    const NOTIFICATION_BIRTHDAY_THREE_DAYS = "3";



    /**
     * @param User $user
     * @param Array $data
     * @param integer $type
     * @return mixed
     */
    public static function sendAndroidNotification($user, $type, $data = null)
    {

        if (!$user->userDeviceToken)
            return;


        $target = $user->userDeviceToken->token;


        switch ($type) {


            case self::NOTIFICATION_BIRTHDAY_TODAY:
                return self::birthdayToday($target, $data);
                break;

            case self::NOTIFICATION_BIRTHDAY_TOMORROW:
                return self::birthdayTomorrow($target, $data);
                break;

            case self::NOTIFICATION_BIRTHDAY_THREE_DAYS:
                return self::birthdayThreeDays($target, $data);
                break;
        }
    }



    protected function birthdayToday($target, $data)
    {
        $friend = User::findOne($data['friend_id']);
        $name = $friend->userProfile->full_name;

        $not_data = [
            'notification_type' => self::NOTIFICATION_BIRTHDAY_TODAY,
            'title' => "Its $name's birthday today. Wish them by sending them a birthday pin.",    
        ] + $data;

        return self::sendAndroidPushNotification(
            self::makeAndroidNotification($not_data, self::DATA_NOTIFICATION),
            $target
        );
    }

    protected function birthdayTomorrow($target, $data)
    {
        $friend = User::findOne($data['friend_id']);
        $name = $friend->userProfile->full_name;

        $not_data = [
            'notification_type' => self::NOTIFICATION_BIRTHDAY_TOMORROW,
            'title' => "Its $name's birthday tomorrow",    
        ] + $data;

        return self::sendAndroidPushNotification(
            self::makeAndroidNotification($not_data, self::DATA_NOTIFICATION),
            $target
        );
    }

    protected function birthdayThreeDays($target, $data)
    {
        $friend = User::findOne($data['friend_id']);
        $name = $friend->userProfile->full_name;

        $not_data = [
            'notification_type' => self::NOTIFICATION_BIRTHDAY_THREE_DAYS,
            'title' => "$name's birthday is coming up in 3 days",    
        ] + $data;

        return self::sendAndroidPushNotification(
            self::makeAndroidNotification($not_data, self::DATA_NOTIFICATION),
            $target
        );
    }





    protected function makeAndroidNotification($data, $type)
    {
        return [
            'ttl' => '3600s',
            'priority' => 'normal',
            $type => $data
        ];
    }


    protected function sendAndroidPushNotification($notification, $target)
    {
        try {
            $token = $target;
            $messaging = FirebaseHelper::getFirebaseMessaging();
            $message = CloudMessage::withTarget('token', $token);
            $config = AndroidConfig::fromArray($notification);

            $message = $message->withAndroidConfig($config);
            return implode(',', $messaging->send($message));
        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            return "Target not available";
        } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
            return "The registration token is not a valid FCM registration token";
        }
    }


    // protected function saveNotification($user_id, $relation, $notification_type)
    // {

    //     $notification = new UserNotification();
    //     $notification->user_id = $user_id;
    //     $notification->relation_id = $relation;
    //     $notification->notification_type = $notification_type;
    //     $notification->read = 0;
    //     $notification->sent = 1;
    //     $notification->save();
    // }


    public static function toString($value)
    {
        return $value . "";
    }
}
