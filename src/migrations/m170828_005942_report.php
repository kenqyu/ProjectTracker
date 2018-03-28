<?php

use yii\db\Migration;

class m170828_005942_report extends Migration
{
    public function safeUp()
    {
        $this->createTable('report', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'content' => $this->text()->notNull(),

            'owner_id' => $this->integer()->notNull(),
            'public' => $this->boolean()->defaultValue(true),

            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->notNull() . ' ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->addForeignKey(
            'fk-report-owner_id',
            'report',
            'owner_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('report');
    }
}
