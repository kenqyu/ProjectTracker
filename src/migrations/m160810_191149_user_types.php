<?php

use yii\db\Migration;

class m160810_191149_user_types extends Migration
{
    public function up()
    {
        $this->createTable('user_types', [
            'user_id' => $this->integer()->notNull(),
            'type_id' => $this->integer()->notNull(),
            'PRIMARY KEY(user_id, type_id)'
        ]);

        $this->addForeignKey(
            'fk-user_types-user_id',
            'user_types',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('user_types');
    }
}
