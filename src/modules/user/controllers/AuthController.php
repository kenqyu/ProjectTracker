<?php

namespace app\modules\user\controllers;

use app\models\Departments;
use app\models\forms\LoginForm;
use app\models\forms\RegisterForm;
use app\models\OrganizationUnit;
use app\models\SubDepartment;
use app\models\User;
use app\modules\user\models\ForgotPasswordForm;
use app\modules\user\models\ResetPasswordForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AuthController extends Controller
{
    public function actionLogin()
    {
        $this->layout = '@app/views/layouts/simple';
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model
        ]);

    }

    public function actionRegister()
    {
        $this->layout = '@app/views/layouts/simple';
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegisterForm();
        if ($model->load(\Yii::$app->request->post()) && $model->register()) {
            \Yii::$app->session->addFlash('success',
                'Thank you! Your request will be reviewed and approved within 24 hours.');
            return $this->goBack();
        }
        return $this->render('register', [
            'model' => $model
        ]);
    }

    public function actionForgotPassword()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = '@app/views/layouts/simple';

        $model = new ForgotPasswordForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            /** @var User $user */
            $user = $model->getUser();
            if ($user !== null && $user->getStatus() !== 0) {
                $user->generatePasswordResetToken();
                $user->save(false, ['forgot_password_hash']);
                $mailer = \Yii::$app->mailer;
                $mailer->compose("forgot_password", ['model' => $user])
                    ->setSubject("(External):CX Project Tracker Password Reset")
                    ->setFrom('no-reply@scemanagement.com')
                    ->setTo([$user->email => $user->getShortName()])
                    ->send();
                \Yii::$app->getSession()->setFlash('success',
                    ' You will receive an email with instructions on how to reset your password in a few minutes.');
                return $this->redirect(['login']);
            }
            $model->addError('email', 'Account does not exist or is disabled');
        }
        return $this->render('forgot-password', ['model' => $model,]);
    }

    public function actionResetPassword($hash)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = '@app/views/layouts/simple';

        $model = new ResetPasswordForm($hash);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->resetPassword()) {
                \Yii::$app->getSession()->setFlash('success', 'Password changed.');
                return $this->redirect(['login']);
            }
        }
        return $this->render('reset-password', ['model' => $model]);
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionGetDepartments($organizationUnit)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if (!OrganizationUnit::findOne($organizationUnit)) {
            throw new NotFoundHttpException('Organization Unit not found');
        }

        return ArrayHelper::map(
            Departments::find()
                ->where(['organization_unit_id' => $organizationUnit])
                ->all(),
            'id',
            'name'
        );
    }

    public function actionGetSubDepartments($department)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Departments::findOne($department)) {
            throw new NotFoundHttpException('Department not found');
        }

        return ArrayHelper::map(
            SubDepartment::find()
                ->where(['department_id' => $department])
                ->all(),
            'id',
            'name'
        );
    }
}
