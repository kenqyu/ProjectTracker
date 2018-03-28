<?php

use yii\db\Migration;

class m170602_165233_user_department_connection extends Migration
{
    public function safeUp()
    {
        $this->createTable('user_department', [
            'user_id' => $this->integer(),
            'department_id' => $this->integer(),
            'PRIMARY KEY(user_id, department_id)'
        ]);

        $this->addForeignKey(
            'fk-user_department-user_id',
            'user_department',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_department-department_id',
            'user_department',
            'department_id',
            'departments',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('user_department');
    }
}
