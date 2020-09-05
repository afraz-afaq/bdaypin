<?php

use yii\db\Migration;

/**
 * Class m200818_083720_change_datatype_of_dob_in_user_table
 */
class m200818_083720_change_datatype_of_dob_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('user','dob',$this->date());
        $this->alterColumn('user','phone_number',$this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('user','dob',$this->string());
        $this->alterColumn('user','phone_number',$this->integer());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200818_083720_change_datatype_of_dob_in_user_table cannot be reverted.\n";

        return false;
    }
    */
}
