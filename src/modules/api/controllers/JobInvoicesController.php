<?php
namespace app\modules\api\controllers;

use app\components\ActiveApiController;
use app\models\JobInvoice;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class JobInvoicesController extends ActiveApiController
{
    public $modelClass = JobInvoice::class;

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['update'], $actions['create'], $actions['index']);

        return $actions;
    }

    public function actionCreate($job_id)
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = new JobInvoice();
        $model->job_id = $job_id;

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute(['index', 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    public function actionUpdate($job_id, $id)
    {
        $model = JobInvoice::findOne($id);

        if (!$model || $model->job_id != $job_id) {
            throw new NotFoundHttpException("Object not found: $id");
        }

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }

    public function actionIndex($job_id)
    {
        return new ActiveDataProvider([
            'query' => JobInvoice::find()->where(['job_id' => $job_id]),
        ]);
    }
}
