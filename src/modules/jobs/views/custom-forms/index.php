<?php
/**
 * @var $this \yii\web\View
 */
use kartik\grid\GridView;

$this->title = 'Custom Forms';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a href="<?= \yii\helpers\Url::to(['create']) ?>" class="btn btn-primary">Create form</a>
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
                    '{toggleData}'
                ],
                'columns' => [
                    'name',
                    [
                        'class' => \yii\grid\ActionColumn::class,
                        'template' => '{update} {delete}',
                        'options' => [
                            'style' => 'width: 50px'
                        ]
                    ]
                ]
            ])
            ?>
        </div>
    </div>
</div>
