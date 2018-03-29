<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\jobs\models\UpdateJobForm
 * @var $form \yii\bootstrap\ActiveForm
 * @var $user \app\models\User
 */
$user = Yii::$app->user->identity;
?>
<div class="row">
    <div class="col-md-12">

        <?php if ($user->role >= \app\models\enums\UserRoles::MANAGER) { ?>
            <?= $form->field($model, 'processing_unit_id')->dropDownList(\app\models\ProcessingUnit::getDataList()) ?>
            <?= $form->field($model, 'request_type_id')
                ->dropDownList(\app\models\RequestType::getDataList($model->processing_unit_id)) ?>
        <?php } ?>
        <?php if (!empty($model->work_types)) { ?>
            <?= $form->field($model, 'work_types')->widget(\nex\chosen\Chosen::class, [
                'items' => \app\models\WorkType::getDataList(),
                'multiple' => true,
                'options' => [
                    'disabled' => true
                ]
            ]) ?>
        <?php } ?>
        <?= $form->field($model, 'project_lead_id',
            ['hintType' => \kartik\form\ActiveField::HINT_SPECIAL])->widget(\nex\chosen\Chosen::class, [
            'items' => \app\models\User::getDataList(false, \app\models\enums\UserTypes::PROJECT_LEAD),
            'options' => [
                'disabled' => $user->role == \app\models\enums\UserRoles::GENERAL
            ]
        ]) ?>
        <?= $form->field($model, 'project_manager_id',
            ['hintType' => \kartik\form\ActiveField::HINT_SPECIAL])->widget(\nex\chosen\Chosen::class, [
            'items' => \app\models\User::getDataList(false, \app\models\enums\UserTypes::PROJECT_MANAGER),
            'options' => [
                'disabled' => $user->role == \app\models\enums\UserRoles::GENERAL
            ]
        ]) ?>
        <?php if ($user->role >= \app\models\enums\UserRoles::MANAGER) { ?>
            <?= $form->field($model, 'agency_id',
                ['hintType' => \kartik\form\ActiveField::HINT_SPECIAL])->dropDownList(\app\models\Agency::getDataList(true)) ?>
        <?php } ?>
        <?= $form->field($model, 'iwcm_publishing_assignee_id')->widget(\nex\chosen\Chosen::class, [
            'items' => \app\models\User::getDataList(false, \app\models\enums\UserTypes::IWCM_PUBLISHING_ASSIGNEE),
            'options' => [
                'disabled' => $user->role == \app\models\enums\UserRoles::GENERAL
            ]
        ]) ?>
        <?php
        /*
        $form->field($model, 'translation_manager_id')->widget(\nex\chosen\Chosen::class, [
            'items' => \app\models\User::getDataList(false, \app\models\enums\UserTypes::TRANSLATION_MANAGER),
            'options' => [
                'disabled' => $user->role == \app\models\enums\UserRoles::GENERAL
            ]
        ]) */ ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= \yii\bootstrap\Html::submitButton('Save', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>
</div>