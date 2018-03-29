<?php
/**
 * @var $this \yii\web\View
 */
$this->title = 'Request Types';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a href="<?= \yii\helpers\Url::to([
                'create',
                'processing_unit' => Yii::$app->request->get('processing_unit')
            ]) ?>"
               class="btn btn-primary">Create request type</a>
            <br>
            <br>
            <?= \app\widgets\Alert::widget() ?>
            <?= /** @var \yii\data\ActiveDataProvider $dataProvider */
            \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'id',
                    'name',
                    [
                        'header' => 'Actions',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                    [
                                        'update',
                                        'id' => $model->id,
                                        'processing_unit' => Yii::$app->request->get('processing_unit')
                                    ]) . ' ' .
                                \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                    [
                                        'delete',
                                        'id' => $model->id,
                                        'processing_unit' => Yii::$app->request->get('processing_unit')
                                    ], [
                                        'data-confirm' => 'Are you sure you want to delete this item?'
                                    ]);
                        }
                    ]
                ]
            ])
            ?>
        </div>
    </div>
</div>
