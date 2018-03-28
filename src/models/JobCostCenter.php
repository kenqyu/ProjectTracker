<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "job_cost_center".
 *
 * @property integer $id
 * @property integer $job_id
 * @property integer $cost_center
 * @property integer $percent
 *
 * @property Job $job
 */
class JobCostCenter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_cost_center';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['job_id', 'cost_center', 'percent'], 'required'],
            [['job_id', 'percent'], 'integer'],
            [['cost_center'], 'string'],
            [
                ['job_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Job::class,
                'targetAttribute' => ['job_id' => 'id']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'job_id' => 'Job ID',
            'cost_center_id' => 'Cost Center ID',
            'percent' => 'Percent',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJob()
    {
        return $this->hasOne(Job::class, ['id' => 'job_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        Job::addLog($this->job_id);
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        Job::addLog($this->job_id);
        parent::afterDelete();
    }
}