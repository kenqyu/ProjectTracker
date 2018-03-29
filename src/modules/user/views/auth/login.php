<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\forms\LoginForm
 */

\app\modules\user\assets\AuthAsset::register($this);
?>
<div class="auth">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h1 class="text-center">
                    Welcome to the <br>SCE CX & I Requests and Project Tracker
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="row mobile-reverse">
                    <div class="col-md-6">
                        <?php
                        $form = \yii\bootstrap\ActiveForm::begin();
                        ?>

                        <?= \app\widgets\Alert::widget() ?>

                        <?= $form->field($model, 'email')->textInput(['placeholder' => 'Email'])->label(false) ?>
                        <?= $form->field($model,
                            'password')->passwordInput(['placeholder' => 'Password'])->label(false) ?>
                        <?= $form->field($model, 'rememberMe')->checkbox() ?>
                        <p>
                            <a href="<?= \yii\helpers\Url::to(['forgot-password']) ?>">Forgot your password?</a>
                        </p>

                        <button class="btn btn-primary btn-block">Log In</button>
                        <p class="text-center register">
                            <a href="<?= \yii\helpers\Url::to('register') ?>">Register</a>
                        </p>

                        <?php
                        \yii\bootstrap\ActiveForm::end();
                        ?>
                    </div>
                    <div class="col-md-6">
                        <p class="help-block">
                            Submit your request for content updates, data analytics, check the status of a project, and
                            more
                            from the CX & I Team.
                        </p>
                        <p class="help-block">
                            Please note, this is a unique system and your log in credentials here will not affect your
                            SCE log
                            in. Your email address may already exist in the system; if so you’ll need to <a
                                    href="<?= \yii\helpers\Url::to(['forgot-password']) ?>">reset your password</a>. If
                            you are a new user, <a href="<?= \yii\helpers\Url::to(['register']) ?>">register now</a>.
                        </p>

                        <p class="help-block">
                            <a href="mailto:Adeline.Ashley@sce.com?subject=SCE%20CX%20Requests%20and%20Project%20Tracker%20General%20Question">Need
                                help?</a>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
