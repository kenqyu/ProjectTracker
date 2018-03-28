<?php

use yii\db\Migration;

class m170804_173435_new_user_fields extends Migration
{
    public function safeUp()
    {
        $this->createTable('organization_unit', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()
        ]);

        $this->addColumn('user', 'phone', $this->string()->notNull());
        $this->addColumn('user', 'organization_unit_id', $this->integer());
        $this->addColumn('user', 'organization_unit_other', $this->string());

        $this->addColumn('departments', 'organization_unit_id', $this->integer());

        $this->createTable('sub_department', [
            'id' => $this->primaryKey(),
            'department_id' => $this->integer(),
            'name' => $this->string()
        ]);

        $this->addForeignKey(
            'fk-sub_department-department_id',
            'sub_department',
            'department_id',
            'departments',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addColumn('user', 'department_id', $this->integer());
        $this->addColumn('user', 'sub_department_id', $this->integer());
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-sub_department-department_id', 'sub_department');

        $this->dropColumn('user', 'sub_department_id');
        $this->dropColumn('user', 'department_id');
        $this->dropColumn('user', 'organization_unit_id');
        $this->dropColumn('user', 'organization_unit_other');
        $this->dropColumn('user', 'phone');

        $this->dropColumn('departments', 'organization_unit_id');

        $this->dropTable('sub_department');
        $this->dropTable('organization_unit');
    }
}
