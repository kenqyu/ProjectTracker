<?php
/**
 * @var $this \yii\web\View
 */
use kartik\grid\GridView;

$this->title = 'CWA';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <a href="<?= \yii\helpers\Url::to(['create']) ?>" class="btn btn-primary">Create CWA</a>
            <br>
            <br>
            <?= \app\widgets\Alert::widget() ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
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
                'columns' => [
                    'number',
                    'amount:currency',
                    [
                        'header' => 'Invoiced to Date',
                        'value' => function ($model) {
                            return '$' . number_format($model->amount - $model->getBalance(),
                                    2) . ' (' . \yii\helpers\Html::a('invoices',
                                    ['invoices', 'id' => $model->id]) . ')';
                        },
                        'format' => 'html'
                    ],
                    [
                        'header' => 'Balance',
                        'value' => function ($model) {
                            return $model->getBalance();
                        },
                        'format' => 'currency'
                    ],
                    [
                        'header' => 'Progress',
                        'options' => [
                            'style' => 'width: 40%'
                        ],
                        'value' => function ($model) {
                            /** @var $model \app\models\CWA */
                            $progress = 0;
                            if ($model->amount > 0) {
                                $progress = number_format(($model->amount - $model->getBalance()) / ($model->amount / 100),
                                    2);
                            }
                            return \yii\bootstrap\Html::tag('div',
                                \yii\bootstrap\Html::tag('div', number_format($progress, 0) . '%', [
                                    'class' => 'progress-bar',
                                    'style' => 'min-width: 2em; width: ' . number_format($progress, 0) . '%'
                                ])
                                , ['class' => 'progress accounting_progress', 'style' => 'margin-bottom: 0']);
                        },
                        'format' => 'raw'
                    ],
                    'due_date:date',
                    [
                        'class' => \yii\grid\ActionColumn::class,
                        'template' => '{update} {delete}'
                    ]
                ]
            ])
            ?>
        </div>
    </div>
</div>
