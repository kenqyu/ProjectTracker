<?php

use yii\db\Migration;

class m160923_190400_remove_size extends Migration
{
    public function up()
    {
        $this->dropColumn('job', 'size');
    }

    public function down()
    {
        $this->addColumn('job', 'size', $this->smallInteger(1)->notNull());
    }
}
