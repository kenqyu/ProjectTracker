<?php
/**
 * @var $this \yii\web\View
 */
$this->title = 'Notifications';
\app\modules\jobs\assets\JobsAsset::register($this);
?>
<div class="container-fluid">
    <div class="jobs_archive index">
        <div class="row">
            <div class="col-md-12">
                <?= \app\widgets\Alert::widget() ?>
                <h2>Notifications</h2>
            </div>
        </div>
        <div class="row lists">
            <?php
            echo '<div class="col-md-12">';
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'date',
                        'format' => 'datetime',
                        'options' => [
                            'width' => '200'
                        ],
                        'filter' => \app\widgets\DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'date',
                            'value' => !empty($searchModel->date) ? date('m/d/Y',
                                strtotime($searchModel->date)) : '',
                            'clientOptions' => [
                                'clearBtn' => true
                            ]
                        ])
                    ],
                    [
                        'attribute' => 'message',
                        'format' => 'raw',
                        'value' => function ($item) {
                            return ($item->read ? '' : '<span class="label label-success">NEW</span> ') . $item->message;
                        }
                    ]
                ]
            ]);
            echo '</div>';
            ?>
        </div>
    </div>
</div>