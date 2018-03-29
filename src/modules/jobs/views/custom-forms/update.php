<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\jobs\models\CustomFormForm
 */

use app\models\enums\CustomFormFieldType;

$this->title = ($model->model->isNewRecord ? 'Create' : 'Update') . ' custom form';
\app\modules\jobs\assets\CustomFormsAsset::register($this);
?>
<div class="container custom_forms update">
    <div class="row">
        <div class="col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin(['id' => 'custom_form_form']) ?>
            <?= $form->field($model, 'name') ?>

            <hr>
            <h3>Fields</h3>

            <div class="fields">
                <div class="list_container">
                    <?php
                    foreach ($model->fields as $key => $field) {
                        ?>
                        <div class="item row <?= $field['type'] == CustomFormFieldType::SELECT || $field['type'] == CustomFormFieldType::CHECKBOX_LIST ? 'show_options' : '' ?>"
                             data-id="<?= $key ?>">
                            <div class="col-md-12">
                                <div class="dragger">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                </div>
                                <div class="card">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="text" class="form-control label_input" placeholder="Label"
                                                   name="CustomFormForm[fields][<?= $key ?>][label]"
                                                   value="<?= $field['label'] ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <?= \yii\bootstrap\Html::dropDownList('CustomFormForm[fields][' . $key . '][type]',
                                                $field['type'],
                                                CustomFormFieldType::getDataList(), [
                                                    'class' => 'form-control'
                                                ]) ?>

                                            <div class="modal fade" id="options_<?= $key ?>" tabindex="-1"
                                                 role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close"><span
                                                                        aria-hidden="true">&times;</span>
                                                            </button>
                                                            <h4 class="modal-title">Options</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="list option_list">
                                                                <?php foreach ($field['options'] as $option) { ?>
                                                                    <div class="form-group row item">
                                                                        <div class="col-md-1">
                                                                            <div class="option_dragger">
                                                                                <i class="fa fa-bars"
                                                                                   aria-hidden="true"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <?= \yii\bootstrap\Html::textInput('CustomFormForm[fields][' . $key . '][options][]',
                                                                                $option,
                                                                                ['class' => 'form-control']) ?>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <a href="#"
                                                                               class="btn btn-sm btn-danger delete_option"><i
                                                                                        class="fa fa-times"></i></a>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <a href="#"
                                                               class="btn btn-success btn-block add_option">Add
                                                                option</a>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">
                                                                Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>
                                                <input type="checkbox"
                                                       name="CustomFormForm[fields][<?= $key ?>][required]"
                                                       value="1" <?= isset($field['required']) && $field['required'] == 1 ? 'checked' : '' ?>>
                                                Required
                                            </label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control hint_input" placeholder="Hint"
                                                   name="CustomFormForm[fields][<?= $key ?>][hint]"
                                                   value="<?= $field['hint'] ?>">
                                        </div>
                                    </div>
                                    <div class="last_row row" style="margin-top: 10px;">
                                        <div class="col-md-5 options">
                                            <a href="#" class="open_modal btn btn-block btn-info">Edit Options
                                                (<span><?= count($field['options']) ?></span>)</a>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control default_input"
                                                   placeholder="Default value"
                                                   name="CustomFormForm[fields][<?= $key ?>][default]"
                                                   value="<?= $field['default'] ?>">
                                        </div>
                                        <div class="remove_block col-md-1 text-right">
                                            <a href="#" class="btn btn-sm btn-danger delete"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <a href="#" class="btn btn-success btn-block add_field">Add field</a>
                    </div>
                </div>
            </div>

            <br>

            <input type="submit" class="btn btn-primary" value="Save">
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </div>
</div>
<script id="field-template" type="text/x-handlebars-template">
    <div class="item row" data-id="{{id}}">
        <div class="col-md-12">
            <div class="dragger">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </div>
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="form-control label_input" placeholder="Label"
                               name="CustomFormForm[fields][{{id}}][label]">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= \yii\bootstrap\Html::dropDownList('CustomFormForm[fields][{{id}}][type]', null,
                            CustomFormFieldType::getDataList(), [
                                'class' => 'form-control'
                            ]) ?>

                        <div class="modal fade" id="options_{{id}}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close"><span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title">Options</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="list option_list">
                                        </div>
                                        <a href="#" class="btn btn-success btn-block add_option">Add
                                            option</a>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label>
                            <input type="checkbox" name="CustomFormForm[fields][{{id}}][required]" value="1">
                            Required
                        </label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Hint"
                               name="CustomFormForm[fields][{{id}}][hint]">
                    </div>
                </div>
                <div class="row last_row" style="margin-top: 10px;">
                    <div class="col-md-5 options">
                        <a href="#" class="open_modal btn btn-block btn-info">Edit Options (<span>0</span>)</a>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" placeholder="Default value"
                               name="CustomFormForm[fields][{{id}}][default]">
                    </div>

                    <div class="remove_block col-md-1 text-right">
                        <a href="#" class="btn btn-sm btn-danger delete"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script id="option-template" type="text/x-handlebars-template">
    <div class="form-group row item">
        <div class="col-md-1">
            <div class="option_dragger">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </div>
        </div>
        <div class="col-md-9">
            <?= \yii\bootstrap\Html::textInput('CustomFormForm[fields][{{id}}][options][]',
                null, ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-2">
            <a href="#" class="btn btn-sm btn-danger delete_option"><i
                        class="fa fa-times"></i></a>
        </div>
    </div>
</script>