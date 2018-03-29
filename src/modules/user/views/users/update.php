<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\user\models\UserUpdateForm
 */
$this->title = 'Update User';
\app\modules\user\assets\UserUpdateAsset::register($this);
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?= \app\widgets\Alert::widget() ?>
            <?php $form = \yii\bootstrap\ActiveForm::begin() ?>

            <div class="row">
                <div class="col-md-8">
                    <h2>User Info<br>&nbsp;</h2>
                    <?= $form->field($model, 'username') ?>
                    <?= $form->field($model, 'phone') ?>
                    <?= $form->field($model, 'first_name') ?>
                    <?= $form->field($model, 'last_name') ?>
                    <?php if ($model->id != Yii::$app->user->id || !$model->model->approved) { ?>
                        <?= $form->field($model, 'status')->dropDownList(\app\models\enums\UserStatus::getDataList()) ?>
                    <?php } ?>
                    <?= $form->field($model, 'no_mails')->checkbox() ?>
                    <hr>
                    <?= $form->field($model, 'email') ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                    <hr>
                    <?= $form->field($model, 'organization_unit_id')
                        ->dropDownList(\app\models\OrganizationUnit::getDataList(false),
                            ['options' => \app\models\OrganizationUnit::getDataAttributes()]); ?>

                    <?= $form->field($model, 'department_id')
                        ->dropDownList($model->organization_unit_id > 0 ? \app\models\Departments::getDataListByOrganizationUnit($model->organization_unit_id) : []); ?>
                    <?= $form->field($model, 'sub_department_id')
                        ->dropDownList($model->department_id > 0 ? \app\models\SubDepartment::getDataListByDepartment($model->department_id) : []); ?>
                    <?= $form->field($model, 'organization_unit_other'); ?>
                </div>
                <div class="col-md-4">
                    <h2>User Function and Notification Assignment</h2>
                    <?php if ($model->id != Yii::$app->user->id) { ?>
                        <?= $form->field($model, 'role')->dropDownList(\app\models\enums\UserRoles::getDataList()) ?>
                    <?php } ?>
                    <h3>Types</h3>
                    <?= $form->field($model, 'types')
                        ->checkboxList(\app\models\enums\UserTypes::getDataList())->label(false) ?>
                    <h3>Processing Departments</h3>
                    <?= $form->field($model, 'processing_units')
                        ->checkboxList(\app\models\ProcessingUnit::getDataList())->label(false) ?>
                </div>
            </div>


            <?php if (!$model->model->approved) { ?>
                <a href="<?= \yii\helpers\Url::to(['approve', 'id' => $model->id]) ?>"
                   class="btn btn-warning">Approve</a>
                <a href="<?= \yii\helpers\Url::to(['decline', 'id' => $model->id]) ?>"
                   class="btn btn-danger">Decline</a>
            <?php } else {
                ?>
                <input type="submit" class="btn btn-primary" value="Save">
                <?php if (!$model->model->default_ccc_contact) { ?>
                    <a href="<?= \yii\helpers\Url::to(['made-default-c-c-c-contact', 'id' => $model->id]) ?>"
                       class="btn btn-default">Make
                        user default CCC contact</a>
                <?php } ?>
                <?php
            } ?>
            <?php \yii\bootstrap\ActiveForm::end() ?>
        </div>
    </div>
</div>
