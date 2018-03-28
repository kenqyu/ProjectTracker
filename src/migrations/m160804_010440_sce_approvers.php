<?php

use yii\db\Migration;

class m160804_010440_sce_approvers extends Migration
{
    public function up()
    {
        $this->createTable('sce_approvers', [
            'job_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'PRIMARY KEY(job_id, user_id)'
        ]);

        $this->addForeignKey(
            'fk-sce_approvers-job_id',
            'sce_approvers',
            'job_id',
            'job',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-sce_approvers-user_id',
            'sce_approvers',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('sce_approvers');
    }
}
