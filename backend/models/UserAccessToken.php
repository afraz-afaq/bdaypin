<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_access_token".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $token
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class UserAccessToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_access_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id',  'created_at', 'updated_at'], 'integer'],
            [['token'], 'required'],
            [['token'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'token' => 'Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

        /**
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->token = Yii::$app->security->generateRandomString();
    }

    public function generateNewToken(){
        $this->generateAuthKey();
        if($this->save()){
            return true;
        }else{
            return false;
;
        }
    }
}
