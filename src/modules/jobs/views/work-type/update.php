<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\WorkType
 */
$this->title = ($model->isNewRecord ? 'Create' : 'Update') . ' Work Type';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin() ?>
            <?= \app\widgets\Alert::widget(['models' => [$model]]) ?>
            <?= $form->field($model, 'name') ?>

            <input type="submit" class="btn btn-primary" value="Save">
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </div>
</div>
