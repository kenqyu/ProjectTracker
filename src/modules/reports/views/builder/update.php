<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\reports\models\BuilderForm
 */
\app\modules\reports\assets\BuilderAsset::register($this);
?>

<div class="container-fluid report_builder">
    <div class="row">
        <div class="col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin() ?>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'name') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'public')->dropDownList(\skinka\php\TypeEnum\enums\YesNo::getDataList()) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'processing_unit_id')
                        ->dropDownList(\app\models\ProcessingUnit::getDataList(true, 'All processing units')) ?>
                </div>
                <div class="col-md-4">
                    <div class="fake-label"></div>
                    <a href="#filters_modal" class="btn btn-block btn-primary" data-toggle="modal">Configure filters</a>
                    <?= $this->render('partial/_filters_modal', ['model' => $model]) ?>
                </div>
            </div>

            <div class="rows">
                <?php
                if (isset($model->content['rows'])) {
                    foreach ($model->content['rows'] as $row) {
                        echo $this->render('partial/_row', ['row' => $row]);
                    }
                }
                ?>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <a href="#" class="btn btn-success btn-block add_row">Add row</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'footnote')->widget(\yii\redactor\widgets\Redactor::class, [
                        'clientOptions' => [
                            'visual' => true,
                            'buttonSource' => true,
                            'plugins' => [
                                'fontcolor',
                                'fontsize'
                            ],
                            'linebreaks' => true,
                            'replaceDivs' => false,
                            'paragraphize' => false,
                            'minHeight' => 200,
                            'cleanOnPaste' => false,
                            'removeEmpty' => false
                        ]
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <?= \yii\bootstrap\Html::submitButton('Save', ['class' => 'btn btn-primary btn-lg']) ?>

                    <?php
                    if (!$model->isNewRecord()) {
                        ?>
                        <a href="<?= \yii\helpers\Url::to(['delete', 'id' => $model->getId()]) ?>"
                           class="btn btn-danger btn-lg"
                           onclick="return confirm('You sure?')">Delete report</a>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </div>
</div>
<script id="row-template" type="text/x-handlebars-template">
    <div class="builder_row" data-id="{{row}}">
        <div class="dragger_container">
            <div class="dragger">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </div>
        </div>
        <div class="row-configuration_container">
            <div class="row_name row">
                <div class="col-md-12">
                    <?= \yii\bootstrap\Html::textInput(
                        'BuilderForm[content][rows][{{row}}][name]',
                        null,
                        ['class' => 'form-control', 'placeholder' => 'Row Label']
                    ) ?>
                </div>
            </div>
            <div class="columns"></div>

            <div class="row actions">
                <div class="col-md-12 text-center">
                    <a href="#" class="btn btn-danger delete_row">Delete row</a>
                </div>
            </div>
        </div>
    </div>
</script>
<script id="column-template" type="text/x-handlebars-template">
    <div class="column" data-id="{{column}}">
        <?= \yii\bootstrap\Html::textInput(
            'BuilderForm[content][rows][{{row}}][columns][{{column}}][name]',
            null,
            ['class' => 'form-control', 'placeholder' => 'Column Label']
        ) ?>
        <?= \yii\bootstrap\Html::dropDownList(
            'BuilderForm[content][rows][{{row}}][columns][{{column}}][type]',
            null,
            \app\modules\reports\models\enums\SectionType::getDataList(),
            ['class' => 'form-control column_type']
        ) ?>
        <div class="type_container"></div>
    </div>
</script>
<script id="type-chart-template" type="text/x-handlebars-template">
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[content][rows][{{row}}][columns][{{column}}][field_type]',
        null,
        \app\modules\reports\models\enums\FieldType::getDataList(),
        ['class' => 'form-control field_type']
    ) ?>
    <div class="field_configuration_container"></div>
</script>
<script id="type-chart-native-template" type="text/x-handlebars-template">
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[content][rows][{{row}}][columns][{{column}}][field]',
        null,
        \app\modules\reports\models\enums\NativeField::getDataList(),
        ['class' => 'form-control']
    ) ?>
</script>
<script id="type-chart-custom-template" type="text/x-handlebars-template">
    <?php
    $processingUnits = \app\models\ProcessingUnit::getDataList();
    $requestTypes = \app\models\RequestType::getDataList(key($processingUnits), true);
    if (!empty($requestTypes)) {
        $fields = \yii\helpers\ArrayHelper::map(
            \app\models\RequestType::findOne(key($requestTypes))->customForm->customFormFields,
            'label',
            'label'
        );
    } else {
        $fields = [];
    }
    ?>
    <label for="processing_unit_{{column}}">Processing Unit</label>
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[content][rows][{{row}}][columns][{{column}}][processing_unit]',
        null,
        $processingUnits,
        ['class' => 'form-control processing_unit', 'id' => 'processing_unit_{{column}}']
    ) ?>

    <label for="request_type_{{column}}">Request Type</label>
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[content][rows][{{row}}][columns][{{column}}][request_type]',
        null,
        $requestTypes,
        ['class' => 'form-control request_type', 'id' => 'request_type_{{column}}']
    ) ?>

    <label for="field_{{column}}">Field</label>
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[content][rows][{{row}}][columns][{{column}}][field]',
        null,
        $fields,
        ['class' => 'form-control custom_field', 'id' => 'field_{{column}}']
    ) ?>
</script>