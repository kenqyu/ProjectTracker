<?php

use yii\db\Migration;

class m160810_210328_comments_visibility extends Migration
{
    public function up()
    {
        $this->addColumn('job_comment', 'public', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        $this->dropColumn('job_comment', 'public');
    }
}
