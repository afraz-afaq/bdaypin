<?php

/**
 * Created by PhpStorm.
 * User: macmini2015
 * Date: 11/12/18
 * Time: 5:14 PM
 */

namespace console\controllers;

use backend\models\UserFriend;
use common\components\NotificationSender;
use common\models\User;
use yii\console\Controller;
use Yii;


class CronController extends Controller
{
    public function actionIndex()
    {

        echo "testing";
    }

    public function actionNotifyBirthdays()
    {

        $birthdayTodayUsers = User::find()
            ->where("dob = CURRENT_DATE()")
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->all();

        foreach ($birthdayTodayUsers as $birthday_person) {
            $friends = UserFriend::find()
                ->joinWith('user')
                ->where(['friend_id' => $birthday_person->id])
                ->andWhere(['status' => User::STATUS_ACTIVE])
                ->all();

            foreach ($friends as $friend) {
                $birthdayUser = $friend->friend;
                NotificationSender::sendAndroidNotification(
                    $friend->user,
                    NotificationSender::NOTIFICATION_BIRTHDAY_TODAY,
                    [
                        'friend_id' => NotificationSender::toString($birthdayUser->id),
                        'profile_pic' => $birthdayUser->userProfile->profile_pic
                    ]
                );
            }
        }
    }




    public function actionNotifyBirthdaysTomorrow()
    {

        $birthdayTomorrowUsers = User::find()
            ->where("dob - CURRENT_DATE = 1")
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->all();
    

        foreach ($birthdayTomorrowUsers as $birthday_person) {
            $friends = UserFriend::find()
                ->joinWith('user')
                ->where(['friend_id' => $birthday_person->id])
                ->andWhere(['status' => User::STATUS_ACTIVE])
                ->all();

            foreach ($friends as $friend) {
                $birthdayUser = $friend->friend;
                NotificationSender::sendAndroidNotification(
                    $friend->user,
                    NotificationSender::NOTIFICATION_BIRTHDAY_TOMORROW,
                    [
                        'friend_id' => NotificationSender::toString($birthdayUser->id),
                        'profile_pic' => $birthdayUser->userProfile->profile_pic
                    ]
                );
            }
        }
    }



    public function actionNotifyBirthdaysThree()
    {

        $birthdayThreeDaysUsers = User::find()
            ->where("dob - CURRENT_DATE = 3")
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->all();
        

        foreach ($birthdayThreeDaysUsers as $birthday_person) {
            $friends = UserFriend::find()
                ->joinWith('user')
                ->where(['friend_id' => $birthday_person->id])
                ->andWhere(['status' => User::STATUS_ACTIVE])
                ->all();

            foreach ($friends as $friend) {
                $birthdayUser = $friend->friend;
                NotificationSender::sendAndroidNotification(
                    $friend->user,
                    NotificationSender::NOTIFICATION_BIRTHDAY_THREE_DAYS,
                    [
                        'friend_id' => NotificationSender::toString($birthdayUser->id),
                        'profile_pic' => $birthdayUser->userProfile->profile_pic
                    ]
                );
            }
        }
    }
}
