<?php

use yii\db\Migration;

class m171020_191349_request_type_alert extends Migration
{
    public function safeUp()
    {
        $this->addColumn('request_type', 'alert', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn('request_type', 'alert');
    }
}
