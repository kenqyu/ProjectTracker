<?php

use yii\db\Migration;

class m170525_181333_job_group_fix extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('job', 'job_group_id', $this->integer());
    }

    public function safeDown()
    {
        $this->alterColumn('job', 'job_group_id', $this->integer()->notNull());
    }
}
