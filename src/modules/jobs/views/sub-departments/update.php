<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\SubDepartment
 */
$this->title = ($model->isNewRecord ? 'Create' : 'Update') . ' Sub Department';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin() ?>
            <?= $form->field($model, 'name') ?>

            <input type="submit" class="btn btn-primary" value="Save">
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </div>
</div>