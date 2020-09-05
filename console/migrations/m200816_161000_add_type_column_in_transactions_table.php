<?php

use yii\db\Migration;

/**
 * Class m200816_161000_add_type_column_in_transactions_table
 */
class m200816_161000_add_type_column_in_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('transactions','type',$this->integer()->after('to_user_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('transactions','type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200816_161000_add_type_column_in_transactions_table cannot be reverted.\n";

        return false;
    }
    */
}
