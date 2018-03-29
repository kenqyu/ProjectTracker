<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\user\models\ForgotPasswordForm
 */

\app\modules\user\assets\AuthAsset::register($this);
?>
<div class="auth">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <?php
                $form = \yii\bootstrap\ActiveForm::begin();
                ?>
                <h1 class="text-center">Password reset</h1>
                <?= $form->field($model, 'email')->textInput(['placeholder' => 'Email'])->label(false) ?>

                <button class="btn btn-primary btn-block">Reset</button>
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
