<?php

namespace app\modules\jobs\models;

use app\models\enums\CustomFormFieldType;
use app\models\forms\JobForm;
use app\models\Job;
use app\models\JobCustomFields;
use app\models\JobFile;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\UploadedFile;

class UpdateJobForm extends JobForm
{
    /**
     * @var UploadedFile[]
     */
    public $files = [];
    public $customFields = [];

    private $oldCustomFields = [];

    public function setModel(Job $model)
    {
        parent::setModel($model);
        $this->oldCustomFields = $this->customFields = ArrayHelper::map($model->customFields, 'id', function ($item) {
            return [
                'label' => $item->label,
                'type' => $item->type,
                'value' => $item->type == CustomFormFieldType::CHECKBOX_LIST ? json_decode($item->value) : $item->value,
                'options' => $item->options,
            ];
        });
    }

    public function save()
    {
        if (parent::save()) {
            $this->saveFiles();
            $this->saveCustomFields();
            return true;
        }
        return false;
    }

    public function saveFiles()
    {
        foreach ($this->files as $file) {
            if (!$file->hasError) {
                $model = new JobFile();
                $model->title = $file->baseName . '.' . $file->extension;
                $model->job_id = $this->model->id;
                $model->user_id = \Yii::$app->user->id;
                $model->file_name = $file->baseName . '_' . microtime() . '.' . $file->extension;
                if ($model->save()) {
                    if (!$file->saveAs($model->getPath(true))) {
                        $model->delete();
                    }
                }
            }
        }
    }

    private function saveCustomFields()
    {
        $this->model->unlinkAll('customFields', true);
        foreach ($this->customFields as $key => $field) {
            $m = new JobCustomFields();
            if ($this->oldCustomFields[$key]['type'] == CustomFormFieldType::CHECKBOX_LIST) {
                $field['value'] = json_encode($field['value'] ?? []);
            }
            $m->setAttributes($this->oldCustomFields[$key]);
            $m->setAttributes($field);
            $this->model->link('customFields', $m);
        }
    }
}
