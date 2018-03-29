<?php
/**
 * @var $this \yii\web\View
 */
$this->title = 'Sub Departments';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a href="<?= \yii\helpers\Url::to(['create', 'department' => Yii::$app->request->get('department')]) ?>"
               class="btn btn-primary">Create sub department</a>
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
                                        'department' => Yii::$app->request->get('department')
                                    ]) . ' ' .
                                \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                    [
                                        'delete',
                                        'id' => $model->id,
                                        'department' => Yii::$app->request->get('department')
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
