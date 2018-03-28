<?php

use yii\db\Migration;

class m160815_221005_user_approval extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'approved', $this->boolean()->notNull()->defaultValue(false));
    }

    public function down()
    {
        $this->dropColumn('user', 'approved');
    }
}
