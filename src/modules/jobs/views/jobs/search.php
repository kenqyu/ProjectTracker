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
                    <h1>Search results</h1>
                </div>
                <div class="col-md-6 text-right">
                    <a href="#save_search" data-toggle="modal" class="btn btn-lg btn-info">Save search</a>
                    <a href="#search" data-toggle="modal" class="btn btn-lg btn-info">View/Change search filters</a>
                </div>
            </div>

            <?php
            \yii\bootstrap\Modal::begin([
                'id' => 'search',
                'header' => 'Search'
            ]);
            ?>
            <?php \yii\bootstrap\Modal::end() ?>

            <div class="row">
                <div class="col-md-12">
                    <?= \app\widgets\Alert::widget() ?>
                </div>
            </div>

            <?php
            \yii\bootstrap\Modal::begin([
                'id' => 'save_search',
                'header' => 'Save Search'
            ]);
            $model = new \app\models\SavedSearch();
            $model->data = json_encode(Yii::$app->request->get('filter', []));
            $form = \yii\bootstrap\ActiveForm::begin(['action' => ['save-search']]);
            echo $form->field($model, 'title');
            echo $form->field($model, 'data')->hiddenInput()->label(false);
            echo \yii\bootstrap\Html::submitButton('Save', ['class' => 'btn btn-primary']);
            \yii\bootstrap\ActiveForm::end();
            ?>
            <?php \yii\bootstrap\Modal::end() ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'export' => false,
                'toolbar' => [
                    \yii\bootstrap\Html::a('Export to Excel', array_merge(['export'], Yii::$app->request->get()),
                        ['class' => 'btn btn-default']),
                    '{toggleData}'
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
                        'attribute' => 'translationManager',
                        'value' => 'translationManager.shortName'
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
                        'label' => 'Created On',
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:m/d/Y h:i a']
                    ],
                    [
                        'label' => 'Updated On',
                        'attribute' => 'updated_at',
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
