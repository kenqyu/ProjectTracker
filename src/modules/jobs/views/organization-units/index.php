<?php
/**
 * @var $this \yii\web\View
 */
$this->title = 'Organization Units';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a href="<?= \yii\helpers\Url::to(['create']) ?>" class="btn btn-primary">Create organization unit</a>
            <br>
            <br>
            <?= \app\widgets\Alert::widget() ?>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'id',
                    'name',
                    [
                        'header' => 'Departments',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \yii\helpers\Html::a('Departments',
                                ['departments/index', 'organization_unit' => $model->id]);
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
