<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\OrganizationUnit
 */
$this->title = ($model->isNewRecord ? 'Create' : 'Update') . ' Organization Unit';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin() ?>
            <?= $form->field($model, 'name') ?>
            <?= $form->field($model, 'order') ?>
            <?= $form->field($model, 'ask_for_input')->checkbox() ?>

            <input type="submit" class="btn btn-primary" value="Save">
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </div>
</div>
