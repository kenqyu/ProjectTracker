<?php
/**
 * @var $this \yii\web\View
 * @var $column array
 * @var $rowId string
 * @var $columnId string
 */
?>
<?php
$processingUnits = \app\models\ProcessingUnit::getDataList();
$requestTypes = \app\models\RequestType::getDataList($column['processing_unit'], true);
if (!empty($requestTypes)) {
    $fields = \yii\helpers\ArrayHelper::map(
        \app\models\RequestType::findOne($column['request_type'])->customForm->customFormFields,
        'label',
        'label'
    );
} else {
    $fields = [];
}
?>
<label for="processing_unit_<?= $columnId ?>">Processing Unit</label>
<?= \yii\bootstrap\Html::dropDownList(
    'BuilderForm[content][rows][' . $rowId . '][columns][' . $columnId . '][processing_unit]',
    $column['processing_unit'],
    $processingUnits,
    ['class' => 'form-control processing_unit', 'id' => 'processing_unit_' . $columnId]
) ?>

<label for="request_type_<?= $columnId ?>">Request Type</label>
<?= \yii\bootstrap\Html::dropDownList(
    'BuilderForm[content][rows][' . $rowId . '][columns][' . $columnId . '][request_type]',
    $column['request_type'],
    $requestTypes,
    ['class' => 'form-control request_type', 'id' => 'request_type_' . $columnId]
) ?>

<label for="field_<?= $columnId ?>">Field</label>
<?= \yii\bootstrap\Html::dropDownList(
    'BuilderForm[content][rows][' . $rowId . '][columns][' . $columnId . '][field]',
    $column['field'],
    $fields,
    ['class' => 'form-control custom_field', 'id' => 'field_' . $columnId]
) ?>
<?= \yii\bootstrap\Html::dropDownList(
    'BuilderForm[content][rows][' . $rowId . '][columns][' . $columnId . '][status_filter]',
    $column['status_filter'] ?? '',
    [
        '' => 'All Statuses'
    ] + \app\models\enums\JobStatus::getDataList(),
    ['class' => 'form-control custom_field', 'id' => 'field_' . $columnId]
) ?>
