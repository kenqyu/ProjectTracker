<?php

namespace app\modules\jobs\models;

use app\models\enums\UserRoles;
use app\models\Job;
use Yii;
use yii\base\Model;

class JobSearchForm extends Model
{
    public $name;
    public $legacy_id;
    public $projectLead;
    public $projectManager;
    public $cmsAssignee;
    public $translationManager;
    public $processing_unit;
    public $status;
    public $due_date;
    public $creator;
    public $created_at;
    public $updated_at;
    public $group_id;

    public function rules()
    {
        return [
            [['name', 'legacy_id'], 'string'],
            [
                [
                    'projectLead',
                    'projectManager',
                    'cmsAssignee',
                    'translationManager',
                    'creator',
                    'status',
                    'group_id',
                    'processing_unit'
                ],
                'integer'
            ],
            [['due_date', 'created_at', 'updated_at'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'processing_unit' => 'Processing Department',
            'cmsAssignee' => 'CMS Assignee'
        ];
    }

    public function search($params)
    {
        $model = Job::find()->joinWith([
            'creator creator',
            'projectLead projectLead',
            'projectManager projectManager',
            'translationManager translationManager',
            'processingUnit processingUnit',
            'iwcmPublishingAssignee iwcmPublishingAssignee'
        ]);

        if (
            Yii::$app->user->identity->role >= UserRoles::GENERAL
        ) {
            if (Yii::$app->session->get('all_projects', 0) == 0) {
                $model->orWhere(['creator_id' => Yii::$app->user->id]);
                $model->orWhere(['project_lead_id' => Yii::$app->user->id]);
                $model->orWhere(['project_manager_id' => Yii::$app->user->id]);
                $model->orWhere(['translation_manager_id' => Yii::$app->user->id]);
                $model->orWhere(['iwcm_publishing_assignee_id' => Yii::$app->user->id]);
                $model->orWhere(['ccc_contact_id' => Yii::$app->user->id]);
            } else {
                $model->andWhere(['processing_unit_id' => Yii::$app->user->identity->getProcessingUnitsIds()]);
            }
        } elseif (Yii::$app->user->identity->role == UserRoles::GENERAL) {
            $model->orWhere(['creator_id' => Yii::$app->user->id]);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $dataProvider->sort->attributes['creator'] = [
            'asc' => ['creator.first_name' => SORT_ASC],
            'desc' => ['creator.first_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['projectLead'] = [
            'asc' => ['projectLead.first_name' => SORT_ASC],
            'desc' => ['projectLead.first_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['projectManager'] = [
            'asc' => ['projectManager.first_name' => SORT_ASC],
            'desc' => ['projectManager.first_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['cmsAssignee'] = [
            'asc' => ['iwcmPublishingAssignee.first_name' => SORT_ASC],
            'desc' => ['iwcmPublishingAssignee.first_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['translationManager'] = [
            'asc' => ['translationManager.first_name' => SORT_ASC],
            'desc' => ['translationManager.first_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['processing_unit'] = [
            'asc' => ['processing_unit.name' => SORT_ASC],
            'desc' => ['processing_unit.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $model->andFilterWhere(['like', 'job.name', $this->name]);
        $model->andFilterWhere(['like', 'legacy_id', $this->legacy_id]);
        $model->andFilterWhere(['project_lead_id' => $this->projectLead]);
        $model->andFilterWhere(['project_manager_id' => $this->projectManager]);
        $model->andFilterWhere(['iwcm_publishing_assignee_id' => $this->cmsAssignee]);
        $model->andFilterWhere(['processing_unit_id' => $this->processing_unit]);
        $model->andFilterWhere(['creator_id' => $this->creator]);
        $model->andFilterWhere(['job.status' => $this->status]);
        $model->andFilterWhere(['job.job_group_id' => $this->group_id]);

        if (!empty($this->created_at)) {
            $model->andFilterWhere(['DATE(job.created_at)' => date('Y-m-d', strtotime($this->created_at))]);
        }
        if (!empty($this->updated_at)) {
            $model->andFilterWhere(['DATE(job.updated_at)' => date('Y-m-d', strtotime($this->updated_at))]);
        }
        if (!empty($this->due_date)) {
            $model->andFilterWhere(['DATE(job.due_date)' => date('Y-m-d', strtotime($this->due_date))]);
        }

        return $dataProvider;
    }
}
