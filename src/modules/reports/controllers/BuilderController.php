<?php

namespace app\modules\reports\controllers;

use app\components\Controller;
use app\models\Report;
use app\modules\reports\models\BuilderForm;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BuilderController extends Controller
{
    public function actionCreate()
    {
        return $this->actionUpdate(null, true);
    }

    public function actionUpdate($id, $new = false)
    {
        $model = new BuilderForm();
        if (!$new) {
            $report = Report::findOne($id);
            if (!$report) {
                throw new NotFoundHttpException('Report not found');
            }
            $model->setModel($report);
        }

        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            if ($model->validate()) {
                if ($model->save()) {
                    $this->redirect(['default/index', 'id' => $model->getId()]);
                }
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $report = Report::findOne($id);
        if (!$report) {
            throw new NotFoundHttpException('Report not found');
        }

        if ($report->delete()) {
            \Yii::$app->session->setFlash('success', 'Dashboard removed');
        } else {
            \Yii::$app->session->setFlash('error', 'Error while deleting');
        }
        return $this->redirect(['default/index']);
    }

    public function actionClone($id)
    {
        $report = Report::findOne($id);
        if (!$report) {
            throw new NotFoundHttpException('Original report not found');
        }

        $model = new BuilderForm();
        $model->content = $report->getDecodedContent();
        $model->public = $report->public;

        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            if ($model->validate()) {
                if ($model->save()) {
                    $this->redirect(['default/index', 'id' => $model->getId()]);
                }
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionGetRequestTypes($processing_unit)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return \app\models\RequestType::getDataList($processing_unit, true);
    }

    public function actionGetCustomFields($request_type)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return \yii\helpers\ArrayHelper::map(
            \app\models\RequestType::findOne($request_type)->customForm->customFormFields,
            'label',
            'label'
        );
    }
}
