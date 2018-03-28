<?php

namespace app\models;

use app\helpers\AlexBond;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "job_file".
 *
 * @property integer $id
 * @property integer $job_id
 * @property integer $user_id
 * @property string $file_name
 * @property string $title
 * @property string $description
 * @property string $created_at
 *
 * @property Job $job
 * @property User $user
 */
class JobFile extends \yii\db\ActiveRecord
{
    public $folder = __DIR__ . '/../web/static/uploads/files/';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['job_id', 'user_id', 'title'], 'required'],
            [['job_id', 'user_id'], 'integer'],
            [['description'], 'string'],
            [['created_at'], 'safe'],
            [['file_name', 'title'], 'string', 'max' => 255],
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
            'file_name' => 'File Name',
            'title' => 'Title',
            'description' => 'Description',
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

    public function beforeValidate()
    {
        if (empty($this->title)) {
            $this->title = $this->file_name;
        }
        return parent::beforeValidate();
    }

    public function extraFields()
    {
        return [
            'url'
        ];
    }

    public function getFolder()
    {
        return $this->folder . $this->job_id;
    }

    public function getPath($createFolder = false)
    {
        if ($createFolder) {
            FileHelper::createDirectory($this->getFolder());
        }
        return $this->getFolder() . DIRECTORY_SEPARATOR . $this->file_name;
    }

    public function getUrl()
    {
        return Url::to(['/jobs/jobs/get-file', 'id' => $this->id]);
    }

    public function afterDelete()
    {
        $this->cleanFile();
        if (!Yii::$app->request->isConsoleRequest) {
            Job::addLog($this->job_id);
        }
        parent::afterDelete();
    }

    public function afterSave($insert, $changedAttributes)
    {
        \Yii::$app->cache->set('comment_block_' . $this->job_id . '::last_update', time());
        Job::addLog($this->job_id);
        parent::afterSave($insert, $changedAttributes);
    }

    public function cleanFile()
    {
        if (is_file($this->getPath())) {
            unlink($this->getPath());
            if (\app\helpers\AlexBond::is_dir_empty($this->getFolder())) {
                FileHelper::removeDirectory($this->getFolder());
            }
        }
        $this->file_name = null;
    }

    public function getSize()
    {
        return AlexBond::formatSizeUnits(filesize($this->getPath()));
    }
}