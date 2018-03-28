<?php

use yii\db\Migration;

class m170725_184456_job_relations_fixes extends Migration
{
    public function safeUp()
    {
        $this->dropForeignKey('fk-job_log-job_id', 'job_log');
        $this->addForeignKey(
            'fk-job_log-job_id',
            'job_log',
            'job_id',
            'job',
            'id',
            'CASCADE'
        );

        $this->dropForeignKey('fk-subscription-job_id', 'subscription');
        $this->addForeignKey(
            'fk-subscription-job_id',
            'subscription',
            'job_id',
            'job',
            'id',
            'CASCADE'
        );

        $this->dropForeignKey('fk-job_translation-job_id', 'job_translation');
        $this->addForeignKey(
            'fk-job_translation-job_id',
            'job_translation',
            'job_id',
            'job',
            'id',
            'CASCADE'
        );

        $this->dropForeignKey('fk-notifications-job_id', 'notifications');
        $this->addForeignKey(
            'fk-notifications-job_id',
            'notifications',
            'job_id',
            'job',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-job_log-job_id', 'job_log');
        $this->addForeignKey(
            'fk-job_log-job_id',
            'job_log',
            'job_id',
            'job',
            'id'
        );

        $this->dropForeignKey('fk-subscription-job_id', 'subscription');
        $this->addForeignKey(
            'fk-subscription-job_id',
            'subscription',
            'job_id',
            'job',
            'id'
        );

        $this->dropForeignKey('fk-job_translation-job_id', 'job_translation');
        $this->addForeignKey(
            'fk-job_translation-job_id',
            'job_translation',
            'job_id',
            'job',
            'id'
        );

        $this->dropForeignKey('fk-notifications-job_id', 'notifications');
        $this->addForeignKey(
            'fk-notifications-job_id',
            'notifications',
            'job_id',
            'job',
            'id'
        );
    }
}
