<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\jobs\models\RequestTypeForm
 */
$this->title = ($model->model->isNewRecord ? 'Create' : 'Update') . ' Request Type';
\app\modules\jobs\assets\RequestTypeAsset::register($this);
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin() ?>
            <?= $form->field($model, 'name') ?>
            <?= $form->field($model, 'sort') ?>
            <?= $form->field($model, 'custom_form_id')->dropDownList(\app\models\CustomForm::getDataList(true)) ?>
            <?= $form->field($model, 'alert') ?>
            <?= $form->field($model, 'show_cwa')->checkbox() ?>
            <?= $form->field($model, 'show_links')->checkbox() ?>
            <?= $form->field($model, 'show_mandate')->checkbox() ?>
            <?= $form->field($model, 'show_translation_needed')->checkbox() ?>

            <?= $form->field($model, 'show_cost_center')->checkbox() ?>

            <div class="default_cost_centers">
                <h3>Default cost centers</h3>
                <div class="add">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="new_cost_center_name" data-toggle="popover"
                                       style="border-bottom: 1px dashed #888;"
                                       data-placement="top"
                                       data-content="Enter cost center number(s) and the percentage of overall project costs that should be applied to each cost center. After entering info, please click the “Save” button.">
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
                            <label class="control-label" style="width: 100%;">&nbsp;</label>
                            <a href="#" class="btn btn-primary" id="add_cost_center">Add</a>
                        </div>
                    </div>
                </div>
                <div class="list">
                    <table class="table">
                        <tbody>
                        <?php foreach ($model->model->requestTypeDefaultCostCenters as $item) { ?>
                            <tr data-id="<?= $item->id ?>"
                                data-percent="<?= $item->cost_center_percent ?>">
                                <td width="50%"><input type="hidden"
                                                       name="RequestTypeForm[defaultCostCenters][<?= $item->id ?>][cost_center_label]"
                                                       value="<?= $item->cost_center_label ?>"><?= $item->cost_center_label ?>
                                </td>
                                <td width="33%"><input type="hidden"
                                                       name="RequestTypeForm[defaultCostCenters][<?= $item->id ?>][cost_center_percent]"
                                                       value="<?= $item->cost_center_percent ?>"><?= $item->cost_center_percent ?>
                                    %
                                </td>
                                <td><a class="delete" href="#"><i class="fa fa-times"></i></a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <input type="submit" class="btn btn-primary" value="Save">
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </div>
</div>
<script id="cost-center-template" type="text/x-handlebars-template">
    <tr data-id="{{id}}"
        data-percent="{{percent}}">
        <td width="50%">
            <input type="hidden" name="RequestTypeForm[defaultCostCenters][{{id}}][cost_center_label]"
                   value="{{cost_center_name}}">
            {{cost_center_name}}
        </td>
        <td width="33%">
            <input type="hidden" name="RequestTypeForm[defaultCostCenters][{{id}}][cost_center_percent]"
                   value="{{percent}}">
            {{percent}}%
        </td>
        <td><a class="delete" href="#"><i class="fa fa-times"></i></a></td>
    </tr>
</script>