<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "user_notifications".
 *
 * @property int $id
 * @property int|null $by_user_id
 * @property int|null $for_user_id
 * @property int|null $type
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $byUser
 * @property User $forUser
 */
class UserNotification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_notifications';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['by_user_id', 'for_user_id', 'type'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['by_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['by_user_id' => 'id']],
            [['for_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['for_user_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'by_user_id' => 'By User ID',
            'for_user_id' => 'For User ID',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[ByUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getByUser()
    {
        return $this->hasOne(User::className(), ['id' => 'by_user_id']);
    }

    /**
     * Gets query for [[ForUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForUser()
    {
        return $this->hasOne(User::className(), ['id' => 'for_user_id']);
    }
}
