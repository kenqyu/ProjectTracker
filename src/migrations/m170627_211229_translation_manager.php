<?php

use yii\db\Migration;

class m170627_211229_translation_manager extends Migration
{
    public function safeUp()
    {
        $this->addColumn('job','translation_manager_id', $this->integer());
    }

    public function safeDown()
    {
        echo "m170627_211229_translation_manager cannot be reverted.\n";

        return false;
    }
}
