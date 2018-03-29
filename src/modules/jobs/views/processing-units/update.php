<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\ProcessingUnit
 */
$this->title = ($model->isNewRecord ? 'Create' : 'Update') . ' Processing Department';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin() ?>
            <?= $form->field($model, 'name') ?>
            <?= $form->field($model, 'order') ?>

            <input type="submit" class="btn btn-primary" value="Save">
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </div>
</div>
