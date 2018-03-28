<?php

namespace app\models;

use app\components\PusherComponent;
use app\services\NotificationService;
use Yii;
use yii\bootstrap\Html;
use yii\db\Expression;
use yii\helpers\Url;

/**
 * This is the model class for table "job_comment".
 *
 * @property integer $id
 * @property integer $job_id
 * @property integer $user_id
 * @property boolean $public
 * @property string $body
 * @property string $created_at
 *
 * @property Job $job
 * @property User $user
 */
class JobComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['job_id', 'user_id'], 'required'],
            [['job_id', 'user_id'], 'integer'],
            [['body'], 'string'],
            [['public'], 'boolean'],
            [['created_at'], 'safe'],
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
            'body' => 'Body',
            'public' => 'Is Public',
            'created_at' => 'Created At',
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

    private function getMentionsRedEx()
    {
        return '/(^|[^a-z0-9_])@([a-z0-9_.]+)/i';
    }

    public function beforeSave($insert)
    {
        $this->body = strip_tags($this->body);
        foreach ($this->getMentions() as $mention) {
            $this->body = str_replace(
                '@' . $mention->username,
                '<span class="mention" data-user_id="' . $mention->id . '">@' . $mention->username . "</span>",
                $this->body
            );
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return User[]
     */
    public function getMentions()
    {
        preg_match_all($this->getMentionsRedEx(), $this->body, $mentions);
        return User::find()->where(['in', 'username', collect($mentions[2])->unique()])->all();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->job->updated_at = new Expression('NOW()');
        $this->job->save();
        if (YII_CONSOLE) {
            return;
        }
        \Yii::$app->cache->set('comment_block_' . $this->job_id . '::last_update', time());

        $mentions_ids = [];
        foreach ($this->getMentions() as $item) {
            if ($item->id != $this->user_id) {
                NotificationService::getInstance()->addNotification(
                    $item,
                    $this->job,
                    $this->user->username . ' mentioned you in a comment to ' . $this->job->name . ' (' . $this->job->legacy_id . ')',
                    '<span class="mention" data-id="' . $this->user->id . '">' . $this->user->username . '</span> mentioned you in a comment for ' . Html::a($this->job->name,
                        $this->job->getUrl())
                );
                $mentions_ids[] = $item->id;
            }
        }
        foreach ($this->job->subscribers as $item) {
            if ($item->id != $this->user_id && !in_array($item->id, $mentions_ids)) {
                NotificationService::getInstance()->addNotification(
                    $item,
                    $this->job,
                    $this->user->username . ' posted a comment on ' . $this->job->name . ' (' . $this->job->legacy_id . ')',
                    '<span class="mention" data-id="' . $this->user->id . '">' . $this->user->username . '</span> posted a comment on ' . Html::a($this->job->name,
                        $this->job->getUrl())
                );
            }
        }
    }
}