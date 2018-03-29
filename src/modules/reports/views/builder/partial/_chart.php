<?php
/**
 * @var $this \yii\web\View
 * @var $column array
 * @var $rowId string
 * @var $columnId string
 */
?>
<?= \yii\bootstrap\Html::dropDownList(
    'BuilderForm[content][rows][' . $rowId . '][columns][' . $columnId . '][field_type]',
    $column['field_type'],
    \app\modules\reports\models\enums\FieldType::getDataList(),
    ['class' => 'form-control field_type']
) ?>
<div class="field_configuration_container">
    <?php
    switch ($column['field_type']) {
        case \app\modules\reports\models\enums\FieldType::HARDCODED:
            echo $this->render('__chart_hardcoded',
                ['rowId' => $rowId, 'columnId' => $columnId, 'column' => $column]);
            break;
        case \app\modules\reports\models\enums\FieldType::CUSTOM:
            echo $this->render('__chart_custom',
                ['rowId' => $rowId, 'columnId' => $columnId, 'column' => $column]);
            break;
    }
    ?>
</div>