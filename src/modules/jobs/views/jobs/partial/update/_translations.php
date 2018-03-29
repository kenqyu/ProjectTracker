<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\jobs\models\UpdateJobForm
 * @var $form \yii\bootstrap\ActiveForm
 */
?>
<div class="row">
    <div class="col-md-12">

        <div class="translations">
            <?= $form->field($model,
                'translation_needed')->radioList(\skinka\php\TypeEnum\enums\YesNo::getDataList()) ?>

            <?php
            \yii\bootstrap\Modal::begin([
                'id' => 'add_translation',
                'header' => 'Add Translation'
            ]);
            ?>
            <div class="add">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="new_translation_due_date">Due Date</label>
                            <div class="input-group date">
                                <input type="text" id="new_translation_due_date" class="form-control"
                                       name="new_translation_due_date" value="">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                            <?php $this->registerJs("jQuery('#new_translation_due_date').parent().datepicker({\"startDate\":new Date});") ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="new_translation_rush">Rush</label>
                            <div id="new_translation_rush">
                                <div class="radio radio-inline">
                                    <label>
                                        <input type="radio" name="new_translation_rush" value="1">
                                        Yes
                                    </label>
                                </div>
                                <div class="radio radio-inline">
                                    <label>
                                        <input type="radio" name="new_translation_rush" value="0" checked>
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="new_translation_status">Status</label>
                            <?= \yii\bootstrap\Html::dropDownList('new_translation_status', \app\models\enums\JobTranslationStatus::REQUESTED,
                                \app\models\enums\JobTranslationStatus::getDataList(),
                                ['class' => 'form-control', 'id' => 'new_translation_status']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label" for="new_translation_language">Language</label>
                            <?= \yii\bootstrap\Html::dropDownList('new_translation_language', 0,
                                \app\models\enums\Languages::getDataList(),
                                ['class' => 'form-control', 'id' => 'new_translation_language']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <input type="submit" class="btn btn-primary btn-block" value="Add" id="add_translation_submit">
                    </div>
                </div>
            </div>
            <?php \yii\bootstrap\Modal::end() ?>
            <div class="translation_needed_block" style="display: <?= $model->translation_needed ? 'block' : 'none' ?>">
                <a href="#add_translation" data-toggle="modal" class="btn btn-primary btn-block">Add Translation</a>

                <div class="list">
                    <table class="table">
                        <tr>
                            <th>Due Date</th>
                            <th>Rush</th>
                            <th>Status</th>
                            <th>Language</th>
                            <th></th>
                        </tr>
                        <?php foreach ($model->model->jobTranslations as $item) { ?>
                            <tr data-id="<?= $item->id ?>">
                                <td><?= date('m/d/Y', strtotime($item->due_date)) ?></td>
                                <td><?= $item->rush ? 'Yes' : 'No' ?></td>
                                <td><?= \yii\bootstrap\Html::dropDownList('status', $item->status,
                                        \app\models\enums\JobTranslationStatus::getDataList(),
                                        ['class' => 'form-control']) ?></td>
                                <td><?= \app\models\enums\Languages::getByValue($item->language)->text() ?></td>
                                <td><a href="#" class="delete"><i class="fa fa-times"></i></a></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= \yii\bootstrap\Html::submitButton('Save', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>
</div>
<script id="translation-template" type="text/x-handlebars-template">
    <tr data-id="{{id}}">
        <td>{{due_date}}</td>
        <td>{{#if rush}}Yes{{else}}No{{/if}}</td>
        <td>
            <?= \yii\bootstrap\Html::dropDownList('status', null,
                \app\models\enums\JobTranslationStatus::getDataList(),
                ['class' => 'form-control']) ?>
        </td>
        <td>{{language}}</td>
        <td><a class="delete" href="#"><i class="fa fa-times"></i></a></td>
    </tr>
</script>