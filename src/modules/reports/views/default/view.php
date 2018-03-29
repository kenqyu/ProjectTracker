<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Report
 * @var $processingUnit \app\models\ProcessingUnit
 * @var $dates \DateTime[]
 * @var $totalJobs integer
 */
\app\modules\reports\assets\ReportAsset::register($this);
?>

<div class="container-fluid report">
    <div class="row">
        <div class="col-md-12">
            <?= \app\widgets\Alert::widget() ?>
        </div>
    </div>
    <div class="row header">
        <div class="col-md-4">
            <div class="report_selector">
                <div class="current"><?= $model->name ?></div>
                <div class="dropdown">
                    <ul>
                        <?php
                        foreach (\app\models\Report::find()->where(['owner_id' => \Yii::$app->user->id])->orWhere(['public' => 1])->all() as $item) {
                            if ($item->id == $model->id) {
                                continue;
                            }
                            echo ' <li><a href="' . \yii\helpers\Url::current(['id' => $item->id]) . '">' . $item->name . '</a></li>';
                        }
                        ?>
                        <li><a href="<?= \yii\helpers\Url::to(['builder/create']) ?>" class="create_report">
                                Create Dashboard
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <p class="processing_unit">
                <?= \app\models\ProcessingUnit::findOne($model->processing_unit_id)->name ?? '' ?>
            </p>

            <form>
                <input type="hidden" name="id" value="<?= $model->id ?>">
                <input id="date_range" name="date_range" class="form-control text-center"
                       value="<?= $dates[0]->format('m/d/Y') ?> - <?= $dates[1]->format('m/d/Y') ?>" readonly>
            </form>

            <p class="total_jobs">
                Total Requests - <?= $totalJobs ?>
            </p>
            <p class="total_jobs">
                Total <span style="text-decoration: underline;">Completed</span> Requests - <?= $totalCompletedJobs ?>
            </p>
        </div>
        <div class="col-md-4 text-right actions">
            <a href="<?= \yii\helpers\Url::to(['builder/clone', 'id' => $model->id]) ?>" class="btn btn-default">Clone
                Dashboard</a>
            <a href="<?= \yii\helpers\Url::to(['builder/update', 'id' => $model->id]) ?>"
               class="btn btn-default">Edit Dashboard</a>
            <a href="<?= \yii\helpers\Url::to([
                'print/index',
                'id' => $model->id,
                'processingUnit' => $processingUnit ? $processingUnit->id : '',
                'date_range' => $dates[0]->format('m/d/Y') . ' - ' . $dates[1]->format('m/d/Y')
            ]) ?>" class="btn btn-primary">Export PDF</a>
        </div>
    </div>

    <?php
    $data = $model->getDecodedContent();
    if (isset($data['rows'])) {
        foreach ($data['rows'] as $item) {
            echo $this->render('partial/_row', ['rootModel' => $model, 'model' => $item, 'dates' => $dates]);
        }
    }
    ?>
</div>