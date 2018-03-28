<?php

namespace app\models;

use app\models\enums\JobTranslationStatus;
use Yii;

/**
 * This is the model class for table "job_translation".
 *
 * @property integer $id
 * @property integer $job_id
 * @property integer $rush
 * @property integer $language
 * @property string $due_date
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Job $job
 */
class JobTranslation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['job_id', 'language', 'due_date', 'status'], 'required'],
            [['job_id', 'rush', 'language', 'status'], 'integer'],
            [['status'], 'default', 'value' => JobTranslationStatus::REQUESTED],
            [['due_date', 'created_at', 'updated_at'], 'safe'],
            [
                ['job_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Job::class,
                'targetAttribute' => ['job_id' => 'id']
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
            'rush' => 'Rush',
            'language' => 'Language',
            'due_date' => 'Due Date',
            'status' => 'Status',
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