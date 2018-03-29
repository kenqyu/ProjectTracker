<?php
/**
 * @var $this \yii\web\View
 * @var $model array
 * @var $dates \DateTime[]
 * @var $rootModel \app\models\Report
 * @var $width integer
 */

use app\models\RequestType;
use app\modules\reports\services\CustomFieldReportService;
use app\modules\reports\services\HardcodedFieldReportService;

$data = [];

$additionalFilters = [];
if (!empty($model['status_filter'])) {
    $additionalFilters['job.status'] = $model['status_filter'];
}
collect($rootModel->getDecodedFilters())->each(function ($item) use (&$additionalFilters) {
    $additionalFilters[$item['field']] = $item['value'];
});
$isPercent = false;
if ($model['type'] != \app\modules\reports\models\enums\SectionType::TEXTAREA) {
    if ($model['field_type'] == \app\modules\reports\models\enums\FieldType::HARDCODED) {
        $s = new HardcodedFieldReportService(
            \app\modules\reports\models\enums\NativeField::getByValue($model['field'])->field(),
            $dates[0],
            $dates[1],
            $additionalFilters + ($rootModel->processing_unit_id > 0 ? ['processing_unit_id' => $rootModel->processing_unit_id] : []),
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

<div class="column" data-width="<?= intval($width) ?>" style="float: left;width: <?= $width ?>%">
    <div class="column_title"
         style="background: #4D863D;color: #fff;text-align: center;font-size: 22px;padding: 5px;font-weight: bold;">
        <?= $model['name'] ?>
    </div>

    <div class="content" style="text-align: center; margin-top: 10px;">
        <?php
        switch ($model['type']) {
            case \app\modules\reports\models\enums\SectionType::TEXTAREA:
                echo 'TEXT';
                break;
            case \app\modules\reports\models\enums\SectionType::PIE_CHART:
                echo $this->render('__pie_chart', ['data' => $data, 'width' => $width]);
                break;
            case \app\modules\reports\models\enums\SectionType::BAR_CHART:
                echo $this->render('__bar_chart', ['data' => $data, 'isPercent' => $isPercent, 'width' => $width]);
                break;

        }
        ?>
    </div>
</div>
