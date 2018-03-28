<?php

use yii\db\Migration;

class m170811_180005_default_job_translation_status extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('job_translation', 'status', $this->smallInteger()->defaultValue(4)->notNull());
    }

    public function safeDown()
    {
        $this->alterColumn('job_translation', 'status', $this->smallInteger()->notNull());
    }
}
