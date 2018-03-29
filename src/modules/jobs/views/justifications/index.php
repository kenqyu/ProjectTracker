<?php
/**
 * @var $this \yii\web\View
 */
$this->title = 'Justifications';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a href="<?= \yii\helpers\Url::to(['create']) ?>" class="btn btn-primary">Create justification</a>
            <br>
            <br>
            <?= \app\widgets\Alert::widget() ?>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'id',
                    'name',
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
