<?php
namespace app\modules\jobs\models;

use app\models\enums\JobStatus;
use app\models\Job;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ArchiveSearch extends Model
{
    public $name;
    public $legacy_id;
    public $project_lead_id;
    public $status;
    public $completed_on;
    public $creator_id;
    public $created_at;

    public function rules()
    {
        return [
            [
                [
                    'status',
                    'creator_id',
                    'project_lead_id',
                ],
                'integer'
            ],
            [['status'], 'in', 'range' => JobStatus::getKeys()],
            [['completed_on', 'created_at'], 'safe'],
            [['legacy_id', 'name'], 'string', 'max' => 255]
        ];
    }

    public function search($params)
    {
        $query = Job::find()
            ->andWhere(['<', 'completed_on', new \yii\db\Expression('DATE_SUB(NOW(), INTERVAL 30 day)')])
            ->andWhere(['IN', 'status', [JobStatus::COMPLETED, JobStatus::CANCELED]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere(['project_lead_id' => $this->project_lead_id])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['creator_id' => $this->creator_id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'legacy_id', $this->legacy_id]);

        if (!empty($this->completed_on)) {
            $query->andFilterWhere(['DATE(completed_on)' => date('Y-m-d', strtotime($this->completed_on))]);
        }
        if (!empty($this->created_at)) {
            $query->andFilterWhere(['DATE(created_at)' => date('Y-m-d', strtotime($this->created_at))]);
        }

        return $dataProvider;
    }
}
