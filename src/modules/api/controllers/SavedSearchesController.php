<?php
namespace app\modules\api\controllers;

use app\components\ActiveApiController;
use app\models\SavedSearch;
use yii\data\ActiveDataProvider;

class SavedSearchesController extends ActiveApiController
{
    public $modelClass = SavedSearch::class;

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function prepareDataProvider()
    {
        return new ActiveDataProvider([
            'query' => SavedSearch::find()->andWhere(['user_id' => \Yii::$app->user->id])
        ]);
    }
}
