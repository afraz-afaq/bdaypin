<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string $name
 * @property string $image_url
 * @property float $price
 * @property int $category
 * @property string|null $color
 * @property int|null $is_available
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'image_url', 'price', 'category'], 'required'],
            [['price'], 'number'],
            [['category', 'is_available', 'created_at', 'updated_at'], 'integer'],
            [['name', 'image_url', 'color'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'image_url' => 'Image Url',
            'price' => 'Price',
            'category' => 'Category',
            'color' => 'Color',
            'is_available' => 'Is Available',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
