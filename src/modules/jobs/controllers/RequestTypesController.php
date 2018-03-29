<?php

namespace app\modules\jobs\controllers;

use app\components\Controller;
use app\models\ProcessingUnit;
use app\models\RequestType;
use app\modules\jobs\models\RequestTypeForm;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RequestTypesController extends Controller
{
    public function actionIndex($processing_unit)
    {
        if (!ProcessingUnit::findOne($processing_unit)) {
            throw  new NotFoundHttpException('Processing Department not found');
        }
        return $this->render('index',
            [
                'dataProvider' => new ActiveDataProvider([
                        'query' => RequestType::find()->where(['processing_unit_id' => $processing_unit]),
                        'sort' => [
                            'defaultOrder' => ['sort' => SORT_ASC]
                        ]
                    ]
                )
            ]);
    }

    public function actionCreate($processing_unit)
    {
        return $this->actionUpdate(0, $processing_unit, true);
    }

    public function actionUpdate($id, $processing_unit, $new = false)
    {
        if (!ProcessingUnit::findOne($processing_unit)) {
            throw  new NotFoundHttpException('Processing Department not found');
        }

        if ($new) {
            $model = new RequestType();
            $model->processing_unit_id = $processing_unit;
        } else {
            $model = RequestType::findOne($id);
        }

        if (!$model) {
            throw new NotFoundHttpException('Request Type not found');
        }

        $form = new RequestTypeForm();
        $form->setModel($model);

        if (\Yii::$app->request->isPost) {
            $form->load(\Yii::$app->request->post());
            if ($form->save()) {
                \Yii::$app->session->setFlash('success', 'Object updated');
                return $this->redirect(['index', 'processing_unit' => $processing_unit]);
            }
        }

        return $this->render('update', ['model' => $form]);
    }

    public function actionDelete($id)
    {
        $model = RequestType::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Request Type not found');
        }

        if ($model->delete()) {
            \Yii::$app->session->setFlash('success', 'Object removed');
        } else {
            \Yii::$app->session->setFlash('error', 'Error while deleting');
        }
        return $this->redirect(['index', 'processing_unit' => $model->processing_unit_id]);
    }

    public function actionGet($processing_unit)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if (!ProcessingUnit::findOne($processing_unit)) {
            throw new NotFoundHttpException('Processing Department not found');
        }

        return ArrayHelper::map(RequestType::find()->orderBy(['sort' => SORT_ASC])->where(['processing_unit_id' => $processing_unit])->all(),
            'id', function (RequestType $item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'order' => $item->sort
                ];
            });
    }
}
