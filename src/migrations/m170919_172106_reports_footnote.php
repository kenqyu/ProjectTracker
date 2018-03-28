<?php

use yii\db\Migration;

class m170919_172106_reports_footnote extends Migration
{
    public function safeUp()
    {
        $this->addColumn('report', 'footnote', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn('report', 'footnote');
    }

}
