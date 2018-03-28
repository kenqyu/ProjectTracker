<?php

use yii\db\Migration;

class m170511_182409_request_type extends Migration
{
    public function safeUp()
    {
        $this->createTable('request_type', [
            'id' => $this->primaryKey(),
            'department_id' => $this->integer(),
            'name' => $this->string()->notNull()->unique(),
            'custom_form_id' => $this->integer(),
            'show_cwa' => $this->boolean(),
            'show_links' => $this->boolean(),
            'show_cost_center' => $this->boolean(),
            'show_mandate' => $this->boolean(),
            'show_translation_needed' => $this->boolean(),
            'sort' => $this->integer()->defaultValue(0)
        ]);

        $this->addForeignKey(
            'fk-request_type-department_id',
            'request_type',
            'department_id',
            'departments',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('request_type_default_cost_center', [
            'id' => $this->primaryKey(),
            'request_type_id' => $this->integer(),
            'cost_center_label' => $this->string(),
            'cost_center_percent' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-request_type_default_cost_center-request_type_id',
            'request_type_default_cost_center',
            'request_type_id',
            'request_type',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('request_type_default_cost_center');
        $this->dropTable('request_type');
    }
}
