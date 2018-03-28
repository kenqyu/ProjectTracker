<?php

use yii\db\Migration;

class m160728_215726_saved_search extends Migration
{
    public function up()
    {
        $this->createTable('saved_search', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'data' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->notNull() . ' ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->addForeignKey(
            'fk-saved_search-user_id',
            'saved_search',
            'user_id',
            'user',
            'id'
        );
    }

    public function down()
    {
        $this->dropTable('saved_search');
    }
}
