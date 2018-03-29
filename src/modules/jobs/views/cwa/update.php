<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\CWA
 */
$this->title = ($model->isNewRecord ? 'Create' : 'Update') . ' CWA';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php $form = \yii\bootstrap\ActiveForm::begin() ?>
            <?= $form->field($model, 'name') ?>
            <?= $form->field($model, 'number') ?>
            <?= $form->field($model, 'amount') ?>
            <?= $form->field($model, 'owner_id')->widget(\nex\chosen\Chosen::class, [
                'clientOptions' => [
                    'allow_single_deselect' => false
                ],
                'items' => \app\models\User::getDataList(false)
            ]) ?>
            <?= $form->field($model, 'due_date')->widget(\app\widgets\DatePicker::class, [
                'options' => [
                    'value' => !empty($model->due_date) ? date('m/d/Y', strtotime($model->due_date)) : date('m/d/Y')
                ]
            ]) ?>

            <input type="submit" class="btn btn-primary" value="Save">
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </div>
</div>
