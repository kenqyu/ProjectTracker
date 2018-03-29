<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
use kartik\grid\GridView;

$this->title = 'CWA Invoices';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?= \app\widgets\Alert::widget() ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'panel' => [
                    'type' => GridView::TYPE_DEFAULT,
                ],
                'export' => false,
                'toolbar' => [
                    '{toggleData}'
                ],
                'columns' => [
                    [
                        'attribute' => 'job.name',
                        'header' => 'Project Name',
                        'value' => function ($model) {
                            return \yii\helpers\Html::a($model->job->name, $model->job->getUrl());
                        },
                        'format' => 'raw',
                        'options' => [
                            'style' => 'width: 50%'
                        ]
                    ],
                    [
                        'attribute' => 'number',
                        'header' => 'Invoice Number'
                    ],
                    [
                        'attribute' => 'date',
                        'header' => 'Invoice Date',
                        'format' => 'date'
                    ],
                    [
                        'attribute' => 'amount',
                        'header' => 'Invoice Amount',
                        'format' => 'currency'
                    ],
                ]
            ])
            ?>
        </div>
    </div>
</div>
