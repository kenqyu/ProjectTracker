<?php

use yii\db\Migration;

class m160727_012330_job_translations extends Migration
{
    public function up()
    {
        $this->createTable('job_translation', [
            'id' => $this->primaryKey(),
            'job_id' => $this->integer()->notNull(),
            'rush' => $this->boolean()->defaultValue(false)->notNull(),
            'language' => $this->integer()->notNull(),
            'due_date' => $this->dateTime()->notNull(),
            'status' => $this->smallInteger(1)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('NOW()'),
            'updated_at' => $this->dateTime()->defaultExpression('NOW()') . ' ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->addForeignKey(
            'fk-job_translation-job_id',
            'job_translation',
            'job_id',
            'job',
            'id'
        );
    }

    public function down()
    {
        $this->dropTable('job_translation');
    }
}
