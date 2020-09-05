<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_access_token}}`.
 */
class m200816_153127_create_user_access_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_access_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'token' => $this->string(255)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-user_access_token-user_id}}',
            '{{%user_access_token}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_access_token-user_id}}',
            '{{%user_access_token}}',
            'user_id',
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

        $this->dropForeignKey('{{%fk-user_access_token-user_id}}','{{%user_access_token}}');
        $this->dropIndex('{{%idx-user_access_token-user_id}}','{{%user_access_token}}');
        $this->dropTable('{{%user_access_token}}');
    }
}
