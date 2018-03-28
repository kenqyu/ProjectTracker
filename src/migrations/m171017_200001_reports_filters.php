<?php

use yii\db\Migration;

class m171017_200001_reports_filters extends Migration
{
    public function safeUp()
    {
        $this->addColumn('report', 'filters', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn('report', 'filters');
    }
}
