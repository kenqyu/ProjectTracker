<?php

use yii\db\Migration;

class m170817_230556_user_department_rework extends Migration
{
    public function safeUp()
    {
        $this->renameTable('user_department', 'user_processing_unit');
        $this->renameColumn('user_processing_unit', 'department_id', 'processing_unit_id');

        $this->createTable('processing_unit', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()
        ]);

        $this->execute('INSERT INTO `processing_unit` SELECT `id`, `name` FROM `departments`');

        $this->dropForeignKey('fk-user_department-department_id', 'user_processing_unit');
        $this->dropForeignKey('fk-user_department-user_id', 'user_processing_unit');

        $this->addForeignKey(
            'fk-user_processing_unit-user_id',
            'user_processing_unit',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_processing_unit-processing_unit_id',
            'user_processing_unit',
            'processing_unit_id',
            'processing_unit',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->renameColumn('job', 'department_id', 'processing_unit_id');
        $this->renameColumn('request_type', 'department_id', 'processing_unit_id');

        $this->dropForeignKey('fk-request_type-department_id', 'request_type');
        $this->addForeignKey(
            'fk-request_type-processing_unit_id',
            'request_type',
            'processing_unit_id',
            'processing_unit',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
    }
}
