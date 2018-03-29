<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
use app\models\Job;
use kartik\grid\GridView;

\app\modules\jobs\assets\JobsAsset::register($this);

$this->title = 'Search results';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <a href="javascript:window.history.back();" class="btn"><i class="fa fa-chevron-left"></i> Back</a>
                    <h1>Search results for "<?= \yii\bootstrap\Html::encode($searchModel->term) ?>"</h1>
                </div>
            </div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'toolbar' => [
                    '{export}',
                    '{toggleData}'
                ],
                'export' => [
                    'icon' => false,
                    'label' => 'Export'
                ],
                'exportConfig' => [
                    GridView::CSV => [],
                    GridView::TEXT => [],
                    GridView::EXCEL => []
                ],
                'panel' => [
                    'type' => GridView::TYPE_DEFAULT,
                ],
                'columns' => [
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function ($item) {
                            return \yii\bootstrap\Html::a($item->name,
                                ['update', 'id' => $item->id, 'list_view' => 1]);
                        }
                    ],
                    [
                        'attribute' => 'legacy_id'
                    ],
                    [
                        'attribute' => 'projectLead',
                        'value' => 'projectLead.shortName'
                    ],
                    [
                        'attribute' => 'projectManager',
                        'value' => 'projectManager.shortName'
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \app\models\enums\JobStatus::getByValue($model->status)->getLabel();
                        }
                    ],
                    [
                        'attribute' => 'due_date',
                        'format' => ['date', 'php:m/d/Y']
                    ],
                    [
                        'attribute' => 'creator',
                        'value' => 'creator.shortName'
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:m/d/Y h:i a']
                    ],
                    [
                        'header' => 'Cost Centers',
                        'format' => 'raw',
                        'value' => function ($model) {
                            /**
                             * @var $model Job
                             */
                            return collect($model->jobCostCenters)
                                ->map(function ($item) {
                                    return $item->cost_center;
                                })
                                ->implode(', ');
                        }
                    ],
                    [
                        'class' => \yii\grid\ActionColumn::class,
                        'template' => '{update}'
                    ]
                ]
            ]); ?>
        </div>
    </div>
</div>
