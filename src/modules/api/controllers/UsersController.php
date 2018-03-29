<?php
namespace app\modules\api\controllers;

use app\components\ActiveApiController;
use app\models\forms\UserForm;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class UsersController extends ActiveApiController
{
    public $modelClass = User::class;

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['create'], $actions['update']);

        return $actions;
    }

    public function actionCreate()
    {
        $model = new User();
        $form = new UserForm();
        $form->model = $model;
        $form->scenario = 'create';
        $form->setAttributes(\Yii::$app->request->getBodyParams());
        if (!$form->validate()) {
            return $form;
        }
        if ($form->save()) {
            \Yii::$app->response->setStatusCode(201);
        }

        return $model;
    }

    public function actionUpdate($id)
    {
        $model = User::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('User not found');
        }
        $form = new UserForm();
        $form->model = $model;
        $form->setAttributes($model->getAttributes());
        $form->setAttributes(\Yii::$app->request->getBodyParams());
        if (!$form->validate()) {
            return $form;
        }
        if ($form->save()) {
            \Yii::$app->response->setStatusCode(201);
        }

        return $model;
    }

    public function actionSuggest()
    {
        if (strlen(\Yii::$app->request->getBodyParam('query')) >= 3) {
            return new ActiveDataProvider([
                'query' => User::find()->where(['LIKE', 'username', \Yii::$app->request->getBodyParam('query')])
            ]);
        }
        return [];
    }
}
