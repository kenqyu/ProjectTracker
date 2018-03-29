<?php
/**
 * @var $this \yii\web\View
 * @var $column array
 * @var $rowId string
 * @var $columnId string
 */
?>
<?= \yii\bootstrap\Html::dropDownList(
    'BuilderForm[content][rows][' . $rowId . '][columns][' . $columnId . '][field]',
    $column['field'],
    \app\modules\reports\models\enums\NativeField::getDataList(),
    ['class' => 'form-control']
) ?>
<?= \yii\bootstrap\Html::dropDownList(
    'BuilderForm[content][rows][' . $rowId . '][columns][' . $columnId . '][status_filter]',
    $column['status_filter'] ?? '',
    [
        '' => 'All Statuses'
    ] + \app\models\enums\JobStatus::getDataList(),
    ['class' => 'form-control custom_field', 'id' => 'field_' . $columnId]
) ?>