<?php

namespace app\modules\jobs\controllers;

use app\components\Controller;
use app\models\CustomForm;
use app\models\RequestType;
use app\modules\jobs\models\CustomFormForm;
use http\Exception\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CustomFormsController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', ['dataProvider' => new ActiveDataProvider(['query' => CustomForm::find()])]);
    }

    public function actionCreate()
    {
        return $this->actionUpdate(0, true);
    }

    public function actionUpdate($id, $new = false)
    {
        if ($new) {
            $model = new CustomForm();
        } else {
            $model = CustomForm::findOne($id);
        }

        if (!$model) {
            throw new NotFoundHttpException('Custom Form not found');
        }

        $form = new CustomFormForm();
        $form->setModel($model);

        if (\Yii::$app->request->isPost) {
            $form->load(\Yii::$app->request->post());
            if ($form->save()) {
                \Yii::$app->session->setFlash('success', 'Object updated');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', ['model' => $form]);
    }

    public function actionGenerate(array $ids)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $out = [];

        foreach ($ids as $item) {
            $req = RequestType::findOne($item);
            if (!$req) {
                throw new InvalidArgumentException('Request type #' . $item . ' not found');
            }
            $model = $req->customForm;

            $out[$req->id] = [
                'name' => $req->name,
                'alert' => $req->alert,
                'html' => $this->renderAjax('generated_form', ['model' => $model, 'req' => $req])
            ];
        }

        return $out;
    }
}
