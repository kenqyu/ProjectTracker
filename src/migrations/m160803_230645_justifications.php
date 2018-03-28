<?php

use yii\db\Migration;

class m160803_230645_justifications extends Migration
{
    public function up()
    {
        $this->createTable('justifications', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique()
        ]);

        $this->createTable('job_justification', [
            'job_id' => $this->integer()->notNull(),
            'justification_id' => $this->integer()->notNull(),
            'PRIMARY KEY(job_id, justification_id)'
        ]);

        $this->addForeignKey(
            'fk-job_justification-job_id',
            'job_justification',
            'job_id',
            'job',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-job_justification-justification_id',
            'job_justification',
            'justification_id',
            'justifications',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('job_justification');
        $this->dropTable('justifications');
    }
}
