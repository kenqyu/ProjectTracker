<?php

use yii\db\Migration;

class m160727_004152_job_comments extends Migration
{
    public function up()
    {
        $this->createTable('job_comment', [
            'id' => $this->primaryKey(),
            'job_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'body' => $this->text(),
            'created_at' => $this->dateTime()->defaultExpression('NOW()')
        ]);

        $this->addForeignKey(
            'fk-job_comment-job_id',
            'job_comment',
            'job_id',
            'job',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('job_comment');
    }
}
