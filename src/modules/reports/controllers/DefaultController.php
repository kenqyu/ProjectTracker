<?php

namespace app\modules\reports\controllers;

use app\components\Controller;
use app\models\enums\JobStatus;
use app\models\Job;
use app\models\ProcessingUnit;
use app\models\Report;
use app\models\RequestType;
use app\modules\reports\services\CustomFieldReportService;
use app\modules\reports\services\HardcodedFieldReportService;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
    public function actionIndex($id = null, $date_range = null)
    {
        if ($id !== null) {
            $model = Report::findOne($id);
            if (!$model) {
                throw new NotFoundHttpException('Report not found');
            }
        } else {
            $model = Report::find()->where(['owner_id' => \Yii::$app->user->id])->orWhere(['public' => 1])->one();
            if(!$model)
                return $this->redirect(['builder/create']);
        }

        /** @var $dates \DateTime[] $dates */
        $dates = [
            new \DateTime(date('1-m-Y')),
            new \DateTime()
        ];
        if ($date_range) {
            $exploded = explode(' - ', $date_range);
            $dates = [
                new \DateTime($exploded[0]),
                new \DateTime($exploded[1])
            ];
        }

        $totalJobs = Job::find()
            ->where(['>=', 'DATE(created_at)', $dates[0]->format('Y-m-d')])
            ->andWhere(['<=', 'DATE(created_at)', $dates[1]->format('Y-m-d')]);


        $processingUnitModel = null;
        if ($model && $model->processing_unit_id) {
            $processingUnitModel = ProcessingUnit::findOne($model->processing_unit_id);
            $totalJobs->andWhere(['processing_unit_id' => $processingUnitModel->id]);
        }


        return $this->render('view',
            [
                'model' => $model,
                'dates' => $dates,
                'processingUnit' => $processingUnitModel,
                'totalJobs' => $totalJobs->count(),
                'totalCompletedJobs' => $totalJobs->andWhere(['status' => JobStatus::COMPLETED])->count(),
            ]);
    }
}
