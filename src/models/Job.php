<?php

namespace app\models;

use app\models\enums\JobDueDatePriority;
use app\models\enums\JobStatus;
use app\models\enums\UserRoles;
use app\services\NotificationService;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "job".
 *
 * @property integer $id
 * @property integer $old_id
 * @property string $legacy_id
 * @property string $name
 * @property string $description
 * @property integer $status
 * @property string $due_date
 * @property boolean $mandate
 * @property integer $creator_id
 * @property integer $project_lead_id
 * @property integer $project_manager_id
 * @property integer $translation_manager_id
 * @property integer $agency_id
 * @property integer $iwcm_publishing_assignee_id
 * @property string $approver
 * @property boolean $translation_needed
 * @property integer $internal_only
 * @property integer $ccc_impact
 * @property integer $one_voice
 * @property integer $ccc_contact_id
 * @property string $content_expiration_date
 * @property boolean $page_update
 * @property string $completed_on
 * @property string $published_on
 * @property string $created_at
 * @property string $updated_at
 *
 * @property integer $processing_unit_id
 * @property integer $request_type_id
 * @property integer $job_group_id
 *
 * @property User $cccContact
 * @property User $creator
 * @property User $iwcmPublishingAssignee
 * @property User $projectLead
 * @property User $projectManager
 * @property User $translationManager
 * @property Agency $agency
 * @property OrganizationUnit $processingUnit
 * @property JobComment[] $jobComments
 * @property JobCostCenter[] $jobCostCenters
 * @property JobFile[] $jobFiles
 * @property JobInvoice[] $jobInvoices
 * @property JobTranslation[] $jobTranslations
 * @property User[] $subscribers
 * @property WorkType[] $workTypes
 * @property Justifications[] $justifications
 * @property JobLink[] $jobLinks
 * @property CWA[] $cwa
 * @property JobCustomFields[] $customFields
 * @property JobGroup[] $jobGroup
 * @property RequestType $requestType
 */
class Job extends \yii\db\ActiveRecord
{
    public $isSubscribed;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'due_date', 'mandate', 'creator_id'], 'required'],
            [['description'], 'string'],
            [
                [
                    'old_id',
                    'status',
                    'creator_id',
                    'project_lead_id',
                    'translation_manager_id',
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
            [['status'], 'in', 'range' => JobStatus::getKeys()],
            [['status'], 'default', 'value' => JobStatus::NEW],
            [['due_date', 'content_expiration_date', 'completed_on', 'published_on'], 'safe'],
            [['legacy_id', 'name', 'approver'], 'string', 'max' => 255],
            [['legacy_id'], 'unique'],
            [['translation_needed', 'mandate', 'page_update'], 'boolean'],
            [
                ['ccc_contact_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['ccc_contact_id' => 'id']
            ],
            [
                ['creator_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['creator_id' => 'id']
            ],
            [
                ['iwcm_publishing_assignee_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['iwcm_publishing_assignee_id' => 'id']
            ],
            ['internal_only', 'default', 'value' => 1],
            [
                ['project_lead_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['project_lead_id' => 'id']
            ],
            [
                ['project_manager_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['project_manager_id' => 'id']
            ],
            [
                ['translation_manager_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['translation_manager_id' => 'id']
            ],
            [
                ['agency_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Agency::class,
                'targetAttribute' => ['agency_id' => 'id']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'old_id' => 'Old ID',
            'legacy_id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'status' => 'Status',
            'due_date' => 'Due Date',
            'mandate' => 'Mandate',
            'size' => 'Size',
            'work_type_id' => 'Work Type ID',
            'creator_id' => 'Creator ID',
            'project_lead_id' => 'Project Lead ID',
            'iwcm_publishing_assignee_id' => 'Iwcm Publishing Assignee',
            'internal_only' => 'Internal Only',
            'estimate_amount' => 'Estimate Amount',
            'ccc_impact' => 'Ccc Impact',
            'one_voice' => 'One Voice',
            'ccc_contact_id' => 'Ccc Contact',
            'content_expiration_date' => 'Content Expiration Date',
            'page_update' => 'Page(s) update required',
            'completed_on' => 'Completed On',
            'published_on' => 'Published On',
            'processing_unit_id' => 'Processing Department'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCccContact()
    {
        return $this->hasOne(User::class, ['id' => 'ccc_contact_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIwcmPublishingAssignee()
    {
        return $this->hasOne(User::class, ['id' => 'iwcm_publishing_assignee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectLead()
    {
        return $this->hasOne(User::class, ['id' => 'project_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectManager()
    {
        return $this->hasOne(User::class, ['id' => 'project_manager_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslationManager()
    {
        return $this->hasOne(User::class, ['id' => 'translation_manager_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::class, ['id' => 'agency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCwa()
    {
        return $this->hasMany(CWA::class, ['id' => 'cwa_id'])
            ->viaTable('job_cwa', ['job_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobGroup()
    {
        return $this->hasOne(JobGroup::class, ['id' => 'job_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomFields()
    {
        return $this->hasMany(JobCustomFields::class, ['job_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobComments()
    {
        return $this->hasMany(JobComment::class, ['job_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobCostCenters()
    {
        return $this->hasMany(JobCostCenter::class, ['job_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobFiles()
    {
        return $this->hasMany(JobFile::class, ['job_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobInvoices()
    {
        return $this->hasMany(JobInvoice::class, ['job_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobTranslations()
    {
        return $this->hasMany(JobTranslation::class, ['job_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscribers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('subscription', ['job_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkTypes()
    {
        return $this->hasMany(WorkType::class, ['id' => 'work_type_id'])
            ->viaTable('job_work_type', ['job_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    //public function getDepartments()
    //{
    //    return $this->hasMany(Departments::class, ['id' => 'department_id'])
    //        ->viaTable('job_department', ['job_id' => 'id']);
    //}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessingUnit()
    {
        return $this->hasOne(ProcessingUnit::class, ['id' => 'processing_unit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestType()
    {
        return $this->hasOne(RequestType::class, ['id' => 'request_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJustifications()
    {
        return $this->hasMany(Justifications::class, ['id' => 'justification_id'])
            ->viaTable('job_justification', ['job_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobLinks()
    {
        return $this->hasMany(JobLink::class, ['job_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\queries\JobQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\JobQuery(get_called_class());
    }

    public function extraFields()
    {
        return ArrayHelper::merge(parent::extraFields(), [
            'isSubscribed',
            'creator'
        ]);
    }

    public function getIsSubscribed()
    {
        return Subscription::find()->where([
            'user_id' => \Yii::$app->user->id,
            'job_id' => $this->id
        ])->exists();
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            if (empty($this->legacy_id)) {
                $i = Job::find()->where(['>=', 'created_at', new Expression('CURDATE()')])->count();
                do {
                    $i++;
                    $id = date("ymd") . "-" . sprintf("%03d", $i);
                } while (Job::find()->where(['legacy_id' => $id])->exists());
                $this->legacy_id = $id;
            }
            if (empty($this->creator_id)) {
                $this->creator_id = \Yii::$app->user->id;
            }
        }
        if (empty($this->completed_on)) {
            $this->completed_on = null;
        }
        if (empty($this->ccc_contact_id)) {
            Yii::info('No CCC Contact. Setting default');
            $user = User::find()->where(['default_ccc_contact' => true])->one();
            if ($user) {
                Yii::info('Setting default CCC Contact to ' . $user->getFullName());
                $this->ccc_contact_id = $user->id;
            } else {
                Yii::info('Default CCC Contact not found');
            }
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->refresh();
        if (YII_CONSOLE) {
            return;
        }
        Job::addLog($this->id);
        $this->sendNotifications($changedAttributes);
        if ($insert) {

            foreach (User::find()
                         ->where(['role' => UserRoles::ADMIN, 'processing_unit.id' => $this->processing_unit_id])
                         ->joinWith('processingUnits')
                         ->all()
                     as $item) {
                NotificationService::getInstance()->addNotification(
                    $item,
                    $this,
                    $this->creator->getShortName() . ' created a new project ' . $this->name . ' (' . $this->legacy_id . ')',
                    '<span class="mention" data-id="' . $this->creator->id . '">' . $this->creator->username . '</span> created a new project ' . Html::a($this->name,
                        $this->getUrl())
                );
            }

            $message = Yii::$app->mailer->compose('job_submited', ['model' => $this]);
            $message->setFrom('no-reply@scemanagement.com');
            $message->setTo($this->creator->email);
            $message->setSubject('(External):CX Project Tracker Job Submission Confirmation Email');
            Yii::$app->mailer->send($message);
        } else {
            if (array_key_exists('status', $changedAttributes)) {
                if ($changedAttributes['status'] != JobStatus::COMPLETED && $this->status == JobStatus::COMPLETED) {
                    $message = Yii::$app->mailer->compose('completed', ['model' => $this]);
                    $message->setFrom('no-reply@scemanagement.com');
                    $message->setSubject('(External):CX Project Tracker Job Completed');

                    foreach ($this->subscribers as $item) {
                        $message->setTo($item->email);
                        Yii::$app->mailer->send($message);
                    }

                    $message->setTo($this->creator->email);
                    Yii::$app->mailer->send($message);
                }
            }
        }
    }

    private static function generateLogArray($id)
    {
        /**
         * @var $new Job
         */
        $new = static::find()->where(['job.id' => $id])->withAll()->one();
        if (!$new) {
            throw new Exception('Job not found for log');
        }
        $out = $new->getAttributes();
        $out['creator'] = [
            'id' => $new->creator->id,
            'username' => $new->creator->username
        ];
        if ($new->cccContact) {
            $out['cccContact'] = [
                'id' => $new->cccContact->id,
                'username' => $new->cccContact->username
            ];
        }
        if ($new->iwcmPublishingAssignee) {
            $out['iwcmPublishingAssignee'] = [
                'id' => $new->iwcmPublishingAssignee->id,
                'username' => $new->iwcmPublishingAssignee->username
            ];
        }
        if ($new->projectManager) {
            $out['projectManager'] = [
                'id' => $new->projectManager->id,
                'username' => $new->projectManager->username
            ];
        }
        if ($new->projectLead) {
            $out['projectLead'] = [
                'id' => $new->projectLead->id,
                'username' => $new->projectLead->username
            ];
        }
        if ($new->translationManager) {
            $out['translationManager'] = [
                'id' => $new->translationManager->id,
                'username' => $new->translationManager->username
            ];
        }
        if (!empty($new->workTypes)) {
            foreach ($new->workTypes as $item) {
                $out['workTypes'][$item->id] = [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            }
        }
        if (!empty($new->jobCostCenters)) {
            foreach ($new->jobCostCenters as $item) {
                $out['jobCostCenters'][$item->id] = [
                    'id' => $item->id,
                    'name' => $item->cost_center,
                    'percent' => $item->percent,
                ];
            }
        }
        if (!empty($new->jobFiles)) {
            foreach ($new->jobFiles as $item) {
                $out['jobFiles'][$item->id] = [
                    'id' => $item->id,
                    'name' => $item->title
                ];
            }
        }
        if (!empty($new->jobInvoices)) {
            foreach ($new->jobInvoices as $item) {
                $out['jobInvoices'][$item->id] = [
                    'id' => $item->id,
                    'number' => $item->number,
                    'amount' => $item->amount,
                ];
            }
        }
        if (!empty($new->jobTranslations)) {
            foreach ($new->jobTranslations as $item) {
                $out['jobTranslations'][$item->id] = [
                    'id' => $item->id,
                    'language' => $item->language,
                    'status' => $item->status,
                ];
            }
        }
        if (!empty($new->jobLinks)) {
            foreach ($new->jobLinks as $item) {
                $out['jobLinks'][$item->id] = [
                    'link' => $item->link
                ];
            }
        }

        return $out;
    }

    public static function addLog($id)
    {
        $new = self::generateLogArray($id);
        $old = JobLog::find()->where(['job_id' => $id])->orderBy(['id' => SORT_DESC])->one();
        if (!$old || $old->after != json_encode($new)) {
            $model = new JobLog();
            $model->user_id = \Yii::$app->user->id;
            $model->job_id = $id;
            $model->after = json_encode($new);
            return $model->save();
        }
        return false;
    }

    private function sendNotifications($changesAttributes)
    {
        if (count($changesAttributes) == 1 && isset($changesAttributes['updated_at'])) {
            return;
        }
        try {
            foreach ($this->subscribers as $item) {
                if ($item->id != Yii::$app->user->id) {
                    NotificationService::getInstance()->addNotification(
                        $item,
                        $this,
                        'Job ' . $this->legacy_id . ' updated',
                        '<span class="mention" data-id="' . Yii::$app->user->id . '">' . Yii::$app->user->identity->getShortName() . '</span> updated ' . Html::a($this->name,
                            $this->getUrl())
                    );
                }
            }

            // PROJECT LEAD
            if (array_key_exists('project_lead_id', $changesAttributes) && !empty($this->project_lead_id) &&
                $this->project_lead_id != $changesAttributes['project_lead_id']
            ) {

                NotificationService::getInstance()->addNotification(
                    $this->projectLead,
                    $this,
                    'You were assigned as the project lead on ' . $this->name . ' (' . $this->legacy_id . ')',
                    'You were assigned as the project lead on ' . Html::a($this->name, $this->getUrl())
                );

                NotificationService::getInstance()->addNotification(
                    $this->creator,
                    $this,
                    $this->projectLead->getShortName() . ' assigned as project lead on ' . $this->name . ' (' . $this->legacy_id . ')',
                    '<span class="mention" data-id="' . $this->projectLead->id . '">' . $this->projectLead->getShortName() . '</span> assigned as project lead on ' . Html::a($this->name,
                        $this->getUrl())
                );

                $this->projectLead->subscribe($this);
            }

            //PROJECT MANAGER
            if (array_key_exists('project_manager_id', $changesAttributes) && !empty($this->project_manager_id) &&
                $this->project_manager_id != $changesAttributes['project_manager_id']
            ) {

                NotificationService::getInstance()->addNotification(
                    $this->projectManager,
                    $this,
                    'You were assigned as the project manager on ' . $this->name . ' (' . $this->legacy_id . ')',
                    'You were assigned as the project manager on ' . Html::a($this->name, $this->getUrl())
                );

                NotificationService::getInstance()->addNotification(
                    $this->creator,
                    $this,
                    $this->projectManager->getShortName() . ' assigned as project manager on ' . $this->name . ' (' . $this->legacy_id . ')',
                    '<span class="mention" data-id="' . $this->projectManager->id . '">' . $this->projectManager->getShortName() . '</span> assigned as project manager on ' . Html::a($this->name,
                        $this->getUrl())
                );

                $this->projectManager->subscribe($this);
            }

            //IWCm Pub
            if (array_key_exists('iwcm_publishing_assignee_id', $changesAttributes) &&
                !empty($this->iwcm_publishing_assignee_id) &&
                $this->iwcm_publishing_assignee_id != $changesAttributes['iwcm_publishing_assignee_id']
            ) {

                NotificationService::getInstance()->addNotification(
                    $this->iwcmPublishingAssignee,
                    $this,
                    'You were assigned as the CMS Assignee on ' . $this->name . ' (' . $this->legacy_id . ')',
                    'You were assigned as the CMS Assignee on ' . Html::a($this->name, $this->getUrl())
                );

                NotificationService::getInstance()->addNotification(
                    $this->creator,
                    $this,
                    $this->iwcmPublishingAssignee->getShortName() . ' assigned as CMS Assignee on ' . $this->name . ' (' . $this->legacy_id . ')',
                    '<span class="mention" data-id="' . $this->iwcmPublishingAssignee->id . '">' . $this->iwcmPublishingAssignee->getShortName() . '</span> assigned as CMS Assignee on ' . Html::a($this->name,
                        $this->getUrl())
                );

                $this->iwcmPublishingAssignee->subscribe($this);
            }

            // CCC Contact
            if (array_key_exists('ccc_contact_id', $changesAttributes) && !empty($this->ccc_contact_id) &&
                $this->ccc_contact_id != $changesAttributes['ccc_contact_id']
            ) {

                NotificationService::getInstance()->addNotification(
                    $this->cccContact,
                    $this,
                    'You were assigned as the CCC Contact on ' . $this->name . ' (' . $this->legacy_id . ')',
                    'You were assigned as the CCC Contact on ' . Html::a($this->name, $this->getUrl())
                );


                $this->cccContact->subscribe($this);
            }

            if (array_key_exists('due_date', $changesAttributes) && Yii::$app->user->id !== $this->creator->id) {
                NotificationService::getInstance()->addNotification(
                    $this->creator,
                    $this,
                    Yii::$app->user->identity->getShortName() . ' changed due date on ' . $this->name . ' (' . $this->legacy_id . ')',
                    '<span class="mention" data-id="' . Yii::$app->user->identity->id . '">' . Yii::$app->user->identity->getShortName() . '</span> changed due date on ' . Html::a($this->name,
                        $this->getUrl())
                );
            }

            if (array_key_exists('status', $changesAttributes) && Yii::$app->user->id !== $this->creator->id) {
                NotificationService::getInstance()->addNotification(
                    $this->creator,
                    $this,
                    Yii::$app->user->identity->getShortName() . ' changed due date on ' . $this->name . ' (' . $this->legacy_id . ')',
                    '<span class="mention" data-id="' . Yii::$app->user->identity->id . '">' . Yii::$app->user->identity->getShortName() . '</span> changed due date on ' . Html::a($this->name,
                        $this->getUrl())
                );
            }
        } catch (\Swift_TransportException $e) {
            \Yii::error($e->getMessage());
        }
        if (array_key_exists('status',
                $changesAttributes) && $this->status != $changesAttributes['status'] && $this->status == JobStatus::MANAGER_REVIEW
        ) {
            foreach (User::find()->leftJoin('user_processing_unit', 'user_id=id')->where([
                'role' => UserRoles::REVIEWER,
                'user_processing_unit.processing_unit_id' => $this->processing_unit_id
            ])->all() as $item) {
                $message = Yii::$app->mailer->compose('to_review',
                    ['model' => $this, 'user' => $item]);
                $message->setFrom('no-reply@scemanagement.com');
                $message->setTo($item->email);
                $message->setSubject('(External):CX Project Tracker Job Needs Review');
                Yii::$app->mailer->send($message);
            }
        }
    }

    public function getRelations()
    {
        return [
            'cccContact',
            'creator',
            'iwcmPublishingAssignee',
            'projectLead',
            'projectManager',
            'workTypes',
            'jobCostCenters',
            'jobFiles',
            'jobInvoices',
            'jobTranslations',
            'jobLinks',
            'cwa',
            'processingUnit',
            'requestType'
        ];
    }

    public function getUrl()
    {
        return Yii::$app->urlManager->createAbsoluteUrl(['/jobs/jobs/update', 'id' => $this->id]);
    }

    public function resetCustomFields()
    {
        JobCustomFields::deleteAll(['job_id' => $this->id]);

        if (!empty($this->requestType) && !empty($this->requestType->customForm) && !empty($this->requestType->customForm->customFormFields)) {
            foreach ($this->requestType->customForm->customFormFields as $f) {
                $field = new JobCustomFields();
                $field->job_id = $this->id;
                $field->label = $f->label;
                $field->type = $f->type;
                $field->options = $f->options;
                $field->save();
            }
        }
    }

    public function beforeDelete()
    {
        foreach ($this->jobFiles as $file) {
            $file->delete();
        }
        return parent::beforeDelete();
    }

    public function countInvoices($cwa)
    {
        return collect($this->jobInvoices)->sum(function ($item) use ($cwa) {
            /** @var $item JobInvoice */
            return $item->cwa_id == $cwa ? 1 : 0;
        });
    }

    public function totalInvoices($cwa)
    {
        return collect($this->jobInvoices)
            ->filter(function ($item) use ($cwa) {
                return $cwa == $item->cwa_id;
            })
            ->sum(function ($item) use ($cwa) {
                /** @var $item JobInvoice */
                return $item->amount;
            });
    }
}