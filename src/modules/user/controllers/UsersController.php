<?php
namespace app\modules\user\controllers;

use app\components\Controller;
use app\components\PusherComponent;
use app\models\User;
use app\modules\user\models\UserSearch;
use app\modules\user\models\UserUpdateForm;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{
    public function actionIndex()
    {
        $model = new UserSearch();
        return $this->render('index', [
            'dataProvider' => $model->search(\Yii::$app->request->get()),
            'model' => $model
        ]);
    }

    public function actionUpdate($id)
    {
        $model = User::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('User not found');
        }

        $form = new UserUpdateForm();
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

    public function actionApprove($id)
    {
        $model = User::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('User not found');
        }

        if ($model->approved) {
            \Yii::$app->session->setFlash('error', 'User already activated');
        }

        if ($model->activate()) {
            \Yii::$app->session->setFlash('success', 'User activated');
        } else {
            \Yii::$app->session->setFlash('error', 'Error');
        }

        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionDecline($id)
    {
        $model = User::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('User not found');
        }

        if ($model->approved) {
            \Yii::$app->session->setFlash('error', 'User already activated');
        }

        if ($model->decline()) {
            \Yii::$app->session->setFlash('success', 'User declined');
        } else {
            \Yii::$app->session->setFlash('error', 'Error');
        }

        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionMadeDefaultCCCContact($id)
    {
        $model = User::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('User not found');
        }

        if (!$model->approved) {
            \Yii::$app->session->setFlash('error', 'User not activated');
        } else {
            if ($model->madeDefaultCCCContact()) {
                \Yii::$app->session->setFlash('success', 'User now default CCC Contact');
            } else {
                \Yii::$app->session->setFlash('error', 'User cant be default default CCC contact without CCC Contact type.');
            }
        }

        return $this->redirect(['update', 'id' => $id]);
    }
}
