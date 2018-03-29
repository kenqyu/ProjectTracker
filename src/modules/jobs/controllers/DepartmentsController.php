<?php

namespace app\modules\jobs\controllers;

use app\components\Controller;
use app\models\Departments;
use app\models\OrganizationUnit;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DepartmentsController extends Controller
{
    public function actionIndex($organization_unit)
    {
        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider([
                'query' => Departments::find()->where(['organization_unit_id' => $organization_unit])
            ]),
            'organization_unit' => $organization_unit,
        ]);
    }

    public function actionCreate($organization_unit)
    {
        return $this->actionUpdate($organization_unit, 0, true);
    }

    public function actionUpdate($organization_unit, $id, $new = false)
    {
        if ($new) {
            $model = new Departments();
            $model->organization_unit_id = $organization_unit;
        } else {
            $model = Departments::findOne($id);
        }

        if (!$model) {
            throw new NotFoundHttpException('Department not found');
        }

        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', 'Object updated');
                return $this->redirect(['index', 'organization_unit' => $organization_unit]);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = Departments::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Department not found');
        }

        if ($model->delete()) {
            \Yii::$app->session->setFlash('success', 'Object removed');
        } else {
            \Yii::$app->session->setFlash('error', 'Error while deleting');
        }
        return $this->redirect(['index', 'organization_unit' => $model->organization_unit_id]);
    }

    public function actionGet($organizationUnit)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if (!OrganizationUnit::findOne($organizationUnit)) {
            throw new NotFoundHttpException('Organization Unit not found');
        }

        return ArrayHelper::map(Departments::find()->where(['organization_unit_id' => $organizationUnit])->all(),
            'id', 'name');
    }
}
