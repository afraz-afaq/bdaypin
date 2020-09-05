<?php

namespace api\modules\v1\controllers;

use api\components\Helper;
use api\components\RestResponses;
use backend\models\Product;
use backend\models\UserFriend;
use Codeception\Util\HttpCode;
use common\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;


/**
 * Friend Controller API
 */

class ProductController extends ActiveController
{

    public $modelClass = Product::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }

    /**
     *@OA\Schema(
     *  schema="AddFriends",
     *
     * 
     *    @OA\Property(
     *     property="friends",
     *     type="String",
     *     description="[Friends as array of names and numbers",
     *      example="[{name:Afraz, number:03482269070},{name:Shan, number:03482269071},{name:Sheryl, number:03482269022}]"
     *  )
     *)
     */
    /**
     * @OA\Post(path="/friend/add",
     *   summary="Friends ADD",
     *   tags={"User, friend-add"},
     *
     *   @OA\RequestBody(
     *     description="data",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/AddFriends"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="success",
     *   ),
     * 
     *    security={
     *         {"bearerAuth":{}}
     *     },
     * )
     */
    public function actionAdd()
    {
        $friends = \Yii::$app->request->post('friends');
        $user = User::findOne(Helper::getUserIdFromHeader());

        $summary_array = [];
        if ($friends) {

            foreach($friends as $friend){
                $name = $friend['name'];
                $number = $friend['number'];
                if($mate = self::isFriendValid($number)){
                    if(UserFriend::find()->where(['user_id' => $user->id])->andWhere(['friend_id' => $mate->id])->exists())
                        $summary_array['already_friends'][] = ['name' => $name, 'number' => $number];
                    else{

                        $userFriend = new UserFriend();
                        $userFriend->user_id = $user->id;
                        $userFriend->friend_id = $mate->id;
                        $userFriend->save();
                        $summary_array['new_friends'][] = ['name' => $name, 'number' => $number];
                    }

                }
                else
                    $summary_array['unverified_or_not_exist'][] = ['name' => $name, 'number' => $number];

            }
            return RestResponses::setSuccessResponse($summary_array, "Friends Added Successfully");
        } else
            throw new \yii\web\HttpException(HttpCode::UNPROCESSABLE_ENTITY, "Friends required.");
    }


        /**
     * @OA\Get(path="/friend/upcoming-birthdays",
     *   summary="Friend Birthdays",
     *   tags={"FRIEND, Birthdays"},
     *
     *
     *   @OA\Response(
     *     response=200,
     *     description="success",
     *   ),
     *     security={
     *         {"bearerAuth":{}}
     *     },
     * )
     */

    public function actionUpcomingBirthdays()
    {
        $user = User::findOne(Helper::getUserIdFromHeader());

        $birthdays = User::find()
            ->where(['user.id' => $user->getUserFriends()->select('friend_id')->column()])
            ->andWhere("MONTH(dob) = MONTH(NOW()) AND user.id != $user->id AND dob >= NOW()")
            ->joinWith('userProfile')
            ->all();
        return RestResponses::setSuccessResponse($birthdays, "Upcoming Birthdays");
    }

           /**
     * @OA\Get(path="/friend/friend-list",
     *   summary="Friend List",
     *   tags={"FRIEND, list"},
     *
     *
     *   @OA\Response(
     *     response=200,
     *     description="success",
     *   ),
     *     security={
     *         {"bearerAuth":{}}
     *     },
     * )
     */

    public function actionFriendList(){

        $user = User::findOne(Helper::getUserIdFromHeader());
        $friends = UserFriend::find()
                    ->where(['user_friends.user_id' => $user->id])
                    ->with('friend.userProfile')
                    ->all();

        return RestResponses::setSuccessResponse($friends, "User Friends");

    }



        /**
     *@OA\Schema(
     *  schema="FriendDetails",
     *
     * 
     *    @OA\Property(
     *     property="friend_id",
     *     type="String",
     *     description="Id of the friend",
     *      example="9"
     *  )
     *)
     */

    /**
     * @OA\Post(path="/friend/friend-details",
     *   summary="Friends Details",
     *   tags={"User, friend-Details"},
     *
     *   @OA\RequestBody(
     *     description="data",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/FriendDetails"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="success",
     *   ),
     * 
     *    security={
     *         {"bearerAuth":{}}
     *     },
     * )
     */
    public function actionFriendDetails(){

        $user = User::findOne(Helper::getUserIdFromHeader());
        $friend_id = \Yii::$app->request->post('friend_id');

        $flag = UserFriend::find()
                    ->where(['user_id' => $user->id])
                    ->andWhere(['friend_id' => $friend_id])
                    ->exists();

        if(!$flag)
            throw new \yii\web\HttpException(HttpCode::UNPROCESSABLE_ENTITY, "This user is not a friend.");

        $friend = User::find()
                    ->where(['user.id' => $friend_id])
                    ->joinWith('userProfile')
                    ->joinWith('userTransactions')
                    ->all();

        return RestResponses::setSuccessResponse($friend, "User Friends");

    }


    





    public static function isFriendValid($number){
        $users = User::find()
                     ->where(['status' => User::STATUS_ACTIVE])
                     ->all();

        foreach($users as $user){
            if(self::isEqualPhoneNumber($number,$user->phone_number))
                return $user;
        }
        return false;
    }


    public static function isEqualPhoneNumber($phoneA, $phoneB, $substringMinLength = 7)
{
    if ($phoneA == $phoneB) {
        return true;
    }

    // remove "0", "+" from the beginning of the numbers
    if ($phoneA[0] == '0' || $phoneB[0] == '0' ||
            $phoneA[0] == '+' || $phoneB[0] == '+') {
        return self::isEqualPhoneNumber(ltrim($phoneA, '0+'), ltrim($phoneB, '0+'));
    }

    // change numbers if second is longer
    if (strlen($phoneA) < strlen($phoneB)) {
        return self::isEqualPhoneNumber($phoneB, $phoneA);
    }

    if (strlen($phoneB) < $substringMinLength) {
        return false;
    }

    // is second number a first number ending
    $position = strrpos($phoneA, $phoneB);
    if ($position !== false && ($position + strlen($phoneB) === strlen($phoneA))) {
        return true;
    }

    return false;
}
    



    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param \yii\base\Model $model the model to be accessed. If `null`, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // check if the user can access $action and $model
        // throw ForbiddenHttpException if access should be denied
    }
}
