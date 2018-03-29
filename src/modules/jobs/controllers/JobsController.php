<?php

namespace app\modules\jobs\controllers;

use app\components\Controller;
use app\models\CWA;
use app\models\enums\JobTranslationStatus;
use app\models\enums\Languages;
use app\models\enums\UserRoles;
use app\models\Job;
use app\models\JobComment;
use app\models\JobCostCenter;
use app\models\JobCustomFields;
use app\models\JobFile;
use app\models\JobInvoice;
use app\models\JobTranslation;
use app\models\SavedSearch;
use app\modules\jobs\models\ArchiveSearch;
use app\modules\jobs\models\CreateJobForm;
use app\modules\jobs\models\JobSearchForm;
use app\modules\jobs\models\SearchForm;
use app\modules\jobs\models\UpdateJobForm;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use yii\base\Exception;
use yii\base\InvalidCallException;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class JobsController extends Controller
{
    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'search' => ['POST'],
        ];
    }

    public function actionIndex()
    {
        if (\Yii::$app->user->identity->role >= UserRoles::MANAGER) {
            if (\Yii::$app->request->get('all_projects') != null &&
                \Yii::$app->request->get('all_projects') != \Yii::$app->session->get('all_projects')
            ) {
                \Yii::$app->session->set('all_projects', \Yii::$app->request->get('all_projects'));
            }
        }
        $model = new JobSearchForm();
        $dataProvider = $model->search(\Yii::$app->request->get());
        return $this->render('index', ['dataProvider' => $dataProvider, 'searchModel' => $model]);
    }

    public function actionGroup($id)
    {
        $model = new JobSearchForm();
        $dataProvider = $model->search(ArrayHelper::merge(\Yii::$app->request->get(),
            ['JobSearchForm' => ['group_id' => $id]]));
        return $this->render('group', ['dataProvider' => $dataProvider, 'searchModel' => $model]);
    }

    public function actionArchive()
    {
        $model = new ArchiveSearch();
        $dataProvider = $model->search(\Yii::$app->request->get());
        return $this->render('archive', ['dataProvider' => $dataProvider, 'searchModel' => $model]);
    }

    public function actionTextSearch()
    {
        $model = new SearchForm();
        $dataProvider = $model->search(\Yii::$app->request->get());
        return $this->render('text-search', ['dataProvider' => $dataProvider, 'searchModel' => $model]);
    }

    public function actionSearch()
    {
        return $this->render('search', [
            'dataProvider' => $this->prepareSearchDataProvider()
        ]);
    }

    public function actionExport()
    {
        if (\Yii::$app->request->get('filter')) {
            $dataProvider = $this->prepareSearchDataProvider();
        } else {
            $dataProvider = (new JobSearchForm())->search(\Yii::$app->request->get());
        }
        $query = $dataProvider->query;
        if ($dataProvider->getSort() !== null) {
            $query->addOrderBy($dataProvider->getSort()->getOrders());
        }

        /**
         * @var $models Job[]
         */
        $models = $query->all();

        $doc = new PHPExcel();
        $doc->setActiveSheetIndex(0);

        $out = [
            [
                'Name',
                'ID',
                'Project Lead',
                'Project Manager',
                'Status',
                'Due Date',
                'Creator',
                'Created At',
                'Updated At',
                'Cost Centers',
                'Links'
            ]
        ];

        foreach ($models as $model) {
            $out[] = [
                $model->name,
                $model->legacy_id,
                $model->projectLead ? $model->projectLead->getFullName() : '',
                $model->projectManager ? $model->projectManager->getFullName() : '',
                \app\models\enums\JobStatus::getByValue($model->status)->text(),
                \Yii::$app->formatter->asDate($model->due_date),
                $model->creator->getFullName(),
                \Yii::$app->formatter->asDatetime($model->created_at),
                \Yii::$app->formatter->asDatetime($model->updated_at),
                collect($model->jobCostCenters)
                    ->map(function ($item) {
                        return $item->cost_center;
                    })
                    ->implode(', '),
                collect($model->jobLinks)
                    ->map(function ($item) {
                        return $item->link;
                    })
                    ->implode("\n")
            ];
        }

        $doc->getActiveSheet()->fromArray($out);
        $doc->getActiveSheet()->getStyle('A1:' . $doc->getActiveSheet()->getHighestDataColumn() . '1')
            ->getFont()->setBold(true);
        $doc->getActiveSheet()->getStyle('A1:' . $doc->getActiveSheet()->getHighestDataColumn() . '1')
            ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $doc->getActiveSheet()->getStyle('A1:' . $doc->getActiveSheet()->getHighestDataColumn() . $doc->getActiveSheet()->getHighestRow())
            ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        foreach (range('A', $doc->getActiveSheet()->getHighestDataColumn()) as $col) {
            $doc->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }
        $doc->getActiveSheet()->setAutoFilter('A1:' . $doc->getActiveSheet()->getHighestDataColumn() . '1');
        $doc->getActiveSheet()->getStyle('K1:K' . $doc->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="sce_management_export_' . date('m.d.Y_h-i') . '.xls"');
        header('Cache-Control: max-age=0');

        // Do your stuff here
        $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

        $writer->save('php://output');
    }

    private function prepareSearchDataProvider()
    {
        $model = new Job();
        $query = Job::find()->withAll();

        foreach (\Yii::$app->request->get('filter', []) as $item) {
            $rule = [];

            $split = mbsplit('\.', $item['field']);
            if (count($split) > 2) {
                throw new InvalidParamException('Invalid field: ' . $item['field']);
            }
            if (count($split) == 2) {
                if ($split[0] != 'job' && !in_array($split[0], $model->getRelations())
                ) {
                    throw new InvalidParamException('Cant find \'' . $split[0] . '\' relation');
                }
            }

            switch ($item['type']) {
                case 'match':
                    $rule = [$item['field'] => $item['value']];
                    break;
                case 'like':
                    $rule = ['like', $item['field'], $item['value']];
                    break;
                case 'in':
                    $rule = ['in', $item['field'], $item['value']];
                    break;
                case 'not in':
                    $rule = ['not in', $item['field'], $item['value']];
                    break;
                case 'between':
                    $rule = ['between', $item['field'], $item['value']];
                    break;
                case 'range':
                    $rule = $item['field'] . ' >= ' . intval($item['value'][0]) . ' AND ' . $item['field'] . '<= ' . intval($item['value'][1]);
                    break;
                case 'daterange':
                    $rule = $item['field'] . ' >= \'' . date('Y-m-d', strtotime($item['value'][0])) . '\'';
                    if (!empty($item['value'][1])) {
                        $rule .= ' AND ' . $item['field'] . '<= \'' . date('Y-m-d',
                                strtotime($item['value'][1])) . '\'';
                    }
                    break;
                case 'boolean':
                    $rule = [$item['field'] => !!$item['value']];
                    break;
                case 'null':
                    $rule = [$item['field'] => null];
                    break;
                case 'not_null':
                    $rule = ['IS NOT', $item['field'], null];
                    break;
                case '>':
                    $rule = ['>', $item['field'], $item['value']];
                    break;
                case '>=':
                    $rule = ['>=', $item['field'], $item['value']];
                    break;
                case '<':
                    $rule = ['<', $item['field'], $item['value']];
                    break;
                case '<=':
                    $rule = ['<=', $item['field'], $item['value']];
                    break;
            }
            $query->orWhere($rule);
        }
        $query->groupBy(['id']);

        \Yii::$app->user->setReturnUrl(Url::current());

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $dataProvider->sort->attributes['creator'] = [
            'asc' => ['creator.first_name' => SORT_ASC],
            'desc' => ['creator.first_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['projectLead'] = [
            'asc' => ['projectLead.first_name' => SORT_ASC],
            'desc' => ['projectLead.first_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['translationManager'] = [
            'asc' => ['translationManager.first_name' => SORT_ASC],
            'desc' => ['translationManager.first_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['projectManager'] = [
            'asc' => ['projectManager.first_name' => SORT_ASC],
            'desc' => ['projectManager.first_name' => SORT_DESC],
        ];
        return $dataProvider;
    }

    public function actionSaveSearch()
    {
        $model = new SavedSearch();
        $model->load(\Yii::$app->request->post());
        if ($model->save()) {
            \Yii::$app->session->setFlash('success', 'Search saved');
        } else {
            \Yii::$app->session->setFlash('error', 'Error');
        }

        return $this->goBack();
    }

    public function actionCreate()
    {
        $form = new CreateJobForm();
        $model = new Job();
        $model->loadDefaultValues();
        $model->created_at = new Expression('NOW()');
        $model->updated_at = new Expression('NOW()');
        if (\Yii::$app->request->isPost) {
            $form->load(\Yii::$app->request->post());
            $form->files = UploadedFile::getInstances($form, 'files');
            if ($form->validate() && $form->save()
            ) {
                return $this->redirect(['index']);
            }
        }

        throw new InvalidCallException(json_encode($model->getErrors() + $form->getErrors()));
    }

    public function actionUpdate($id)
    {
        $model = Job::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        $form = new UpdateJobForm();
        $form->setModel($model);
        $form->setAttributes($model->getAttributes(null, ['departments']));

        if (\Yii::$app->request->isPost) {
            $form->load(\Yii::$app->request->post());
            $form->files = UploadedFile::getInstances($form, 'files');
            if ($form->validate() && $form->save()) {
                if (\Yii::$app->request->post('reset_custom_fields') == 1) {
                    $model->resetCustomFields();
                }
                \Yii::$app->session->addFlash('success', 'Job updated');
                return $this->redirect($form->model->getUrl());
            }
        }

        return $this->render('update', ['model' => $form]);
    }

    public function actionGetInvoices($job_id, $cwa_id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return collect(JobInvoice::find()->where(['cwa_id' => $cwa_id, 'job_id' => $job_id])->all())->map(function (
            $item
        ) {
            $item->date = \Yii::$app->formatter->asDate($item->date, 'short');
            return $item;
        });
    }

    public function actionAddInvoice()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new JobInvoice();
        $model->load(\Yii::$app->request->post());

        if (!Job::findOne($model->job_id)) {
            throw new NotFoundHttpException('Object not found');
        }


        $model->user_id = \Yii::$app->user->id;
        if ($model->save()) {
            return $model;
        }
        throw new Exception('Error adding object');
    }

    public function actionDeleteInvoice($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $model = JobInvoice::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        if ($model->delete()) {
            return true;
        } else {
            throw new Exception('Error while deleting');
        }
    }

    public function actionAddCostCenter()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new JobCostCenter();
        $model->load(\Yii::$app->request->post());

        if (!Job::findOne($model->job_id)) {
            throw new NotFoundHttpException('Object not found');
        }

        if ($model->save()) {
            return $model;
        }
        throw new Exception('Error adding object');
    }

    public function actionDeleteCostCenter($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $model = JobCostCenter::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        if ($model->delete()) {
            return true;
        } else {
            throw new Exception('Error while deleting');
        }
    }

    public function actionAddCwa($job_id, $cwa_id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = CWA::findOne($cwa_id);
        $job = Job::findOne($job_id);
        if (!$model || !$job) {
            throw new NotFoundHttpException('Object not found');
        }


        $job->link('cwa', $model);

        if ($job->save()) {
            return ['status' => 'success'];
        }
        throw new Exception('Error adding object');
    }

    public function actionDeleteCwa($job_id, $cwa_id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $job = Job::findOne($job_id);
        $model = CWA::findOne($cwa_id);

        if (!$model || !$job) {
            throw new NotFoundHttpException('Object not found');
        }

        $job->unlink('cwa', $model, true);

        if ($job->save()) {
            return true;
        } else {
            throw new Exception('Error while deleting');
        }
    }

    public function actionAddTranslation()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new JobTranslation();
        $model->load(\Yii::$app->request->post());
        $model->due_date = date('Y-m-d', strtotime($model->due_date));

        if (!Job::findOne($model->job_id)) {
            throw new NotFoundHttpException('Object not found');
        }

        if ($model->save()) {
            $out = $model->toArray();
            $out['language'] = Languages::getByValue($out['language'])->text();
            $out['rush'] = (int)$out['rush'];
            $out['due_date'] = date('m/d/Y', strtotime($out['due_date']));
            return $out;
        }
        throw new Exception('Error adding object');
    }

    public function actionUpdateTranslationStatus($id, $status)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $model = JobTranslation::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }
        JobTranslationStatus::getByValue($status);

        $model->status = $status;

        if ($model->save()) {
            return true;
        } else {
            throw new Exception('Error while updating');
        }
    }

    public function actionDeleteTranslation($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $model = JobTranslation::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        if ($model->delete()) {
            return true;
        } else {
            throw new Exception('Error while deleting');
        }
    }

    public function actionFileUpload($job_id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Job::findOne($job_id);
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        $form = new UpdateJobForm();
        $form->setModel($model);
        $form->files = [UploadedFile::getInstanceByName('file')];
        $form->saveFiles();

        return 1;
    }

    public function actionAddComment($job_id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Job::findOne($job_id);
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        $comment = new JobComment();
        $comment->body = \Yii::$app->request->post('body');
        $comment->public = !!\Yii::$app->request->post('public', true);
        $comment->job_id = $job_id;
        $comment->user_id = \Yii::$app->user->id;
        if ($comment->save()) {
            return $comment;
        }
        throw new Exception('Error while saving');
    }

    public function actionGetComments($job_id, $time)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Job::findOne($job_id);
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        if (\Yii::$app->cache->get('comment_block_' . $job_id . '::last_update') > $time) {
            return [
                'timestamp' => time(),
                'content' => $this->renderAjax('partial/update/__comments_list', ['model' => $model])
            ];
        }
        return [
            'no_updates' => true
        ];
    }

    public function actionSubscribe($id)
    {
        $model = Job::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        if (!\Yii::$app->user->identity->subscribed($model)) {
            \Yii::$app->user->identity->subscribe($model);
        }

        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionUnSubscribe($id)
    {
        $model = Job::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        if (\Yii::$app->user->identity->subscribed($model)) {
            \Yii::$app->user->identity->unsubscribe($model);
        }

        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionGetFile($id)
    {
        $model = JobFile::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('File not found');
        }

        \Yii::$app->response->format = Response::FORMAT_RAW;
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $model->title . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($model->getPath()));
        readfile($model->getPath());
        \Yii::$app->end();
    }

    public function actionForceCustomFields($id)
    {
        $model = Job::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        $model->resetCustomFields();

        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionDeleteFile($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if (\Yii::$app->user->identity->role != UserRoles::ADMIN) {
            throw new BadRequestHttpException();
        }

        $model = JobFile::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('File not found');
        }

        if ($model->delete()) {
            return ['status' => 'ok'];
        } else {
            \Yii::$app->response->statusCode = 500;
            return ['status' => 'error'];
        }
    }

    public function actionDelete($id)
    {
        $model = Job::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Object not found');
        }

        if ($model->delete()) {
            \Yii::$app->session->addFlash('success', 'Request ' . $model->legacy_id . ' removed');
        } else {
            \Yii::$app->session->addFlash('danger', 'Error while removing job');
        }
        return $this->redirect(['index']);
    }
}
