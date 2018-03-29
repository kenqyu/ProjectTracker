<?php

namespace app\modules\jobs\models;

use app\models\CWA;
use app\models\enums\CustomFormFieldType;
use app\models\forms\JobForm;
use app\models\Job;
use app\models\JobCostCenter;
use app\models\JobCustomFields;
use app\models\JobFile;
use app\models\JobGroup;
use app\models\JobLink;
use app\models\RequestType;
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class CreateJobForm extends Model
{

    public $name;
    public $description;
    public $due_date;
    public $approver;

    public $request_type = [];
    public $processing_unit;

    /**
     * @var UploadedFile[]
     */
    public $files = [];

    public $requests = [];

    public function rules()
    {
        return [
            [
                [
                    'name',
                    'description',
                    'approver',
                    'due_date',
                    'processing_unit'
                ],
                'required'
            ],
            [['description'], 'string'],
            [['request_type', 'requests'], 'safe'],
        ];
    }

    public function save()
    {
        $group_id = null;
        if (count($this->requests) > 1) {
            $group = new JobGroup();
            $group->name = $this->name;
            $group->save();
            $group_id = $group->id;
        }
        foreach ($this->requests as $key => $request) {
            $this->proceedRequest($key, $request, $group_id);
        }
        return true;
    }

    private function proceedRequest(int $request_type, array $request_details, $group_id)
    {
        $request = RequestType::findOne($request_type);

        $job = new Job();
        $job->loadDefaultValues();
        $job->created_at = new Expression('NOW()');
        $job->updated_at = new Expression('NOW()');

        $model = new JobForm();
        $model->setModel($job);

        $model->name = $this->name . ' - ' . $request->name;
        $model->description = $this->description;
        $model->due_date = date('Y-m-d', strtotime($this->due_date));
        $model->approver = $this->approver;

        $model->job_group_id = $group_id;
        $model->request_type_id = $request_type;
        $model->processing_unit_id = $this->processing_unit;

        $model->mandate = $request_details['mandate'] ?? false;
        $model->translation_needed = $request_details['translation_needed'] ?? false;

        $model->page_update = $request_details['page_update'] ?? false;
        $model->links = $request_details['links'] ?? [];


        if (!$model->save()) {
            /** @var \mito\sentry\Component $sentry */
            $sentry = \Yii::$app->sentry;
            $sentry->captureMessage('Error while adding request.', [
                'Model errors' => $model->getErrors()
            ]);
            return;
        }
        if ($request->show_cost_center) {
            $this->saveCostCenters($model, $request_details['cost_centers']);
        } else {
            $this->saveCostCenters($model,
                ArrayHelper::map($request->requestTypeDefaultCostCenters, 'id', function ($item) {
                    return [
                        'cost_center' => $item->cost_center_label,
                        'percent' => $item->cost_center_percent
                    ];
                }));
        }
        if ($request->show_cwa) {
            $this->saveCWAs($model, $request_details['cwa'] ?? []);
        }
        if (isset($request_details['custom_fields'])) {
            $this->proceedCustomFields($model, $request, $request_details['custom_fields']);
        }
        $this->saveFiles($model->model,
            UploadedFile::getInstances($this, 'requests[' . $request_type . '][files]'));
    }

    private function saveFiles(Job $job, array $files)
    {
        foreach ($files as $file) {
            if (!$file->hasError) {
                $model = new JobFile();
                $model->title = $file->baseName . '.' . $file->extension;
                $model->job_id = $job->id;
                $model->user_id = \Yii::$app->user->id;
                $model->file_name = $file->baseName . '_' . time() . '.' . $file->extension;
                if ($model->save()) {
                    if (!$file->saveAs($model->getPath(true))) {
                        $model->delete();
                    }
                }
            }
        }
    }

    private function saveCostCenters(JobForm $form, $cost_centers)
    {
        foreach ($cost_centers as $item) {
            $model = new JobCostCenter();
            $model->cost_center = $item['cost_center'];
            $model->percent = $item['percent'];
            $model->job_id = $form->model->id;
            $model->save();
        }
    }

    private function saveCWAs(JobForm $form, $cwa)
    {
        if (is_array($cwa)) {
            foreach ($cwa as $item) {
                $model = CWA::findOne($item);

                if ($item) {
                    $form->model->link('cwa', $model);
                }
            }
        }
    }

    private function proceedCustomFields(JobForm $model, RequestType $request, array $custom_fields)
    {
        $field_types = ArrayHelper::map($request->customForm->customFormFields, 'id', 'type');
        $field_options = ArrayHelper::map($request->customForm->customFormFields, 'id', 'options');
        foreach ($custom_fields as $key => $f) {
            if (!isset($f['value'])) {
                continue;
            }
            $field = new JobCustomFields();
            $field->job_id = $model->model->id;
            $field->label = $f['label'];
            switch ($field_types[$key]) {
                case CustomFormFieldType::CHECKBOX:
                    $field->value = $f['value'] ?? false;
                    break;
                case CustomFormFieldType::CHECKBOX_LIST:
                    $field->value = json_encode($f['value'] ?? []);
                    break;
                default:
                    $field->value = $f['value'];
            }
            $field->type = $field_types[$key];
            $field->options = $field_options[$key];
            $field->save();
        }
    }
}
