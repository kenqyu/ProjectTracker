<?php

use yii\db\Migration;

class m170525_193039_work_type_cleanup extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('work_type', 'custom_form_id');
    }

    public function safeDown()
    {
        $this->addColumn('work_type', 'custom_form_id', $this->integer());
    }
}
