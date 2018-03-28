<?php

use yii\db\Migration;

class m170420_174318_remove_estimated_budget extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('job', 'budget');
    }

    public function safeDown()
    {
        $this->addColumn('job', 'budget', $this->double(2)->notNull());
    }
}
