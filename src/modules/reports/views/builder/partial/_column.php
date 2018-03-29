<?php
/**
 * @var $this \yii\web\View
 * @var $column array
 * @var $rowId string
 */
$columnId = \Ramsey\Uuid\Uuid::uuid4()->toString();
?>
<div class="column" data-id="<?= $columnId ?>">
    <?= \yii\bootstrap\Html::textInput(
        'BuilderForm[content][rows][' . $rowId . '][columns][' . $columnId . '][name]',
        $column['name'],
        ['class' => 'form-control', 'placeholder' => 'Column Label']
    ) ?>
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[content][rows][' . $rowId . '][columns][' . $columnId . '][type]',
        $column['type'],
        \app\modules\reports\models\enums\SectionType::getDataList(),
        ['class' => 'form-control column_type']
    ) ?>
    <div class="type_container">
        <?php
        switch ($column['type']) {
            case \app\modules\reports\models\enums\SectionType::PIE_CHART:
            case \app\modules\reports\models\enums\SectionType::BAR_CHART: {
                echo $this->render('_chart', ['rowId' => $rowId, 'columnId' => $columnId, 'column' => $column]);
            }
        }
        ?>
    </div>
</div>
