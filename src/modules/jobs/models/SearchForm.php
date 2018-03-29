<?php
/**
 * Created by PhpStorm.
 * User: tys
 * Date: 8/5/16
 * Time: 11:27 AM
 */
namespace app\modules\jobs\models;

use app\models\Job;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SearchForm extends Model
{
    public $term;

    public function rules()
    {
        return [
            [['term'], 'safe']
        ];
    }

    public function search($params)
    {
        $query = Job::find()->withAll();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

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

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->orFilterWhere(['like', 'job.name', $this->term])
            ->orFilterWhere(['like', 'job.description', $this->term])
            ->orFilterWhere(['like', 'job.legacy_id', $this->term]);

        return $dataProvider;
    }
}