<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transactions".
 *
 * @property int $id
 * @property int|null $by_user_id
 * @property int|null $to_user_id
 * @property int|null $type
 * @property float $amount
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $byUser
 * @property User $toUser
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['by_user_id', 'to_user_id', 'type'], 'integer'],
            [['amount', 'created_at', 'updated_at'], 'required'],
            [['amount'], 'number'],
            [['by_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['by_user_id' => 'id']],
            [['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['to_user_id' => 'id']],
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
            'to_user_id' => 'To User ID',
            'type' => 'Type',
            'amount' => 'Amount',
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
     * Gets query for [[ToUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(User::className(), ['id' => 'to_user_id']);
    }
}
