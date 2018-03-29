<?php
namespace app\modules\api\controllers;

use app\components\ApiController;
use app\models\Job;
use app\models\Notifications;
use app\models\Subscription;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class NotificationsController extends ApiController
{
    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => Notifications::find()->currentUser()
        ]);
    }

    public function actionMarkAsRead($id)
    {
        $model = Notifications::find()->currentUser()->andWhere(['id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException('Notification not found');
        }

        if ($model->markAsRead()) {
            \Yii::$app->response->setStatusCode(201);
        }
        return $model;
    }

    public function actionSubscribe($job_id)
    {
        $job = Job::findOne($job_id);
        if (!$job) {
            throw new NotFoundHttpException('Job not found');
        }
        \Yii::$app->user->identity->subscribe($job);
        return true;
    }

    public function actionUnSubscribe($job_id)
    {
        $job = Job::findOne($job_id);
        if (!$job) {
            throw new NotFoundHttpException('Job not found');
        }
        \Yii::$app->user->identity->unSubscribe($job);
        return true;
    }

    public function actionIsSubscribed($job_id)
    {
        return [
            'subscribed' => Subscription::find()->where([
                'user_id' => \Yii::$app->user->id,
                'job_id' => $job_id
            ])->exists()
        ];
    }
}
