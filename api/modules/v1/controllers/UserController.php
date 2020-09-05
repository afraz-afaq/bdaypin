<?php

namespace api\modules\v1\controllers;

use api\components\Helper;
use api\components\RestResponses;
use backend\models\UserAccessToken;
use backend\models\UserDeviceToken;
use backend\models\UserFriend;
use backend\models\UserProfile;
use Codeception\Util\HttpCode;
use common\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

/**
 * User Controller API
 */

/**
 * @OA\Info(
 *   version="1.0",
 *   title="Birthday Pin",
 *   description="Birthday Pin - Mobile app API",
 *   @OA\Contact(
 *     name="Afraz Afaq",
 *     email="afrazafaq96@gmail.com",
 *   ),
 * ),
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * )
 * @OA\Server(
 *   url="http://localhost:8888/bday-pin/api/web/v1/",
 *   description="local dev server",
 * )
 * @OA\Server(
 *   url="http://localhost/bday-pin/api/web/v1/",
 *   description="local dev server afraz",
 * )
 * @OA\Server(
 *   url="https://development-afraz.000webhostapp.com/bday-pin/api/web/v1/",
 *   description="dev server",
 * )
 */

class UserController extends ActiveController
{

    public $modelClass = User::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['login', 'account-data', 'send-code', 'verify-code']
        ];
        return $behaviors;
    }

    /**
     *@OA\Schema(
     *  schema="UserLogin",
     *
     *  @OA\Property(
     *     property="email",
     *     type="String",
     *     description="User Login",
     *      example="abc@gmail.com"
     *  ),
     * 
     *  @OA\Property(
     *     property="is_fb",
     *     type="String",
     *     description="0 for facebook and 1 for instagram",
     *      example="0"
     *  ),
     * 
     * 
     *  @OA\Property(
     *     property="device_token",
     *     type="String",
     *     description="Firebase Device Token",
     *      example="osadf8dsofsf68df687"
     *  )
     *)
     */
    /**
     * @OA\Post(path="/user/login",
     *   summary="User Login",
     *   tags={"User, user-login"},
     *
     *   @OA\RequestBody(
     *     description="data",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/UserLogin"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="success",
     *   ),
     * )
     */
    public function actionLogin()
    {
        $email = \Yii::$app->request->post('email');
        $device_token = \Yii::$app->request->post('device_token');
        $is_fb = \Yii::$app->request->post('is_fb');

        $is_fb = $is_fb == null ? 0 : $is_fb;

        if ($email && $device_token) {
            $user = User::find()->with('userProfile')->where(['email' => $email])->one();
            if ($user) {
                if ($user->status == User::STATUS_ACTIVE) {
                    $user->userAccessToken->generateNewToken();
                    $user->accessToken = $user->userAccessToken;
           
                    if($user->userDeviceToken)
                        $userDeviceToken = $user->userDeviceToken;
                    else
                        $userDeviceToken = new UserDeviceToken();

                    $userDeviceToken->user_id = $user->id;
                    $userDeviceToken->token = $device_token;
                    $userDeviceToken->save();
                
                    return RestResponses::setSuccessResponse($user, "User already exist and is verified.");
                } else
                    return RestResponses::setSuccessResponse($user, "User already exist and is unverified.");
            }

            $user = new User();
            $user->email = $email;
            $user->password = Yii::$app->security->generatePasswordHash(Yii::$app->security->generateRandomString());
            $user->is_fb = $is_fb;
            $user->status = User::STATUS_ACTIVE;

            if ($user->save()) {

                $userProfile = new UserProfile();
                $userProfile->user_id = $user->id;
                $userProfile->save();

                if($device_token){
                    $userDeviceToken = new UserDeviceToken();
                    $userDeviceToken->token = $device_token;
                    $userDeviceToken->user_id = $user->id;
                    $userDeviceToken->save();
                }

                $userAccessToken = new UserAccessToken();
                $userAccessToken->user_id = $user->id;
                $userAccessToken->generateNewToken();
                $user->accessToken = $userAccessToken;

                return RestResponses::setSuccessResponse($user, "User created successfully.");
            } else {
                $message = Helper::processModelError($user->getErrorSummary(true), "Unable to create user.");
                throw new \yii\web\HttpException(HttpCode::UNPROCESSABLE_ENTITY, $message);
            }
        } else
            throw new \yii\web\HttpException(HttpCode::UNPROCESSABLE_ENTITY, "Email And Device Token is required.");
    }




    /**
     *@OA\Schema(
     *  schema="AccountData",
     *
     *  @OA\Property(
     *     property="dob",
     *     type="String",
     *     description="Y-m-d Date of birth",
     *      example="2020-06-04"
     *  ),
     * 
     *    @OA\Property(
     *     property="number",
     *     type="String",
     *     description="Phone number of the user.",
     *      example="03462234543"
     *  ),
     * 
     *  @OA\Property(
     *     property="full_name",
     *     type="String",
     *     description="Full name of the user",
     *      example="Shane O' Mac"
     *  ),
     * 
     *   @OA\Property(
     *     property="user_id",
     *     type="Integer",
     *     description="Datebase id of the user.",
     *      example="8"
     *  ),
     * 
     *  @OA\Property(
     *     property="profile_pic",
     *     type="string",
     *     description="Profile pic of the user",
     *      example="https://qph.fs.quoracdn.net/main-qimg-613fe4621d20779fbecdb0546549cd19"
     *  )
     *)
     */
    /**
     * @OA\Post(path="/user/account-data",
     *   summary="User Account Data",
     *   tags={"User, save-data"},
     *
     *   @OA\RequestBody(
     *     description="data",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/AccountData"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="success",
     *   ),
     * )
     */
    public function actionAccountData()
    {
        $dob = \Yii::$app->request->post('dob');
        $number = \Yii::$app->request->post('number');
        $name = \Yii::$app->request->post('full_name');
        $id = \Yii::$app->request->post('user_id');
        $profile_pic = \Yii::$app->request->post('profile_pic');

        if ($dob && $number && $id && $name) {

            if (User::find()->where(['phone_number' => $number])->exists())
                throw new \yii\web\HttpException(HttpCode::UNPROCESSABLE_ENTITY, "Number already exists.");

            $user = User::findOne($id);

            $userProfile = $user->userProfile;
            if($userProfile)
            {
                $userProfile->full_name = $name;
                if($profile_pic)
                    $userProfile->profile_pic = $profile_pic;
                $userProfile->save();
            }
            else{
                $userProfile = new UserProfile();
                $userProfile->user_id = $user->id;
                $userProfile->full_name = $name;
                if($profile_pic)
                        $userProfile->profile_pic = $profile_pic;

                if(!$userProfile->save()){
                    $message = Helper::processModelError($userProfile->getErrorSummary(true), "Unable to create user.");
                    throw new \yii\web\HttpException(HttpCode::UNPROCESSABLE_ENTITY, $message);
                }
            }

            $user->dob = date('Y-m-d',strtotime($dob));
            $user->phone_number = $number;
            if ($user->save()) {
                $user = User::find()->with('userProfile')->where(['id' => $user->id])->one();
                $user->accessToken = $user->userAccessToken;
                return RestResponses::setSuccessResponse($user, "Account Data Saved.");
            } else {
                $message = Helper::processModelError($user->getErrorSummary(true), "Unable to create user.");
                throw new \yii\web\HttpException(HttpCode::UNPROCESSABLE_ENTITY, $message);
            }
        } else
            throw new \yii\web\HttpException(HttpCode::UNPROCESSABLE_ENTITY, "Required Data Missing.");
    }



    /**
     *@OA\Schema(
     *  schema="SendCode",
     *
     * 
     *    @OA\Property(
     *     property="number",
     *     type="String",
     *     description="Phone number of the user.",
     *      example="0013434324234"
     *  ),
     * 
     *)
     */
    /**
     * @OA\Post(path="/user/code",
     *   summary="Send Verification Code",
     *   tags={"User, sms-code"},
     *
     *   @OA\RequestBody(
     *     description="data",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/SendCode"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="success",
     *   ),
     * )
     */

    public function actionSendCode()
    {
        $number = \Yii::$app->request->post('number');

        $user = User::find()->where(['phone_number' => $number])->one();
        if (!$user)
            throw new \yii\web\HttpException(HttpCode::UNPROCESSABLE_ENTITY, "Number does not exists.");

        $six_digit_code = 666666;
        $expiry = strtotime("+3 minutes");

        $user->verification_token = $six_digit_code;
        $user->verification_token_expiry = $expiry;
        if ($user->save())
            return RestResponses::setSuccessResponse($user, "Token Sent Successfully.");
        else {
            $message = Helper::processModelError($user->getErrorSummary(true), "Unable to create user.");
            throw new \yii\web\HttpException(HttpCode::UNPROCESSABLE_ENTITY, $message);
        }
        /*
            $six_digit_code = SmsSender::generateSixDigitCode();
            $result = SmsSender::sendSms($number, '<#> Your six digit verification code is: ' . $six_digit_code . ' ' .'1234');
            if ($result == 'Message Sent Successfully!') {
                $this->saveVerificationCode($number, $six_digit_code);
                return RestResponses::setSuccessResponse(null, "Code sent successfully");
            } else
                if (strpos($result, ':') !== false) {
                $result_array = explode(": ", $result);
                if (sizeof($result_array) > 1) {
                    throw new \yii\web\HttpException(HttpCode::EXPECTATION_FAILED, $result_array[1]);
                }
                throw new \yii\web\HttpException(HttpCode::EXPECTATION_FAILED, $result);
            }
            throw new \yii\web\HttpException(HttpCode::EXPECTATION_FAILED, $result);
        */
    }



    /**
     *@OA\Schema(
     *  schema="VerfifyCode",
     *
     * 
     *    @OA\Property(
     *     property="number",
     *     type="String",
     *     description="Phone number of the user.",
     *      example="0013434324234"
     *  ),
     * 
     *   @OA\Property(
     *     property="code",
     *     type="String",
     *     description="User verification code",
     *      example="666666"
     *  ),
     * 
     *)
     */
    /**
     * @OA\Post(path="/user/verify-code",
     *   summary="Verification of Code",
     *   tags={"User, code-verification"},
     *
     *   @OA\RequestBody(
     *     description="data",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/VerfifyCode"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="success",
     *   ),
     * )
     */

    public function actionVerifyCode()
    {
        $number = \Yii::$app->request->post('number');
        $code = \Yii::$app->request->post('code');
        $user = User::find()->where(['phone_number' => $number])->one();
        if (!$user) {
            throw new \yii\web\HttpException(HttpCode::EXPECTATION_FAILED, "Number does not exists.");
        }

        if ($user->verification_token == $code) {
            $time_diff = $user->verification_token_expiry - strtotime("+0 minutes");
            if ($time_diff <= 0) {
                throw new \yii\web\HttpException(HttpCode::EXPECTATION_FAILED, "Verification Code Expired.");
            }

            $user->status = User::STATUS_ACTIVE;
            $user->save();
            $user->userAccessToken->generateNewToken();
            $user->accessToken = $user->userAccessToken;
            return RestResponses::setSuccessResponse($user, "User Verified.");
        } else
            throw new \yii\web\HttpException(HttpCode::EXPECTATION_FAILED, "Verification Code Mismatch.");
    }

    /**
     * @OA\Get(path="/user/{id}",
     *   summary="User Profile",
     *   tags={"USER, 'userProfile', 'userAccessToken', 'userFriends', 'userTransactions', 'userNotifications'"},
     *     
     *    @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     * 
     *   @OA\Parameter(
     *     name="expand",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
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
    // public function actionProfile()
    // {

    //     $query = User::find()->where(['id' => Helper::getUserIdFromHeader()]);
    //     return new ActiveDataProvider([
    //         'query' => $query,
    //     ]);
    // }



    public function actionDeleteUser(){
        $id = \Yii::$app->request->get('user_id');
        $user = User::findOne($id);
        if($user){
            UserProfile::deleteAll(['user_id' => $id]);
            UserAccessToken::deleteAll(['user_id' => $id]);
            UserDeviceToken::deleteAll(['user_id' => $id]);
            UserFriend::deleteAll(['user_id' => $id]);
            UserFriend::deleteAll(['friend_id' => $id]);

            if($user->delete())
                return "User Deleted Successfully";
        }
        else
            return "User not found.";
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
