<?php

use yii\db\Migration;

class m170720_191344_invoice_date extends Migration
{
    public function safeUp()
    {
        $this->addColumn('job_invoice', 'date', $this->date()->notNull());
        $this->execute('UPDATE `job_invoice` SET `date`=`created_at`');
    }

    public function safeDown()
    {
        $this->dropColumn('job_invoice', 'date');
    }
}
