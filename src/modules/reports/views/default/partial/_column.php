<?php
/**
 * @var $this \yii\web\View
 * @var $model array
 * @var $dates \DateTime[]
 * @var $width integer
 * @var $rootModel \app\models\Report
 */

use app\models\RequestType;
use app\modules\reports\services\CustomFieldReportService;
use app\modules\reports\services\HardcodedFieldReportService;

$data = [];
$isPercent = false;
$additionalFilters = [];
if (!empty($model['status_filter'])) {
    $additionalFilters['job.status'] = $model['status_filter'];
}
collect($rootModel->getDecodedFilters())->each(function ($item) use (&$additionalFilters) {
    $additionalFilters[$item['field']] = $item['value'];
});
if ($model['type'] != \app\modules\reports\models\enums\SectionType::TEXTAREA) {
    if ($model['field_type'] == \app\modules\reports\models\enums\FieldType::HARDCODED) {
        $s = new HardcodedFieldReportService(
            \app\modules\reports\models\enums\NativeField::getByValue($model['field'])->field(),
            $dates[0],
            $dates[1],
            $additionalFilters + ($rootModel->processing_unit_id > 0 ? ['job.processing_unit_id' => $rootModel->processing_unit_id] : []),
            $rootModel->processing_unit_id
        );
        if ($model['field'] == \app\modules\reports\models\enums\NativeField::COMPLETION_TIME_FRAME) {
            $isPercent = true;
        }
    } else {
        $s = new CustomFieldReportService(
            RequestType::findOne($model['request_type']),
            $model['field'],
            $dates[0],
            $dates[1],
            $additionalFilters
        );
    }

    $data = $s->proceed();
}
?>

<div class="column" data-width="<?= number_format($width, 0, '', '') ?>" style="width: <?= intval($width) ?>%">
    <div class="column_title">
        <?= $model['name'] ?>
    </div>

    <div class="content">
        <?php
        switch ($model['type']) {
            case \app\modules\reports\models\enums\SectionType::TEXTAREA:
                echo '<textarea name="" id="" rows="10" class="form-control"></textarea>';
                break;
            case \app\modules\reports\models\enums\SectionType::PIE_CHART:
                echo $this->render('__pie_chart', ['rootModel' => $rootModel, 'data' => $data]);
                break;
            case \app\modules\reports\models\enums\SectionType::BAR_CHART:
                echo $this->render('__bar_chart',
                    ['rootModel' => $rootModel, 'data' => $data, 'isPercent' => $isPercent]);
                break;

        }
        ?>
    </div>
</div>
