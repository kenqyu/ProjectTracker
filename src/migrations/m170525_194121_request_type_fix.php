<?php

use yii\db\Migration;

class m170525_194121_request_type_fix extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('request_type', 'name', $this->string()->notNull());
        $this->dropIndex('name', 'request_type');
    }

    public function safeDown()
    {
        $this->alterColumn('request_type', 'name', $this->string()->notNull()->unique());
    }
}
