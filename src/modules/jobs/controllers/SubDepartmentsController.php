<?php

namespace app\modules\jobs\controllers;

use app\components\Controller;
use app\models\Departments;
use app\models\RequestType;
use app\models\SubDepartment;
use app\modules\jobs\models\RequestTypeForm;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SubDepartmentsController extends Controller
{
    public function actionIndex($department)
    {
        if (!Departments::findOne($department)) {
            throw  new NotFoundHttpException('Department not found');
        }
        return $this->render('index',
            [
                'dataProvider' => new ActiveDataProvider([
                        'query' => SubDepartment::find()->where(['department_id' => $department])
                    ]
                )
            ]);
    }

    public function actionCreate($department)
    {
        return $this->actionUpdate(0, $department, true);
    }

    public function actionUpdate($id, $department, $new = false)
    {
        if (!Departments::findOne($department)) {
            throw  new NotFoundHttpException('Department not found');
        }

        if ($new) {
            $model = new SubDepartment();
            $model->department_id = $department;
        } else {
            $model = SubDepartment::findOne($id);
        }

        if (!$model) {
            throw new NotFoundHttpException('Sub Department not found');
        }

        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', 'Object updated');
                return $this->redirect(['index', 'department' => $department]);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = SubDepartment::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Request Type not found');
        }

        if ($model->delete()) {
            \Yii::$app->session->setFlash('success', 'Object removed');
        } else {
            \Yii::$app->session->setFlash('error', 'Error while deleting');
        }
        return $this->redirect(['index', 'department' => $model->department_id]);
    }

    public function actionGet($department)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Departments::findOne($department)) {
            throw new NotFoundHttpException('Department not found');
        }

        return ArrayHelper::map(SubDepartment::find()->where(['department_id' => $department])->all(),
            'id', 'name');
    }
}
