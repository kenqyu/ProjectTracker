<?php

use yii\db\Migration;

class m171102_220547_processing_unit_sort extends Migration
{
    public function safeUp()
    {
        $this->addColumn('processing_unit', 'order', $this->integer()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('processing_unit', 'order');
    }
}
