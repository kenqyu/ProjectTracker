<?php
namespace app\services;

use app\models\Job;
use app\models\Notifications;
use app\models\User;
use app\traits\Singleton;
use yii\helpers\ArrayHelper;

class NotificationService
{
    use Singleton;

    public function addNotification(User $user, Job $job, $title, $message)
    {
        $model = new Notifications();
        $model->user_id = $user->id;
        $model->job_id = $job->id;
        $model->message = $message;
        $model->title = $title;
        $model->save();
    }

    /**
     * @param User $user
     * @param int $limit
     * @return \app\models\Notifications[]
     */
    public function getNotifications(User $user, int $limit)
    {
        $out = Notifications::find()->where([
            'user_id' => $user->id,
            'read' => false
        ])->orderBy(['date' => SORT_DESC])->all();
        if (count($out) < $limit) {
            $read = Notifications::find()->where([
                'user_id' => $user->id,
                'read' => true
            ])->limit($limit - count($out))->orderBy(['date' => SORT_DESC])->all();
            $out = array_merge($out, $read);
        }
        return $out;
    }

    /**
     * @param User $user
     * @return int
     */
    public function getUnreadCount(User $user)
    {
        return Notifications::find()->where([
            'user_id' => $user->id,
            'read' => false
        ])->count();
    }
}
