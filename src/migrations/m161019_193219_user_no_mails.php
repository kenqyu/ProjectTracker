<?php

use yii\db\Migration;

class m161019_193219_user_no_mails extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'no_mails', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        $this->dropColumn('user', 'no_mails');
    }
}
