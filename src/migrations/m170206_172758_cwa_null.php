<?php

use yii\db\Migration;

class m170206_172758_cwa_null extends Migration
{
    public function safeUp()
    {
        \app\models\Job::updateAll(['cwa_due_date' => null], 'cwa_due_date < "2000-01-01"');
    }

    public function safeDown()
    {
        return true;
    }
}
