<?php
namespace app\modules\jobs\controllers;

use app\components\Controller;
use app\models\OldJob;
use app\modules\jobs\models\OldDatabaseSearch;
use yii\web\NotFoundHttpException;

class ArchiveController extends Controller
{
    public function actionIndex()
    {

    }

    public function actionOldIndex()
    {
        $model = new OldDatabaseSearch();
        $dataProvider = $model->search(\Yii::$app->request->get());
        return $this->render('old_index', ['dataProvider' => $dataProvider, 'filterModel' => $model]);
    }

    public function actionViewOld($id)
    {
        $model = OldJob::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        return $this->render('view_old', ['model' => $model]);
    }
}
