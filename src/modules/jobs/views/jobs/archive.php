<?php
/**
 * @var $this \yii\web\View
 */
use app\models\enums\UserRoles;
use app\models\Job;

$this->title = 'Archives';
\app\modules\jobs\assets\JobsAsset::register($this);
?>
<div class="container-fluid">
    <div class="jobs_archive index">
        <div class="row">
            <div class="col-md-12">
                <?= \app\widgets\Alert::widget() ?>
                <h2>Archives</h2>
            </div>
        </div>
        <div class="row lists">
            <?php
            echo '<div class="col-md-12">';
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function ($item) {
                            return \yii\bootstrap\Html::a($item->name,
                                ['update', 'id' => $item->id, 'list_view' => 1]);
                        }
                    ],
                    ['attribute' => 'legacy_id'],
                    [
                        'attribute' => 'project_lead_id',
                        'header' => 'Project Lead',
                        'filter' => \app\models\User::getDataList(false, \app\models\enums\UserTypes::PROJECT_LEAD),
                        'value' => function ($model) {
                            if ($model->projectLead) {
                                return $model->projectLead->shortName;
                            }
                            return null;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'filter' => \app\models\enums\JobStatus::getDataList(),
                        'value' => function ($model) {
                            return \app\models\enums\JobStatus::getByValue($model->status)->getLabel();
                        }
                    ],
                    [
                        'attribute' => 'completed_on',
                        'format' => ['date', 'php:m/d/Y'],
                        'filter' => \app\widgets\DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'completed_on',
                            'value' => !empty($searchModel->completed_on) ? date('m/d/Y',
                                strtotime($searchModel->completed_on)) : '',
                            'clientOptions' => [
                                'clearBtn' => true
                            ]
                        ])
                    ],
                    [
                        'attribute' => 'creator.shortName',
                        'header' => 'Creator'
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:m/d/Y h:i a'],
                        'filter' => \app\widgets\DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'created_at',
                            'value' => !empty($searchModel->created_at) ? date('m/d/Y',
                                strtotime($searchModel->created_at)) : '',
                            'clientOptions' => [
                                'clearBtn' => true
                            ]
                        ])
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
                        'template' => '{update}',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return \yii\helpers\Url::to([$action, 'id' => $key, 'list_view' => 1]);
                        }
                    ]
                ]
            ]);
            echo '</div>';
            ?>
        </div>
    </div>
</div>