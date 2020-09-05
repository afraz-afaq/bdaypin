<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_friends}}`.
 */
class m200816_155041_create_user_friends_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_friends}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'friend_id' => $this->integer(),
            'is_notification_enabled' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);


        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-user_friends-user_id}}',
            '{{%user_friends}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_friends-user_id}}',
            '{{%user_friends}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );


        // creates index for column `friend_id`
        $this->createIndex(
            '{{%idx-user_friends-friend_id}}',
            '{{%user_friends}}',
            'friend_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_friends-friend_id}}',
            '{{%user_friends}}',
            'friend_id',
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

        $this->dropForeignKey('{{%fk-user_friends-user_id}}', '{{%user_friends}}');
        $this->dropIndex('{{%idx-user_friends-user_id}}', '{{%user_friends}}');

        $this->dropForeignKey('{{%fk-user_friends-friend_id}}', '{{%user_friends}}');
        $this->dropIndex('{{%idx-user_friends-friend_id}}', '{{%user_friends}}');

        $this->dropTable('{{%user_friends}}');
    }
}
