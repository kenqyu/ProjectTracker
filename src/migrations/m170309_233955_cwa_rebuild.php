<?php

use app\models\CWA;
use yii\db\Migration;

class m170309_233955_cwa_rebuild extends Migration
{
    public function safeUp()
    {
        $this->createTable('cwa', [
            'id' => $this->primaryKey(),
            'number' => $this->integer()->unique()->notNull(),
            'amount' => $this->double(2)->notNull(),
            'due_date' => $this->date()->notNull()
        ]);

        $this->addColumn('cwa', 'owner_id', $this->integer()->notNull());
        $this->addColumn('cwa', 'name', $this->string()->notNull());

        $this->addColumn('job', 'cwa_id', $this->integer());

        $this->addForeignKey(
            'fk-job-cwa_id',
            'job',
            'cwa_id',
            'cwa',
            'id',
            'CASCADE'
        );

        $owner = \app\models\User::find()->one();
        $items = Yii::$app->db->createCommand('SELECT DISTINCT CAST(`cwa` as INTEGER) as cwa FROM `job`')->queryAll();
        foreach ($items as $item) {
            if ($item['cwa'] > 0) {
                $model = new CWA();
                $model->name = $item['cwa'] . '';
                $model->owner_id = $owner->id;
                $model->number = $item['cwa'];
                $model->amount = 0;
                $model->due_date = new \yii\db\Expression('NOW()');
                if ($model->save()) {
                    Yii::$app->db->createCommand('UPDATE `job` SET `cwa_id`=:id WHERE CAST(`cwa` as INTEGER) = :number',
                        ['id' => $model->id, 'number' => $item['cwa']])->execute();
                }
            }
        }

        $this->dropColumn('job', 'cwa');
        $this->dropColumn('job', 'cwa_due_date');
        $this->dropColumn('job', 'estimate_amount');
    }

    public function safeDown()
    {
        return false;
    }
}
