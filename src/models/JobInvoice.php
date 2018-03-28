<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "job_invoice".
 *
 * @property integer $id
 * @property integer $cwa_id
 * @property integer $job_id
 * @property integer $user_id
 * @property string $date
 * @property string $number
 * @property double $amount
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Job $job
 * @property User $user
 * @property CWA $cwa
 */
class JobInvoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cwa_id', 'job_id', 'user_id', 'number', 'amount'], 'required'],
            [['job_id', 'user_id'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at', 'date'], 'safe'],
            [['number'], 'string', 'max' => 255],
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
            [
                ['cwa_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => CWA::class,
                'targetAttribute' => ['cwa_id' => 'id']
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
            'cwa_id' => 'CWA',
            'job_id' => 'Job ID',
            'user_id' => 'User ID',
            'date' => 'Date',
            'number' => 'Number',
            'amount' => 'Amount',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * @return \yii\db\ActiveQuery
     */
    public function getCwa()
    {
        return $this->hasOne(CWA::class, ['id' => 'cwa_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (YII_CONSOLE) {
            return;
        }
        Job::addLog($this->job_id);
    }

    public function afterDelete()
    {
        if (YII_CONSOLE) {
            return;
        }
        Job::addLog($this->job_id);
        parent::afterDelete();
    }
}