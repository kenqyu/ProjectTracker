<?php
/**
 * @var $this \yii\web\View
 */
$this->title = 'Processing Departments';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a href="<?= \yii\helpers\Url::to(['create']) ?>" class="btn btn-primary">Create processing department</a>
            <br>
            <br>
            <?= \app\widgets\Alert::widget() ?>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'id',
                    'name',
                    'order',
                    [
                        'header' => 'Request Types',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \yii\helpers\Html::a('Request Types',
                                ['request-types/index', 'processing_unit' => $model->id]);
                        }
                    ],
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
