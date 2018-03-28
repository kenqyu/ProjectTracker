<?php
namespace app\commands;

use app\models\enums\JobStatus;
use app\models\Job;
use app\models\Notifications;
use app\models\User;
use app\services\NotificationService;
use Yii;
use yii\bootstrap\Html;
use yii\console\Controller;
use yii\db\Expression;

class NotificationController extends Controller
{
    public function actionCron()
    {
        $model = collect(Notifications::find()->needToInformed()->orderBy(['id' => SORT_DESC])->all());
        foreach ($model->groupBy('user_id') as $key => $item) {
            if (count($item) > 0) {
                /**
                 * @var $item Notifications[]
                 * @var $user User
                 */
                $user = User::findOne($key);
                if ($user->no_mails) {
                    continue;
                }
                $message = Yii::$app->mailer->compose('notifications', ['items' => $item, 'user' => $user]);
                $message->setFrom('no-reply@scemanagement.com');
                $message->setTo([$user->email => $user->getShortName()]);
                $message->setSubject('(External):CX Project Tracker Updates');
                Yii::$app->mailer->send($message);

                foreach ($item as $i) {
                    $i->markAsSent();
                }
            }
        }
    }

    public function actionDueCron()
    {
        $jobs = Job::find()
            ->where([
                'NOT IN',
                'status',
                [JobStatus::COMPLETED, JobStatus::CANCELED]
            ])
            ->andWhere(new Expression('DATE(due_date) = DATE(DATE_ADD(NOW(), INTERVAL 48 HOUR))'))->all();

        foreach ($jobs as $job) {
            $sent = [];
            NotificationService::getInstance()->addNotification(
                $job->creator,
                $job,
                $job->name . ' due in 2 days',
                Html::a($job->name, $job->getUrl()) . ' due in 2 days'
            );
            $sent[] = $job->creator->id;
            if ($job->projectLead && !in_array($job->projectLead->id, $sent)) {
                NotificationService::getInstance()->addNotification(
                    $job->projectLead,
                    $job,
                    $job->name . ' due in 2 days',
                    Html::a($job->name, $job->getUrl()) . ' due in 2 days'
                );
                $sent[] = $job->projectLead->id;
            }
            if ($job->projectManager && !in_array($job->projectManager->id, $sent)) {
                NotificationService::getInstance()->addNotification(
                    $job->projectManager,
                    $job,
                    $job->name . ' due in 2 days',
                    Html::a($job->name, $job->getUrl()) . ' due in 2 days'
                );
                $sent[] = $job->projectManager->id;
            }
            if ($job->iwcmPublishingAssignee && !in_array($job->iwcmPublishingAssignee->id, $sent)) {
                NotificationService::getInstance()->addNotification(
                    $job->iwcmPublishingAssignee,
                    $job,
                    $job->name . ' due in 2 days',
                    Html::a($job->name, $job->getUrl()) . ' due in 2 days'
                );
            }
        }
    }
}
