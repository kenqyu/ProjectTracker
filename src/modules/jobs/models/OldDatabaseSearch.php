<?php
namespace app\modules\jobs\models;

use app\models\OldJob;
use yii\base\Model;

class OldDatabaseSearch extends Model
{
    public $number;
    public $name;
    public $submitted_by;
    public $dce_lead;
    public $submit_date;
    public $status;

    public function rules()
    {
        return [
            [['number', 'name', 'submitted_by', 'dce_lead', 'submit_date', 'status'], 'safe']
        ];
    }

    public function search($params)
    {
        $model = OldJob::find();

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC,]],
            'pagination' => [
                'pageSize' => 100
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $model->andFilterWhere(['like', 'number', $this->number]);
        $model->andFilterWhere(['like', 'name', $this->name]);
        $model->andFilterWhere(['submitted_by' => $this->submitted_by]);
        $model->andFilterWhere(['dce_lead' => $this->dce_lead]);
        $model->andFilterWhere(['status' => $this->status]);

        if (!empty($this->submit_date)) {
            $model->andFilterWhere(['DATE(submit_date)' => date('Y-m-d', strtotime($this->submit_date))]);
        }

        return $dataProvider;
    }
}
