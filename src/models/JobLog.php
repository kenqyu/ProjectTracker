<?php

namespace app\models;

use app\helpers\AlexBond;
use app\helpers\AssocArrayDiff;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "job_log".
 *
 * @property integer $id
 * @property integer $job_id
 * @property integer $user_id
 * @property string $created_at
 * @property string $after
 *
 * @property Job $job
 * @property User $user
 * @property JobLog $prev
 */
class JobLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['job_id', 'user_id', 'after'], 'required'],
            [['job_id', 'user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['after'], 'string'],
            [
                ['job_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Job::class,
                'targetAttribute' => ['job_id' => 'id']
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
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
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'after' => 'After',
            'cwa' => 'CWA',
            'cwa_due_date' => 'CWA Due Date',
            'estimate_amount' => 'CWA Amount'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJob()
    {
        return $this->hasOne(Job::class, ['id' => 'job_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return JobLog
     */
    public function getPrev()
    {
        return JobLog::find()
            ->andWhere(['job_id' => $this->job_id])
            ->andWhere(['<', 'id', $this->id])
            ->orderBy(['id' => SORT_DESC])
            ->one();
    }

    public function getChanges($prev)
    {
        if (!$prev) {
            return false;
        }

        $new = json_decode($this->after, true);

        $initialMerge = ArrayHelper::merge(AssocArrayDiff::array_diff_assoc_recursive(
            $new, $prev
        ), AssocArrayDiff::array_diff_assoc_recursive(
            $prev, $new
        ));

        foreach ($prev as $key => $item) {
            if (isset($prev[$key]) && !isset($new[$key]) || !isset($prev[$key]) && isset($new[$key]) || isset($prev[$key]) && isset($new[$key]) && $prev[$key] != $new[$key]) {
                if (!isset($initialMerge[$key])) {
                    $initialMerge[$key] = true;
                }
            }
        }
        return $initialMerge;
    }
}