<?php
namespace app\modules\notifications\controllers;

use app\components\Controller;
use app\models\Notifications;
use app\modules\notifications\models\NotificationsSearchForm;
use yii\web\NotFoundHttpException;

class NotificationsController extends Controller
{
    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'mark-as-read' => ['POST'],
            'mark-all-as-read' => ['POST'],
        ];
    }

    public function actionIndex()
    {
        $model = new NotificationsSearchForm();
        $dataProvider = $model->search(\Yii::$app->request->get());
        return $this->render('index', ['searchModel' => $model, 'dataProvider' => $dataProvider]);
    }

    public function actionMarkAsRead($id)
    {
        $model = Notifications::findOne($id);
        if (!$model || $model->user_id != \Yii::$app->user->id) {
            throw new NotFoundHttpException('Notification not found');
        }

        $model->read = true;
        $model->save(false, ['read']);
        return $this->redirect($model->job->getUrl());
    }

    public function actionMarkAllAsRead()
    {
        $model = Notifications::find()->where(['user_id' => \Yii::$app->user->id])->all();
        foreach ($model as $item) {
            $item->read = true;
            $item->save(false, ['read']);
        }
        if (\Yii::$app->request->isAjax) {
            return 1;
        }
        return $this->redirect(['index']);
    }
}
