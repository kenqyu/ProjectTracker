<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cwa".
 *
 * @property integer $id
 * @property integer $number
 * @property double $amount
 * @property string $due_date
 * @property string $name
 * @property integer $owner_id
 *
 * @property User $owner
 * @property Job[] $jobs
 * @property Job[] $jobsSecondary
 */
class CWA extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cwa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'amount', 'due_date', 'name', 'owner_id'], 'required'],
            [['number', 'owner_id'], 'integer'],
            [['amount'], 'number'],
            [['due_date'], 'safe'],
            [['number'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'CWA Number',
            'amount' => 'Amount',
            'due_date' => 'End Date',
            'name' => 'Name',
            'owner_id' => 'Owner'
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::class, ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobs()
    {
        return $this->hasMany(Job::class, ['id' => 'job_id'])
            ->viaTable('job_cwa', ['cwa_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobsSecondary()
    {
        return $this->hasMany(Job::class, ['second_cwa_id' => 'id']);
    }

    public static function getDataList($excludeCWAs = [], $withName = true, $withEmpty = false, $emptyLabel = 'None')
    {
        $out = collect(ArrayHelper::map(static::find()->orderBy(['number' => SORT_ASC])->all(), 'id',
            function ($item) use ($withName) {
                /** @var CWA $item */
                return $item->number . ($withName ? (' - ' . $item->name) : '');
            }))->filter(function ($item, $key) use ($excludeCWAs) {
            return !in_array($key, $excludeCWAs);
        })->all();
        if (!$withEmpty) {
            return $out;
        }

        return ['' => $emptyLabel] + $out;
    }

    public static function getDataListInfo(
        $excludeJobs = [],
        $excludeCWAs = [],
        $withName = true
    ) {
        $out = collect(ArrayHelper::map(static::find()->all(), 'id',
            function ($item) use ($excludeJobs, $withName) {
                /** @var CWA $item */
                return [
                    'amount' => $item->amount,
                    'used' => $item->getTotalUsage($excludeJobs)
                ];
            }))
            ->filter(function ($item, $key) use ($excludeCWAs) {
                return !in_array($key, $excludeCWAs);
            })
            ->all();

        return $out;
    }

    public function getBalance($excludeJobs = [])
    {
        return $this->amount - $this->getTotalUsage($excludeJobs);
    }

    public function getTotalUsage($excludeJobs = [])
    {
        $jobs = collect(Job::find()->joinWith('cwa')->where(['cwa_id' => $this->id])->with('jobInvoices')->all());
        $id = $this->id;
        return $jobs
            ->filter(function ($item) use ($excludeJobs) {
                return !in_array($item->id, $excludeJobs);
            })
            ->sum(function ($item) use ($id) {
                /**
                 * @var Job $item
                 */
                return collect($item->jobInvoices)->sum(function ($item) use ($id) {
                    return $item->cwa_id == $id ? $item->amount : 0;
                });
            });
    }
}