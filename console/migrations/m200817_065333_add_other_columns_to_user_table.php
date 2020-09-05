<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m200817_065333_add_other_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        $this->addColumn('user','dob',$this->string()->after('status'));
        $this->addColumn('user','is_fb',$this->integer()->after('dob'));
        $this->addColumn('user','phone_number',$this->integer()->after('is_fb'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user','dob');
        $this->dropColumn('user','is_fb');
        $this->dropColumn('user','phone_number');
    }
}
