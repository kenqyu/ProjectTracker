<?php

use yii\db\Migration;

class m160726_215831_user_roles extends Migration
{
    public function up()
    {
        $this->createTable('user_roles', [
            'user_id' => $this->integer()->notNull(),
            'role_id' => $this->integer()->notNull(),
            'PRIMARY KEY(user_id, role_id)'
        ]);

        $this->addForeignKey(
            'fk-user_roles-user_id',
            'user_roles',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('user_roles');
    }
}
