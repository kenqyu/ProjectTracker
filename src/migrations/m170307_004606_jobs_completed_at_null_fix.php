<?php

use yii\db\Migration;

class m170307_004606_jobs_completed_at_null_fix extends Migration
{
    public function safeUp()
    {
        $this->update('job', [
            'created_at' => null
        ], 'created_at < :date', ['date' => '2000-01-01']);
    }

    public function safeDown()
    {
        return false;
    }
}
