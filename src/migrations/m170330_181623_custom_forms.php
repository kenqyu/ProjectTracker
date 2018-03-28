<?php

use yii\db\Migration;

class m170330_181623_custom_forms extends Migration
{
    public function safeUp()
    {
        $this->createTable('custom_form', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique()
        ]);

        $this->createTable('custom_form_field', [
            'id' => $this->primaryKey(),
            'form_id' => $this->integer()->notNull(),
            'label' => $this->string()->notNull(),
            'hint' => $this->text(),
            'type' => $this->smallInteger(1)->notNull(),
            'required' => $this->boolean()->defaultValue(false),
            'default' => $this->string(),
            'options' => $this->text(),
            'sort' => $this->integer()->defaultValue(0)
        ]);

        $this->addForeignKey(
            'fk-custom_form_field-form_id',
            'custom_form_field',
            'form_id',
            'custom_form',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addColumn('work_type', 'custom_form_id', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('work_type', 'custom_form_id');
        $this->dropTable('custom_form_field');
        $this->dropTable('custom_form');
    }
}
