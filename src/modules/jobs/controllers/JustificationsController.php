<?php
namespace app\modules\jobs\controllers;

use app\components\Controller;
use app\models\Justifications;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class JustificationsController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', ['dataProvider' => new ActiveDataProvider(['query' => Justifications::find()])]);
    }

    public function actionCreate()
    {
        return $this->actionUpdate(0, true);
    }

    public function actionUpdate($id, $new = false)
    {
        if ($new) {
            $model = new Justifications();
        } else {
            $model = Justifications::findOne($id);
        }

        if (!$model) {
            throw new NotFoundHttpException('Work Type not found');
        }

        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', 'Object updated');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = Justifications::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Work Type not found');
        }

        if ($model->delete()) {
            \Yii::$app->session->setFlash('success', 'Object removed');
        } else {
            \Yii::$app->session->setFlash('error', 'Error while deleting');
        }
        return $this->redirect(['index']);
    }
}
