<?php

use yii\db\Migration;

class m160810_191430_user_roles_refactor extends Migration
{
    public function up()
    {
        $this->dropTable('user_roles');
        $this->addColumn('user', 'role', $this->smallInteger(1)->notNull()->defaultValue(0));
    }

    public function down()
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

        $this->dropColumn('user', 'role');
    }
}
