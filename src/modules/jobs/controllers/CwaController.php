<?php

namespace app\modules\jobs\controllers;

use app\components\Controller;
use app\models\Agency;
use app\models\CWA;
use app\models\Job;
use app\models\JobInvoice;
use app\modules\jobs\models\CwaForm;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class CwaController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider([
                'query' => CWA::find(),
                'sort' => ['defaultOrder' => ['number' => SORT_DESC]]
            ])
        ]);
    }

    public function actionExport()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => CWA::find(),
            'sort' => ['defaultOrder' => ['number' => SORT_DESC]]
        ]);

        $query = $dataProvider->query;
        if ($dataProvider->getSort() !== null) {
            $query->addOrderBy($dataProvider->getSort()->getOrders());
        }

        /**
         * @var $models CWA[]
         */
        $models = $query->all();

        $doc = new PHPExcel();
        $doc->setActiveSheetIndex(0);

        $out = [
            [
                'Number',
                'Used',
                'Balance'
            ]
        ];

        foreach ($models as $model) {
            $used = $model->getTotalUsage();
            $out[] = [
                $model->number,
                $used,
                $model->amount - $used
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


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="sce_management_cwa_export_' . date('m.d.Y_h-i') . '.xls"');
        header('Cache-Control: max-age=0');

        // Do your stuff here
        $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

        $writer->save('php://output');
    }

    public function actionCreate()
    {
        return $this->actionUpdate(0, true);
    }

    public function actionUpdate($id, $new = false)
    {
        if ($new) {
            $model = new CwaForm();
        } else {
            $model = CwaForm::findOne($id);
        }

        if (!$model) {
            throw new NotFoundHttpException('CWA not found');
        }

        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', 'Object updated');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = CWA::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('CWA not found');
        }

        if ($model->delete()) {
            \Yii::$app->session->setFlash('success', 'Object removed');
        } else {
            \Yii::$app->session->setFlash('error', 'Error while deleting');
        }
        return $this->redirect(['index']);
    }

    public function actionInvoices($id)
    {
        $model = CWA::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('CWA not found');
        }

        return $this->render('invoices', [
            'dataProvider' => new ActiveDataProvider([
                'query' => JobInvoice::find()->joinWith('job')->where(['in','job_id',collect(Job::find()->where(['cwa_id'=>$model->id])->all())->pluck('id')]),
                'sort' => ['defaultOrder' => ['number' => SORT_DESC]]
            ])
        ]);
    }
}
