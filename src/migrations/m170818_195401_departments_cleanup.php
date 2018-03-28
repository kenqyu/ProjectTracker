<?php

use yii\db\Migration;

class m170818_195401_departments_cleanup extends Migration
{
    public function safeUp()
    {
        $this->dropTable('job_department');
        $this->truncateTable('sub_department');
        foreach (\app\models\Departments::find()->all() as $item) {
            $item->delete();
        }
    }

    public function safeDown()
    {
        $this->createTable('job_department', [
            'job_id' => $this->integer()->notNull(),
            'department_id' => $this->integer()->notNull(),
            'PRIMARY KEY(job_id, department_id)'
        ]);
    }
}
