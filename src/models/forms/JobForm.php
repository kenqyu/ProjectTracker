<?php

namespace app\models\forms;

use app\models\Job;
use app\models\JobLink;
use app\models\Justifications;
use app\models\WorkType;
use yii\base\Model;
use yii\db\Expression;

class JobForm extends Model
{
    /**
     * @var Job
     */
    public $model;

    public $id;

    public $legacy_id;
    public $name;
    public $description;
    public $status;
    public $due_date;
    public $mandate = 0;
    public $creator_id;
    public $project_lead_id;
    public $project_manager_id;
    public $translation_manager_id;
    public $approver;
    public $translation_needed = 0;
    public $agency_id;
    public $iwcm_publishing_assignee_id;
    public $internal_only;
    public $ccc_impact;
    public $one_voice;
    public $ccc_contact_id;
    public $content_expiration_date;
    public $page_update;
    public $completed_on;
    public $published_on;

    public $job_group_id;
    public $request_type_id;
    public $processing_unit_id;

    public $work_types = [];
    public $justifications = [];

    public $links = [];


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'name',
                    'description',
                    'approver',
                    'due_date',
                    'mandate',
                    'creator_id',
                    'processing_unit_id',
                    'request_type_id'
                ],
                'required'
            ],
            [['description'], 'string'],
            [['translation_needed', 'mandate', 'page_update'], 'boolean'],
            [
                [
                    'id',
                    'status',
                    'creator_id',
                    'project_lead_id',
                    'iwcm_publishing_assignee_id',
                    'internal_only',
                    'ccc_impact',
                    'one_voice',
                    'ccc_contact_id',
                    'processing_unit_id',
                    'request_type_id',
                    'job_group_id'
                ],
                'integer'
            ],
            [['work_types', 'justifications', 'links', 'customFields'], 'safe'],
            [['due_date', 'content_expiration_date', 'completed_on', 'published_on'], 'safe'],
            [['legacy_id', 'name', 'approver'], 'string', 'max' => 255],
            [
                ['legacy_id'],
                'unique',
                'targetClass' => \app\models\Job::class,
                'targetAttribute' => ['legacy_id' => 'legacy_id'],
                'filter' => ['!=', 'id', $this->id]
            ],
            [
                ['ccc_contact_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\User::class,
                'targetAttribute' => ['ccc_contact_id' => 'id']
            ],
            [
                ['creator_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\User::class,
                'targetAttribute' => ['creator_id' => 'id']
            ],
            [
                ['iwcm_publishing_assignee_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\User::class,
                'targetAttribute' => ['iwcm_publishing_assignee_id' => 'id']
            ],
            [
                ['project_lead_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\User::class,
                'targetAttribute' => ['project_lead_id' => 'id']
            ],
            [
                ['project_manager_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\User::class,
                'targetAttribute' => ['project_manager_id' => 'id']
            ],
            [
                ['translation_manager_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\User::class,
                'targetAttribute' => ['translation_manager_id' => 'id']
            ],
            [
                ['agency_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => \app\models\Agency::class,
                'targetAttribute' => ['agency_id' => 'id']
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Job Name',
            'project_lead_id' => 'Project Lead',
            'project_manager_id' => 'Project Manager',
            'agency_id' => 'Agency',
            'iwcm_publishing_assignee_id' => 'CMS Assignee',
            'justifications' => 'Justification for Request',
            'ccc_contact_id' => 'CCC Contact',
            'completed_on' => 'Completed/Cancelled Date',
            'page_update' => 'If this request will require content updates to existing sce.com page(s), check this box.',
            'processing_unit_id' => 'Processing Department',
            'request_type_id' => 'Request Type',
            'work_types' => 'Work Types (Legacy)',
            'translation_manager_id' => 'Translation Manager'
        ];
    }

    public function attributeHints()
    {
        return [
            'agency_id' => 'External/third party company completing project work',
            'approver' => 'Content owner, program manager, or original requestor. For data requests, this is the person who should complete the request.',
            'ccc_impact' => 'Does information need to be communicated to the customer call center?',
            'ccc_contact_id' => 'Customer call center contact name',
            'files' => 'You can upload multiple files at once',
            'mandate' => 'A mandate project must be completed by a certain date due to CPUC regulations. Please provide supporting documentation for mandate requests.',
            'one_voice' => 'Does the project meet One Voice guidelines and has associated paperwork been submitted?',
            'project_lead_id' => 'The person responsible for overseeing the project from end to end',
            'project_manager_id' => 'The person responsible for day to day project activities',
            'internal_only' => 'All project work is completed by SCE, without aide of outside agency',
            'description' => 'Please provide as much detail as possible about your request, including justification for the request.'
        ];
    }

    public function isNewRecord()
    {
        return $this->model->isNewRecord;
    }

    public function setModel(Job $model)
    {
        $this->model = $model;
        foreach ($model->workTypes as $item) {
            $this->work_types[] = $item->id;
        }
        $this->justifications = [];
        foreach ($model->justifications as $item) {
            $this->justifications[] = $item->id;
        }
        $this->links = [];
        foreach ($model->jobLinks as $item) {
            $this->links[] = $item->link;
        }

    }

    public function beforeValidate()
    {
        if ($this->isNewRecord()) {
            if (empty($this->legacy_id)) {
                $this->legacy_id = date("ymd") . "-" . sprintf("%03d",
                        Job::find()->where(['>=', 'created_at', new Expression('CURDATE()')])->count() + 1);
            }
            $this->creator_id = \Yii::$app->user->id;
        }
        $this->due_date = date('Y-m-d', strtotime($this->due_date));
        if (!empty($this->completed_on)) {
            $this->completed_on = date('Y-m-d', strtotime($this->completed_on));
        }
        return parent::beforeValidate();
    }

    public function save()
    {
        $this->model->setAttributes($this->getAttributes(null, ['processing_units', 'justifications']));
        if ($this->model->validate() && $this->model->save()) {
            $this->saveWorkTypes();
            $this->saveJustifications();
            $this->saveLinks();
            return true;
        }
        $this->addErrors($this->model->getErrors());
        return false;
    }

    private function saveWorkTypes()
    {
        $this->model->unlinkAll('workTypes', true);
        foreach ($this->work_types as $item) {
            $model = WorkType::findOne($item);
            if ($model) {
                $this->model->link('workTypes', $model);
            }
        }
    }

    private function saveJustifications()
    {
        $this->model->unlinkAll('justifications', true);
        foreach ($this->justifications as $item) {
            $model = Justifications::findOne($item);
            if ($model) {
                $this->model->link('justifications', $model);
            }
        }
    }

    private function saveLinks()
    {
        $this->model->unlinkAll('jobLinks', true);
        if ($this->page_update) {
            foreach ($this->links as $link) {
                $link = mb_split('!ut/', $link)[0];
                $model = new JobLink();
                $model->link = $link;
                $this->model->link('jobLinks', $model);
            }
        }
    }


}
