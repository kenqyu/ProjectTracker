<?php

use yii\db\Migration;

class m160727_181757_job_log extends Migration
{
    public function up()
    {
        $this->createTable('job_log', [
            'id' => $this->primaryKey(),
            'job_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('NOW()'),
            'after' => $this->text()->notNull()
        ]);

        $this->addForeignKey(
            'fk-job_log-job_id',
            'job_log',
            'job_id',
            'job',
            'id'
        );

        $this->addForeignKey(
            'fk-job_log-user_id',
            'job_log',
            'user_id',
            'user',
            'id'
        );
    }

    public function down()
    {
        $this->dropTable('job_log');
    }
}
