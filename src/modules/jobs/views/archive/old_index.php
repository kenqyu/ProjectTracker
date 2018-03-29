<?php
/**
 * @var $this \yii\web\View
 */
use app\models\enums\UserRoles;
use app\models\Job;

$this->title = 'Old Jobs';
?>
<div class="container-fluid">
    <div class="old_jobs index">
        <div class="row">
            <div class="col-md-12">
                <?= \app\widgets\Alert::widget() ?>
            </div>
        </div>
        <div class="row lists">
            <?php
            echo '<div class="col-md-12">';
            $q = \app\models\OldJob::find();
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $filterModel,
                'columns' => [
                    'number',
                    [
                        'attribute' => 'name',
                        'format' => 'html',
                        'value' => function ($item) {
                            return \yii\bootstrap\Html::a($item->name, ['view-old', 'id' => $item->id]);
                        }
                    ],
                    [
                        'attribute' => 'submitted_by',
                        'filter' => collect(\yii\helpers\ArrayHelper::map(\app\models\OldJob::find()->distinct('submitted_by')->all(),
                            'submitted_by', 'submitted_by'))->sort()->all()
                    ],
                    [
                        'attribute' => 'dce_lead',
                        'filter' => collect(\yii\helpers\ArrayHelper::map(\app\models\OldJob::find()->distinct('dce_lead')->all(),
                            'dce_lead', 'dce_lead'))->sort()->all()
                    ],
                    'submit_date',
                    [
                        'attribute' => 'status',
                        'filter' => collect(\yii\helpers\ArrayHelper::map(\app\models\OldJob::find()->distinct('status')->all(),
                            'status', 'status'))->sort()->all()
                    ]
                ]
            ]);
            echo '</div>'; ?>
        </div>
    </div>
</div>