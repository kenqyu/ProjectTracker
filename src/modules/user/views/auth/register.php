<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\forms\RegisterForm
 */

\app\modules\user\assets\RegisterAsset::register($this);
?>
<div class="auth">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <?php
                $form = \yii\bootstrap\ActiveForm::begin();
                ?>
                <h1 class="text-center">SCE CX & I Requests and Project Tracker</h1>

                <p class="help-block">
                    Registration is required in order to submit project requests.
                    Please fill out and submit this form to request access to the Project Tracker. You will be notified
                    by email once your request has been reviewed and approved.
                </p>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'first_name'); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'last_name'); ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'email') ?>

                        <?= $form->field($model, 'phone') ?>
                        <?= $form->field($model, 'password')->passwordInput() ?>
                        <?= $form->field($model, 'password_repeat')->passwordInput() ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'organization_unit')
                            ->dropDownList(\app\models\OrganizationUnit::getDataList(true, '', ''),
                                ['options' => \app\models\OrganizationUnit::getDataAttributes()]); ?>

                        <?= $form->field($model, 'department_id')->dropDownList([]); ?>
                        <?= $form->field($model, 'sub_department_id')->dropDownList([]); ?>
                        <?= $form->field($model, 'organization_unit_other'); ?>
                    </div>
                </div>

                <button class="btn btn-primary btn-block">Register</button>
                <p class="text-center register">
                    <a href="<?= \yii\helpers\Url::to('login') ?>">Log in</a>
                </p>
                <?php
                \yii\bootstrap\ActiveForm::end();
                ?>
            </div>
        </div>
    </div>
</div>
