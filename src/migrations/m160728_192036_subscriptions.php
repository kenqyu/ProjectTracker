<?php

use yii\db\Migration;

class m160728_192036_subscriptions extends Migration
{
    public function up()
    {
        $this->createTable('subscription', [
            'user_id' => $this->integer()->notNull(),
            'job_id' => $this->integer()->notNull(),
            'PRIMARY KEY(user_id, job_id)'
        ]);

        $this->addForeignKey(
            'fk-subscription-user_id',
            'subscription',
            'user_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'fk-subscription-job_id',
            'subscription',
            'job_id',
            'job',
            'id'
        );
    }

    public function down()
    {
        $this->dropTable('subscription');
    }
}
