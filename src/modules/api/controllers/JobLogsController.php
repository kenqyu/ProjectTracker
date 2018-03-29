<?php
namespace app\modules\api\controllers;

use app\components\ApiController;
use app\models\JobLog;
use yii\data\ActiveDataProvider;

class JobLogsController extends ApiController
{
    public function actionIndex($job_id)
    {
        return new ActiveDataProvider([
            'query' => JobLog::find()->with('user')->where(['job_id' => $job_id])->orderBy(['id' => SORT_DESC])
        ]);
    }
}
