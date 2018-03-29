<?php
/**
 * @var $this \yii\web\View
 * @var $model array
 * @var $rootModel \app\models\Report
 * @var $dates \DateTime[]
 */
?>


<div class="row report_row <?= $page == 1 ? 'first_page_item' : '' ?>">
    <div class="col-md-12">
        <?php if (!empty($model['name'])) { ?>
            <div class="row_title"
                 style="background: #4D863D;color: #fff;text-align: center;font-size: 22px;padding: 10px;font-weight: bold;">
                <?= $model['name'] ?>
            </div>
        <?php } ?>
        <div class="columns">
            <?php
            if (isset($model['columns'])) {
                foreach ($model['columns'] as $column) {
                    echo $this->render('_column', [
                        'model' => $column,
                        'dates' => $dates,
                        'width' => 100 / count($model['columns']),
                        'rootModel' => $rootModel,
                    ]);
                }
            }
            ?>
        </div>
        <div style="width:100%;clear: both"></div>
    </div>
</div>