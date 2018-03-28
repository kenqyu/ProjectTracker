<?php

use yii\db\Migration;

class m160822_193340_job_old_id extends Migration
{
    public function up()
    {
        $this->addColumn('job', 'old_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('job', 'job_id');
    }
}
