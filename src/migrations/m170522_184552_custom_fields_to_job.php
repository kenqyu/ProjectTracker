<?php

use yii\db\Migration;

class m170522_184552_custom_fields_to_job extends Migration
{
    public function safeUp()
    {
        $this->createTable('job_group', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()
        ]);

        $this->createTable('job_custom_fields', [
            'id' => $this->primaryKey(),
            'job_id' => $this->integer()->notNull(),
            'label' => $this->string()->notNull(),
            'type' => $this->smallInteger(1)->notNull(),
            'value' => $this->string(),
            'options' => $this->text()
        ]);

        $this->addColumn('job', 'department_id', $this->integer()->notNull());
        $this->addColumn('job', 'request_type_id', $this->integer()->notNull());
        $this->addColumn('job', 'job_group_id', $this->integer()->notNull());

        foreach (\app\models\Job::find()->all() as $item) {
            /** @var $item \app\models\Job */
            if (count($item->departments) > 0) {
                $item->department_id = collect($item->departments)->first()->id;
                $item->save(false, ['department_id']);
            }
        }
    }

    public function safeDown()
    {
        $this->dropColumn('job', 'job_group_id');
        $this->dropColumn('job', 'request_type_id');
        $this->dropColumn('job', 'department_id');

        $this->dropTable('job_custom_fields');
        $this->dropTable('job_group');
    }
}
