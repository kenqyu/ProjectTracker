<?php

namespace app\models\forms;

use app\models\Job;
use app\models\JobFile;
use yii\base\Model;
use yii\web\UploadedFile;

class JobFileForm extends Model
{
    public $job_id;
    public $title;
    public $description;

    /**
     * @var JobFile
     */
    public $model;

    /**
     * @var UploadedFile
     */
    public $file;

    public function rules()
    {
        return [
            [['job_id', 'title'], 'required'],
            [['job_id'], 'integer'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [
                ['job_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Job::class,
                'targetAttribute' => ['job_id' => 'id']
            ],
            [
                ['file'],
                'file',
                'skipOnEmpty' => true,
                'maxSize' => 1024000
            ],
        ];
    }

    public function save()
    {
        if ($this->validate()) {
            $this->model->user_id = \Yii::$app->user->id;
            $this->model->job_id = $this->job_id;
            $this->model->title = $this->title;
            $this->model->description = $this->description;
            if ($this->model->save()) {
                $this->saveFile();
            }
        }
    }

    private function saveFile()
    {
        if ($this->file && !$this->file->hasError) {
            $this->model->cleanFile();
            $this->file->saveAs($this->model->getPath(true));
            $this->model->file_name = $this->file->baseName . '_' . microtime() . '.' . $this->file->extension;
            $this->model->save(false, ['file_name']);
        }
    }
}
