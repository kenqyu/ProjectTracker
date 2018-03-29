<?php
/**
 * @var $this \yii\web\View
 * @var $model array
 * @var $rootModel \app\models\Report
 */
?>


<div class="row report_row">
    <div class="col-md-12">
        <?php if (!empty($model['name'])) { ?>
            <div class="row_title">
                <?= $model['name'] ?>
            </div>
        <?php } ?>
        <div class="columns">
            <?php
            if (isset($model['columns'])) {
                foreach ($model['columns'] as $column) {
                    echo $this->render('_column', [
                        'rootModel' => $rootModel,
                        'model' => $column,
                        'dates' => $dates,
                        'width' => 100 / count($model['columns'])
                    ]);
                }
            }
            ?>
        </div>
    </div>
</div>