<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transactions}}`.
 */
class m200816_154501_create_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transactions}}', [
            'id' => $this->primaryKey(),
            'by_user_id' => $this->integer(),
            'to_user_id' => $this->integer(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `by_user_id`
        $this->createIndex(
            '{{%idx-transactions-by_user_id}}',
            '{{%transactions}}',
            'by_user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-transactions-by_user_id}}',
            '{{%transactions}}',
            'by_user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );


        // creates index for column `to_user_id`
        $this->createIndex(
            '{{%idx-transactions-to_user_id}}',
            '{{%transactions}}',
            'to_user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-transactions-to_user_id}}',
            '{{%transactions}}',
            'to_user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('{{%fk-transactions-by_user_id}}', '{{%transactions}}');
        $this->dropIndex('{{%idx-transactions-by_user_id}}', '{{%transactions}}');

        $this->dropForeignKey('{{%fk-transactions-to_user_id}}', '{{%transactions}}');
        $this->dropIndex('{{%idx-transactions-to_user_id}}', '{{%transactions}}');

        $this->dropTable('{{%transactions}}');
    }
}
