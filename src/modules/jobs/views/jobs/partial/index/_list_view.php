<?php
/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $searchModel \app\modules\jobs\models\JobSearchForm
 */

use app\models\Job;
use app\widgets\DatePicker;
use kartik\grid\GridView;

echo '<div class="col-md-12">';
echo GridView::widget([
    'panel' => [
        'type' => GridView::TYPE_DEFAULT,
    ],
    'export' => false,
    'toolbar' => [
        \yii\bootstrap\Html::a('Export to Excel',
            array_merge(['export'], Yii::$app->request->get()),
            ['class' => 'btn btn-default']),
        '{toggleData}'
    ],
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
            'attribute' => 'projectLead',
            'value' => 'projectLead.shortName',
            'filter' => \app\models\User::getDataList(false,
                \app\models\enums\UserTypes::PROJECT_LEAD)
        ],
        [
            'attribute' => 'projectManager',
            'value' => 'projectManager.shortName',
            'filter' => \app\models\User::getDataList(false,
                \app\models\enums\UserTypes::PROJECT_MANAGER)
        ],
        [
            'attribute' => 'cmsAssignee',
            'label' => 'CMS Assignee',
            'value' => 'iwcmPublishingAssignee.shortName',
            'filter' => \app\models\User::getDataList(false,
                \app\models\enums\UserTypes::IWCM_PUBLISHING_ASSIGNEE)
        ],
        /*[
            'attribute' => 'translationManager',
            'value' => 'translationManager.shortName',
            'filter' => \app\models\User::getDataList(false,
                \app\models\enums\UserTypes::TRANSLATION_MANAGER)
        ],*/
        [
            'attribute' => 'processing_unit',
            'label' => 'Processing Department',
            'value' => 'processingUnit.name',
            'filter' => \app\models\ProcessingUnit::getDataList(false, '',
                Yii::$app->user->identity->getProcessingUnitsIds())
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function ($model) {
                return \app\models\enums\JobStatus::getByValue($model->status)->getLabel();
            },
            'filter' => \app\models\enums\JobStatus::getDataList()
        ],
        [
            'attribute' => 'due_date',
            'format' => ['date', 'php:m/d/Y'],
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'due_date',
                'value' => !empty($searchModel->due_date) ? date('m/d/Y',
                    strtotime($searchModel->due_date)) : '',
                'clientOptions' => [
                    'clearBtn' => true
                ]
            ])
        ],
        [
            'attribute' => 'creator',
            'label' => 'Requestor',
            'value' => 'creator.shortName',
            'filter' => \app\models\User::getDataList(false, null, true)
        ],
        [
            'attribute' => 'created_at',
            'format' => ['date', 'php:m/d/Y h:i a'],
            'filter' => DatePicker::widget([
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
            'attribute' => 'updated_at',
            'format' => ['date', 'php:m/d/Y h:i a'],
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'updated_at',
                'value' => !empty($searchModel->updated_at) ? date('m/d/Y',
                    strtotime($searchModel->updated_at)) : '',
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
            'template' => '{update} {delete}',
            'urlCreator' => function ($action, $model, $key, $index) {
                return \yii\helpers\Url::to([$action, 'id' => $key, 'list_view' => 1]);
            },
            'headerOptions' => [
                'width' => '80'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'buttons' => [
                'delete' => function ($url, $item) {
                    if (Yii::$app->user->identity->role === \app\models\enums\UserRoles::ADMIN) {
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', '#',
                            [
                                'class' => 'delete_job',
                                'data-legacy-id' => $item->legacy_id,
                                'title' => 'Delete request',
                                'rel' => 'tooltip'
                            ]);
                    }
                    return '';
                }
            ]
        ]
    ]
]);
echo '</div>';