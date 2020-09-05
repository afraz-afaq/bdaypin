<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_notifications}}`.
 */
class m200816_160643_create_user_notifications_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_notifications}}', [
            'id' => $this->primaryKey(),
            'by_user_id' => $this->integer(),
            'for_user_id' => $this->integer(),
            'type' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);


                // creates index for column `by_user_id`
                $this->createIndex(
                    '{{%idx-user_notifications-by_user_id}}',
                    '{{%user_notifications}}',
                    'by_user_id'
                );
        
                // add foreign key for table `{{%user}}`
                $this->addForeignKey(
                    '{{%fk-user_notifications-by_user_id}}',
                    '{{%user_notifications}}',
                    'by_user_id',
                    '{{%user}}',
                    'id',
                    'CASCADE',
                    'CASCADE'
                );
        
        
                // creates index for column `for_user_id`
                $this->createIndex(
                    '{{%idx-user_notifications-for_user_id}}',
                    '{{%user_notifications}}',
                    'for_user_id'
                );
        
                // add foreign key for table `{{%user}}`
                $this->addForeignKey(
                    '{{%fk-user_notifications-for_user_id}}',
                    '{{%user_notifications}}',
                    'for_user_id',
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

        $this->dropForeignKey('{{%fk-user_notifications-by_user_id}}', '{{%user_notifications}}');
        $this->dropIndex('{{%idx-user_notifications-by_user_id}}', '{{%user_notifications}}');

        $this->dropForeignKey('{{%fk-user_notifications-for_user_id}}', '{{%user_notifications}}');
        $this->dropIndex('{{%idx-user_notifications-for_user_id}}', '{{%user_notifications}}');

        $this->dropTable('{{%user_notifications}}');
    }
}
