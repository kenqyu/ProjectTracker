<?php
namespace app\modules\api\controllers;

use app\components\ApiController;
use app\models\forms\JobFileForm;
use app\models\JobFile;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class JobFilesController extends ApiController
{
    public function actionIndex($job_id)
    {
        return new ActiveDataProvider([
            'query' => JobFile::find()->where(['job_id' => $job_id])
        ]);
    }

    public function actionCreate($job_id)
    {
        $model = new JobFileForm();
        $model->model = new JobFile();
        $model->load(\Yii::$app->request->post());
        $model->job_id = $job_id;

        if ($model->save()) {
            \Yii::$app->response->setStatusCode(201);
        }

        return $model;
    }

    public function actionUpdate($job_id, $id)
    {
        $model = JobFile::find()->where(['job_id' => $job_id, 'id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }
        $form = new JobFileForm();
        $form->model = $model;
        $form->setAttributes($model->getAttributes());
        $form->load(\Yii::$app->request->getBodyParams());
        $form->job_id = $job_id;

        if ($form->save()) {
            \Yii::$app->response->setStatusCode(201);
        }

        return $model;
    }

    public function actionUpload($job_id, $id)
    {
        $model = JobFile::find()->where(['job_id' => $job_id, 'id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }
        $form = new JobFileForm();
        $form->scenario = 'upload';
        $form->model = $model;
        $form->setAttributes($model->getAttributes());
        $form->file = UploadedFile::getInstanceByName('file');

        if ($form->save()) {
            \Yii::$app->response->setStatusCode(201);
        }

        return $model;
    }

    public function actionDelete($job_id, $id)
    {
        $model = JobFile::find()->where(['job_id' => $job_id, 'id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        \Yii::$app->getResponse()->setStatusCode(204);
    }
}
