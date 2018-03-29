<?php
namespace app\modules\notifications\models;

use app\models\Notifications;
use yii\base\Model;

class NotificationsSearchForm extends Model
{
    public $message;
    public $date;

    public function rules()
    {
        return [
            [['message', 'date'], 'safe']
        ];
    }

    public function search($params)
    {
        $query = Notifications::find()->where(['user_id' => \Yii::$app->user->id]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['date' => SORT_DESC]
            ]
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'message', $this->message]);

        if (!empty($this->date)) {
            $query->andFilterWhere(['DATE(date)' => date('Y-m-d', strtotime($this->date))]);
        }

        return $dataProvider;
    }
}
