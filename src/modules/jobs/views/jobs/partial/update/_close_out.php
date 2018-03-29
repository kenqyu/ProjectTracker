<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\jobs\models\UpdateJobForm
 * @var $form \yii\bootstrap\ActiveForm
 */
?>
<div class="row">
    <div class="col-md-12">

        <?= $form->field($model, 'ccc_impact', ['hintType' => \kartik\form\ActiveField::HINT_SPECIAL])
            ->radioList(\skinka\php\TypeEnum\enums\YesNo::getDataList())->label('CCC Impact') ?>
        <?= $form->field($model, 'one_voice',
            ['hintType' => \kartik\form\ActiveField::HINT_SPECIAL])->radioList(\skinka\php\TypeEnum\enums\YesNo::getDataList()) ?>
        <?= $form->field($model, 'ccc_contact_id',
            ['hintType' => \kartik\form\ActiveField::HINT_SPECIAL])->widget(\nex\chosen\Chosen::class, [
            'items' => \app\models\User::getDataList(false, \app\models\enums\UserTypes::CCC_CONTACT)
        ]) ?>

        <?= $form->field($model, 'approver', ['hintType' => \kartik\form\ActiveField::HINT_SPECIAL])
            ->textInput(['disabled' => Yii::$app->user->identity->role == \app\models\enums\UserRoles::GENERAL]) ?>
        <?= $form->field($model, 'completed_on')->widget(\app\widgets\DatePicker::class, [
            'clientOptions' => [
                'startDate' => Yii::$app->formatter->asDate($model->model->created_at,'php:m-d-Y')
            ],
            'options' => !empty($model->completed_on) ? [
                'value' => date('m/d/Y', strtotime($model->completed_on))
            ] : []
        ]) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= \yii\bootstrap\Html::submitButton('Save', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>
</div>