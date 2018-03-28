<?php

use yii\db\Migration;

class m160815_222522_notifications_title extends Migration
{
    public function up()
    {
        $this->addColumn('notifications', 'title', $this->string()->notNull());
    }

    public function down()
    {
        $this->dropColumn('notifications', 'title');
    }
}
