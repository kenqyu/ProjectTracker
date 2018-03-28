<?php

use yii\db\Migration;

class m170912_192706_second_cwa extends Migration
{
    public function safeUp()
    {
        $this->addColumn('job', 'second_cwa_id', $this->integer());

        $this->addColumn('job_invoice', 'cwa_id', $this->integer());

        foreach (\app\models\JobInvoice::find()->all() as $item) {
            /** @var $item \app\models\JobInvoice */
            $item->cwa_id = $item->job->cwa_id;
            $item->save(false, ['cwa_id']);
        }
    }

    public function safeDown()
    {
        $this->dropColumn('job', 'second_cwa_id');

        $this->dropColumn('job_invoice', 'cwa_id');
    }
}
