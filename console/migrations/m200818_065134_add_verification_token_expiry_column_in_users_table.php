<?php

use yii\db\Migration;

/**
 * Class m200818_065134_add_verification_token_expiry_column_in_users_table
 */
class m200818_065134_add_verification_token_expiry_column_in_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user','verification_token_expiry',$this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user','verification_token_expiry');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200818_065134_add_verification_token_expiry_column_in_users_table cannot be reverted.\n";

        return false;
    }
    */
}
