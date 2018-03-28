<?php

use yii\db\Migration;

class m170915_231922_reports_processing_unit_id extends Migration
{
    public function safeUp()
    {
        $this->addColumn('report', 'processing_unit_id', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('report', 'processing_unit_id');
    }
}
