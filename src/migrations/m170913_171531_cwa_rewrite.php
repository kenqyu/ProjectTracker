<?php

use yii\db\Migration;

class m170913_171531_cwa_rewrite extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('job', 'second_cwa_id');

        $this->createTable('job_cwa', [
            'job_id' => $this->integer()->notNull(),
            'cwa_id' => $this->integer()->notNull(),
            'PRIMARY KEY(job_id, cwa_id)'
        ]);

        $this->addForeignKey(
            'fk-job_cwa-job_id',
            'job_cwa',
            'job_id',
            'job',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-job_cwa-cwa_id',
            'job_cwa',
            'cwa_id',
            'cwa',
            'id',
            'CASCADE',
            'CASCADE'
        );

        foreach (\app\models\JobInvoice::find()->all() as $item) {
            /** @var $item \app\models\JobInvoice */
            if (!$item->job->cwa_id) {
                $item->delete();
                continue;
            }
            $item->cwa_id = $item->job->cwa_id;
            $item->save(false, ['cwa_id']);
        }

        foreach (\app\models\Job::find()->where(['IS NOT', 'cwa_id', null])->all() as $item) {
            /** @var $item \app\models\Job */
            $this->insert('job_cwa', ['job_id' => $item->id, 'cwa_id' => $item->cwa_id]);
        }

        $this->dropForeignKey('fk-job-cwa_id', 'job');
        $this->dropColumn('job', 'cwa_id');
    }

    public function safeDown()
    {
        $this->addColumn('job', 'cwa_id', $this->integer());

        $this->dropTable('job_cwa');

        $this->addColumn('job', 'second_cwa_id', $this->integer());
    }
}
