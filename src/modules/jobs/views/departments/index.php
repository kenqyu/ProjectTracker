<?php
/**
 * @var $this \yii\web\View
 * @var $organization_unit integer
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use yii\helpers\Url;

$this->title = 'Departments';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a href="<?= \yii\helpers\Url::to(['create', 'organization_unit' => $organization_unit]) ?>"
               class="btn btn-primary">Create departments</a>
            <br>
            <br>
            <?= \app\widgets\Alert::widget() ?>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'id',
                    'name',
                    [
                        'header' => 'Sub Departments',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \yii\helpers\Html::a('Sub Departments',
                                ['sub-departments/index', 'department' => $model->id]);
                        }
                    ],
                    [
                        'class' => \yii\grid\ActionColumn::class,
                        'template' => '{update} {delete}',
                        'urlCreator' => function (string $action, \app\models\Departments $model, $key) use (
                            $organization_unit
                        ) {
                            $params = is_array($key) ? $key : [
                                'id' => (string)$key,
                                'organization_unit' => $organization_unit
                            ];
                            $params[0] = $action;

                            return Url::toRoute($params);
                        }
                    ]
                ]
            ])
            ?>
        </div>
    </div>
</div>
