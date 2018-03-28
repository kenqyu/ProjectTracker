<?php

namespace app\models;

use app\components\PusherComponent;
use Yii;

/**
 * This is the model class for table "notifications".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $date
 * @property integer $job_id
 * @property string $title
 * @property string $message
 * @property integer $read
 * @property integer $sent
 *
 * @property Job $job
 * @property User $user
 */
class Notifications extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'job_id', 'title', 'message'], 'required'],
            [['user_id', 'job_id', 'read', 'sent'], 'integer'],
            [['date'], 'safe'],
            [['message', 'title'], 'string'],
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
            'user_id' => 'User ID',
            'date' => 'Date',
            'job_id' => 'Job ID',
            'title' => 'Title',
            'message' => 'Message',
            'read' => 'Read',
            'sent' => 'Sent',
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
     * @inheritdoc
     * @return \app\models\queries\NotificationsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\NotificationsQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

    public function markAsRead()
    {
        $this->read = true;
        return $this->save(false, ['read']);
    }

    public function markAsSent()
    {
        $this->sent = true;
        return $this->save(false, ['sent']);
    }
}