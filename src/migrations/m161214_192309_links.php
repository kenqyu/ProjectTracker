<?php

use yii\db\Migration;

class m161214_192309_links extends Migration
{
    public function safeUp()
    {
        $this->addColumn('job', 'page_update', $this->boolean()->defaultValue(false));

        $this->createTable('job_link', [
            'id' => $this->primaryKey(),
            'job_id' => $this->integer()->notNull(),
            'link' => $this->string(1024)->notNull()
        ]);

        $this->addForeignKey(
            'fk-job_link-job_id',
            'job_link',
            'job_id',
            'job',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('job_link');
        $this->dropColumn('job', 'page_update');
    }
}
