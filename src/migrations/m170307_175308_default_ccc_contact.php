<?php

use yii\db\Migration;

class m170307_175308_default_ccc_contact extends Migration
{
    public function safeUp()
    {
        $this->addColumn('user', 'default_ccc_contact', $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'default_ccc_contact');
    }
}
