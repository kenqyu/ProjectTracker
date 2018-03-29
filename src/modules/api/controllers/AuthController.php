<?php
namespace app\modules\api\controllers;

use app\models\forms\LoginForm;
use app\models\forms\RegisterForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class AuthController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'register' => ['post'],
                    'login' => ['post']
                ],
            ],
        ]);
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        $model->setAttributes(\Yii::$app->request->getBodyParams());
        if ($model->login()) {
            \Yii::$app->getResponse()->setStatusCode(201);
        }

        return $model;
    }

    public function actionRegister()
    {
        $model = new RegisterForm();
        $model->setAttributes(\Yii::$app->request->getBodyParams());
        if ($model->register()) {
            \Yii::$app->getResponse()->setStatusCode(201);
        }

        return $model;
    }
}
