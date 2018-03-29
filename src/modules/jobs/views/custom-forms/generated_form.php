<?php
/**
 * @var $model \app\models\CustomForm
 * @var $req \app\models\RequestType
 */
if ($model) {
    $fields = \app\models\CustomFormField::find()->where(['form_id' => $model->id])->orderBy(['sort' => SORT_ASC])->all();
} else {
    $fields = [];
}
?>
<?php foreach ($fields as $field) {
    /** @var $field \app\models\CustomFormField */
    if (!empty($field->hint)) {
        ?>
        <div class="form-group type_<?= \app\models\enums\CustomFormFieldType::getByValue($field->type)->code() ?> <?= $field->required ? 'required' : '' ?>"
             data-type="<?= \app\models\enums\CustomFormFieldType::getByValue($field->type)->type() ?>">
            <?php if ($field->type != \app\models\enums\CustomFormFieldType::CHECKBOX) { ?>
                <label class="control-label" data-toggle="tooltip"
                       title="<?= \yii\helpers\Html::encode($field->hint) ?>">
                    <?= $field->label ?>
                    <?= $field->required ? '<span class="required">*</span>' : '' ?>
                    <i class="glyphicon glyphicon-question-sign text-info"></i>
                </label>
            <?php } ?>
            <?= \yii\helpers\Html::hiddenInput('CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][label]',
                $field->label) ?>
            <?php
            switch ($field->type) {
                case \app\models\enums\CustomFormFieldType::VARCHAR:
                    echo \yii\helpers\Html::textInput('CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value]',
                        $field->default,
                        ['class' => 'form-control']);
                    echo \yii\helpers\Html::tag('div', $field->label . ' cannot be blank',
                        ['class' => 'help-block error']);
                    break;
                case \app\models\enums\CustomFormFieldType::DATE:
                    echo \app\widgets\DatePicker::widget([
                        'name' => 'CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value]',
                        'value' => $field->default,
                        'options' => [
                            'id' => 'custom_field_' . rand(100000, 999999)
                        ]
                    ]);
                    echo \yii\helpers\Html::tag('div', $field->label . ' cannot be blank',
                        ['class' => 'help-block error']);
                    break;
                case \app\models\enums\CustomFormFieldType::DATETIME:
                    echo \dosamigos\datetimepicker\DateTimePicker::widget([
                        'name' => 'CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value]',
                        'value' => $field->default,
                        'options' => [
                            'id' => 'custom_field_' . rand(100000, 999999)
                        ]
                    ]);
                    echo \yii\helpers\Html::tag('div', $field->label . ' cannot be blank',
                        ['class' => 'help-block error']);
                    break;
                case \app\models\enums\CustomFormFieldType::SELECT:
                    if ($field->default == '') {
                        $o = [
                            '' => ''
                        ];
                    } else {
                        $o = [];
                    }
                    foreach ($field->getOptions() as $item) {
                        $o[$item] = $item;
                    }
                    echo \yii\helpers\Html::dropDownList('CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value]',
                        $field->default, $o, ['class' => 'form-control', 'options' => ['' => ['disabled' => true]]]);
                    echo \yii\helpers\Html::tag('div', $field->label . ' cannot be blank',
                        ['class' => 'help-block error']);
                    break;
                case \app\models\enums\CustomFormFieldType::CHECKBOX:
                    echo '<label class="control-label">' .
                        \yii\helpers\Html::checkbox('CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value]',
                            !empty($field->default),
                            ['class' => 'form-control'])
                        . $field->label . '</label>';
                    echo \yii\helpers\Html::tag('div', $field->label . ' must be checked',
                        ['class' => 'help-block error']);
                    break;
                case \app\models\enums\CustomFormFieldType::CHECKBOX_LIST:
                    $o = [];
                    foreach ($field->getOptions() as $item) {
                        $o[$item] = $item;
                    }
                    echo \yii\helpers\Html::checkboxList('CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value][]',
                        $field->default, $o);
                    echo \yii\helpers\Html::tag('div', $field->label . ' cannot be blank',
                        ['class' => 'help-block error']);
                    break;
            }
            ?>
            <div class="help-block"></div>
        </div>
    <?php } else { ?>
        <div class="form-group type_<?= \app\models\enums\CustomFormFieldType::getByValue($field->type)->code() ?> <?= $field->required ? 'required' : '' ?>"
             data-type="<?= \app\models\enums\CustomFormFieldType::getByValue($field->type)->type() ?>">
            <?php if ($field->type != \app\models\enums\CustomFormFieldType::CHECKBOX) { ?>
                <label class="control-label"><?= $field->label ?> <?= $field->required ? '<span class="required">*</span>' : '' ?></label>
            <?php } ?>
            <?= \yii\helpers\Html::hiddenInput('CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][label]',
                $field->label) ?>
            <?php
            switch ($field->type) {
                case \app\models\enums\CustomFormFieldType::VARCHAR:
                    echo \yii\helpers\Html::textInput('CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value]',
                        $field->default,
                        ['class' => 'form-control']);
                    echo \yii\helpers\Html::tag('div', $field->label . ' cannot be blank',
                        ['class' => 'help-block error']);
                    break;
                case \app\models\enums\CustomFormFieldType::DATE:
                    echo \app\widgets\DatePicker::widget([
                        'name' => 'CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value]',
                        'value' => $field->default,
                        'options' => [
                            'id' => 'custom_field_' . rand(100000, 999999)
                        ]
                    ]);
                    echo \yii\helpers\Html::tag('div', $field->label . ' cannot be blank',
                        ['class' => 'help-block error']);
                    break;
                case \app\models\enums\CustomFormFieldType::DATETIME:
                    echo \dosamigos\datetimepicker\DateTimePicker::widget([
                        'name' => 'CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value]',
                        'value' => $field->default,
                        'options' => [
                            'id' => 'custom_field_' . rand(100000, 999999)
                        ]
                    ]);
                    echo \yii\helpers\Html::tag('div', $field->label . ' cannot be blank',
                        ['class' => 'help-block error']);
                    break;
                case \app\models\enums\CustomFormFieldType::SELECT:
                    if ($field->default == '') {
                        $o = [
                            '' => ''
                        ];
                    } else {
                        $o = [];
                    }
                    foreach ($field->getOptions() as $item) {
                        $o[$item] = $item;
                    }
                    echo \yii\helpers\Html::dropDownList('CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value]',
                        $field->default, $o, ['class' => 'form-control', 'options' => ['' => ['disabled' => true]]]);
                    echo \yii\helpers\Html::tag('div', $field->label . ' cannot be blank',
                        ['class' => 'help-block error']);
                    break;
                case \app\models\enums\CustomFormFieldType::CHECKBOX:
                    echo '<label>' .
                        \yii\helpers\Html::checkbox('CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value]',
                            !empty($field->default))
                        . $field->label . '</label>';
                    echo \yii\helpers\Html::tag('div', $field->label . ' must be checked',
                        ['class' => 'help-block error']);
                    break;
                case \app\models\enums\CustomFormFieldType::CHECKBOX_LIST:
                    $o = [];
                    foreach ($field->getOptions() as $item) {
                        $o[$item] = $item;
                    }
                    echo \yii\helpers\Html::checkboxList('CreateJobForm[requests][' . $req->id . '][custom_fields][' . $field->id . '][value][]',
                        $field->default, $o);
                    echo \yii\helpers\Html::tag('div', $field->label . ' cannot be blank',
                        ['class' => 'help-block error']);
                    break;
            }
            ?>
            <div class="help-block"></div>
        </div>
    <?php }
} ?>
<?php if ($req->show_cwa) {
    ?>
    <div class="cwa">
        <div class="row">
            <div class="col-md-10">
                <div class="form-group">
                    <label class="control-label">CWA Number - CWA Name</label>
                    <?= \yii\helpers\Html::dropDownList('CreateJobForm[requests][' . $req->id . '][cwa_id]', null,
                        \app\models\CWA::getDataList([], true, true, ''),
                        [
                            'class' => 'form-control',
                            'id' => 'new_cwa',
                            'options' => \app\models\CWA::getDataListInfo([], [], true)
                        ]
                    ) ?>
                </div>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <a href="#" id="add_cwa" class="btn btn-primary btn-block"><i class="fa fa-plus"></i></a>
            </div>
        </div>
        <div class="list">
            <table class="table">
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <?php
} ?>

<?php if ($req->show_links) {
    ?>
    <hr>
    <div class="cms_links">
        <div class="form-group">
            <label>
                <?= \yii\helpers\Html::checkbox('CreateJobForm[requests][' . $req->id . '][page_update]', null,
                    ['class' => 'show_links']) ?>
                If this request will require content updates to existing sce.com page(s), check this box.
            </label>
        </div>

        <div id="links" style="display: none;">
            <a href="#" class="btn btn-primary btn-block" id="add_link">Add URLs for pages needing updates here</a>

            <table class="table">
                <tbody></tbody>
            </table>
        </div>
    </div>
    <hr>
    <?php
} ?>
<?php if ($req->show_cost_center) {
    ?>
    <div class="cost-centers">
        <div class="add">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="new_cost_center_name" data-toggle="tooltip"
                               style="border-bottom: 1px dashed #888;"
                               title="Enter cost center number(s) and the percentage of overall project costs that should be applied to each cost center. After entering info, please click the “Save” button.">
                            Cost Center
                            <i class="glyphicon glyphicon-question-sign text-info"></i>
                        </label>
                        <input type="text" id="new_cost_center_name" class="form-control" maxlength="8">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="new_cost_center_percent">Percent</label>
                        <input type="text" id="new_cost_center_percent" class="form-control">

                    </div>
                </div>
                <div class="col-md-2">
                    <a href="#" class="btn btn-primary btn-block" id="add_cost_center"
                       style="margin-top: 24px;">Save</a>
                </div>
            </div>
        </div>
        <div class="list">
            <table class="table">
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <?php
} ?>
<?php if ($req->show_mandate || $req->show_translation_needed) { ?>
    <div class="row">
        <?php if ($req->show_mandate) { ?>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Mandate</label>
                    <?= \yii\helpers\Html::radioList('CreateJobForm[requests][' . $req->id . '][mandate]', 0,
                        \skinka\php\TypeEnum\enums\YesNo::getDataList(),
                        [
                            'class' => 'radio'
                        ]) ?>
                </div>
            </div>
        <?php } ?>
        <?php if ($req->show_translation_needed) { ?>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Translation Needed</label>
                    <?= \yii\helpers\Html::radioList('CreateJobForm[requests][' . $req->id . '][translation_needed]',
                        0, \skinka\php\TypeEnum\enums\YesNo::getDataList(), ['class' => 'radio']) ?>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<div class="form-group">
    <label class="control-label" data-toggle="tooltip" title="You can upload multiple files at once">Files</label>
    <?= \yii\helpers\Html::fileInput('CreateJobForm[requests][' . $req->id . '][files][]') ?>
    <div class="text-left warning">Warning! Do not upload any files containing sensitive information.
        <br>Use SharePoint and provide a link.
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <a href="#" class="back btn btn-info">Back</a>
    </div>
    <div class="col-md-6 text-right">
        <a href="#" class="cancel btn btn-info">Cancel</a>

        <a href="#" class="btn btn-success next_step off"
           data-loading-text="<i class='fa fa - spinner fa - spin '></i> Loading next step">Next
            step</a>
    </div>
</div>
<script id="link-template" type="text/x-handlebars-template">
    <tr>
        <td> <?= \yii\helpers\Html::textInput('CreateJobForm[requests][' . $req->id . '][links][]', null,
                ['class' => 'form-control']) ?></td>
        <td width="40"><a class="delete" href="#">Delete</a></td>
    </tr>
</script>

<script id="cost-center-template" type="text/x-handlebars-template">
    <tr data-cost-center="{{cost_center_name}}"
        data-percent="{{percent}}">
        <td width="50%">
            <input type="hidden" name="CreateJobForm[requests][{{request}}][cost_centers][{{id}}][cost_center]"
                   value="{{cost_center_name}}">
            <input type="hidden" name="CreateJobForm[requests][{{request}}][cost_centers][{{id}}][percent]"
                   value="{{percent}}">
            {{cost_center_name}}
        </td>
        <td>{{percent}}%</td>
        <td width="40"><a class="delete" href="#">Delete</a></td>
    </tr>
</script>
<script id="cwa-template" type="text/x-handlebars-template">
    <tr data-id="{{id}}" data-name="{{name}}">
        <td>
            {{name}}
            <input type="hidden" name="CreateJobForm[requests][{{request}}][cwa][]" value="{{id}}">
        </td>
        <td width="40"><a class="delete" href="#">Delete</a></td>
    </tr>
</script>