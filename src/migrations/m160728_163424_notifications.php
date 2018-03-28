<?php

use yii\db\Migration;

class m160728_163424_notifications extends Migration
{
    public function up()
    {
        $this->createTable('notifications', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'date' => $this->dateTime()->defaultExpression('NOW()'),
            'job_id' => $this->integer()->notNull(),
            'message' => $this->text()->notNull(),
            'read' => $this->boolean()->defaultValue(false),
            'sent' => $this->boolean()->defaultValue(false)
        ]);

        $this->addForeignKey(
            'fk-notifications-user_id',
            'notifications',
            'user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'fk-notifications-job_id',
            'notifications',
            'job_id',
            'job',
            'id'
        );

        $this->createIndex(
            'idx_notifications_date_user_id_read_sent',
            'notifications',
            ['date', 'user_id', 'read', 'sent']
        );
        $this->createIndex('idx_notifications_date_user_id_read', 'notifications', ['date', 'user_id', 'read']);
        $this->createIndex('idx_notifications_date_user_id', 'notifications', ['date', 'user_id']);
        $this->createIndex('idx_notifications_date', 'notifications', ['date']);
    }

    public function down()
    {
        $this->dropTable('notifications');
    }
}
