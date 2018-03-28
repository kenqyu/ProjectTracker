<?php

use yii\db\Migration;

class m160727_004608_job_files extends Migration
{
    public function up()
    {
        $this->createTable('job_file', [
            'id' => $this->primaryKey(),
            'job_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'file_name' => $this->string(),
            'title' => $this->string()->notNull(),
            'description' => $this->text(),
            'created_at' => $this->dateTime()->defaultExpression('NOW()')
        ]);

        $this->addForeignKey(
            'fk-job_file-job_id',
            'job_file',
            'job_id',
            'job',
            'id'
        );

        $this->addForeignKey(
            'fk-job_file-user_id',
            'job_file',
            'user_id',
            'user',
            'id'
        );
    }

    public function down()
    {
        $this->dropTable('job_file');
    }
}
