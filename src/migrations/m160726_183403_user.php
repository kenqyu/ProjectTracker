<?php

use yii\db\Migration;

class m160726_183403_user extends Migration
{
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'access_token' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string()->notNull()->unique(),
            'username' => $this->string()->notNull()->unique(),
            'password' => $this->string()->notNull(),
            'password_reset_token' => $this->string(),
            'email' => $this->string()->notNull()->unique(),
            'first_name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'status' => $this->smallInteger(1)->defaultValue(0)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->notNull() . ' ON UPDATE CURRENT_TIMESTAMP'
        ]);
    }

    public function down()
    {
        $this->dropTable('user');
    }

}
