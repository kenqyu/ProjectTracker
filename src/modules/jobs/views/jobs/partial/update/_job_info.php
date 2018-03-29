<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\jobs\models\UpdateJobForm
 * @var $form \yii\bootstrap\ActiveForm
 */

use app\models\enums\UserRoles;
use yii\bootstrap\Html;

?>
<div class="row">
    <div class="col-md-12">

        <div class="alert alert-info">
            To contact/collaborate with requestor, please go to the <a href="#"
                                                                       onclick="$('a[href=\'#comments\']').click(); return false;">‘Comment’
                section</a>
        </div>

        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'description', ['hintType' => \kartik\form\ActiveField::HINT_SPECIAL])->textarea() ?>

        <?= $form->field($model, 'page_update')->checkbox() ?>
        <div id="links" style="display: <?= $model->page_update ? 'block' : 'none' ?>; margin-bottom: 10px">
            <?php
            foreach ($model->links as $key => $link) {
                ?>
                <div class="row item">
                    <div class="col-md-10">
                        <div class="edit_field">
                            <?= $form->field($model, 'links[' . $key . ']')->label(false) ?>
                        </div>
                        <div class="row text">
                            <div class="col-md-11">
                                <?= Html::a($link, $link, ['class' => 'link', 'target' => '_blank']) ?>
                            </div>
                            <div class="col-md-1 text-right">
                                <a href="#" class="btn btn-info btn-sm edit"><i class="fa fa-pencil"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <a href="#" class="btn btn-danger btn-sm delete"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <?php
            }
            ?>
            <a href="#" class="btn btn-primary btn-block" id="add_link">Add URLs for pages needing updates here</a>
        </div>

        <?php if (Yii::$app->user->identity->role >= \app\models\enums\UserRoles::MANAGER) { ?>
            <?php
            $clientOptions = [];
            if (Yii::$app->user->identity->role == UserRoles::GENERAL) {
                $clientOptions = [
                    'startDate' => new \yii\web\JsExpression('moment(\'' . $model->model->created_at . '\').add(14, \'days\').format(\'MM-DD-YYYY\')')
                ];
            }
            echo $form->field($model, 'due_date')->widget(\app\widgets\DatePicker::class, [
                'clientOptions' => $clientOptions,
                'options' => [
                    'value' => date('m/d/Y', strtotime($model->due_date))
                ]
            ]) ?>
        <?php } else {
            echo $form->field($model, 'due_date')->textInput([
                'disabled' => true,
                'value' => date('m/d/Y', strtotime($model->due_date))
            ]);
        } ?>
        <?php if (Yii::$app->user->identity->role >= \app\models\enums\UserRoles::MANAGER) { ?>
            <?= $form->field($model, 'status')->dropDownList(\app\models\enums\JobStatus::getDataList()) ?>
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'mandate',
                    ['hintType' => \kartik\form\ActiveField::HINT_SPECIAL])->radioList(\skinka\php\TypeEnum\enums\YesNo::getDataList(),
                    [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            $options = array_merge(['label' => $label, 'value' => $value]);
                            if (!$checked) {
                                $options['disabled'] = Yii::$app->user->identity->role == \app\models\enums\UserRoles::GENERAL;
                            }
                            return '<div class="radio">' . Html::radio($name, $checked, $options) . '</div>';
                        }
                    ]) ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= \yii\bootstrap\Html::submitButton('Save', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>
    <div class="col-md-6 text-right">
        <?php if (Yii::$app->user->identity->role === \app\models\enums\UserRoles::ADMIN) { ?>
            <?= Html::a('Delete request', ['delete', 'id' => $model->model->id],
                ['class' => 'delete_job btn btn-sm btn-danger']) ?>
        <?php } ?>
    </div>
</div>

<script id="link-template" type="text/x-handlebars-template">
    <div class="row">
        <div class="col-md-10">
            <?= $form->field($model, 'links[]')->label(false) ?>
        </div>
        <div class="col-md-2">
            <a href="#" class="btn btn-danger delete"><i class="fa fa-trash"></i></a>
        </div>
    </div>
</script>