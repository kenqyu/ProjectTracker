<?php
/**
 * @var $this \yii\web\View
 */

use app\modules\jobs\models\CreateJobForm;
use kartik\form\ActiveForm;

?>
<?php
$model = isset($model) && $model ? $model : new CreateJobForm();
$form = ActiveForm::begin(['action' => ['create'], 'options' => ['enctype' => 'multipart/form-data']])
?>
    <div id="create_new_job">
        <div class="step_0">
            <p>
                We’ve enhanced our project tracker. To begin your request, please select the department that will
                complete it and the request type(s) below.
                <br>
                <br>
                Helpful tips before continuing with your request:
            </p>
            <ul>
                <li><strong>Select more than one Request Type only if you have two or more <span
                                style="text-decoration: underline">different</span> requests
                        related to the same project or task. If you select two or more request types, you will need to
                        complete details for each request type.</strong>
                </li>
                <li>For Marketing, content and other SCE.com-related requests, please select <i>Customer Engagement</i>
                    as the Processing Department.
                </li>
                <li>For data extraction and data analysis requests, please select <i>Data Analytics & Information
                        Compliance</i>.
                </li>
                <li>For marketing research and market intelligence requests, please select <i>Customer Insights</i>.
                </li>
                <li>For Customer Journey Mapping and blueprinting requests, please select <i>Customer Experience Design</i>.
                </li>
            </ul>
            <div class="form-group">
                <label for="department">Processing Department</label>
                <?= \yii\helpers\Html::dropDownList(
                    'CreateJobForm[processing_unit]',
                    null,
                    \app\models\ProcessingUnit::getDataList(false, null, null, true),
                    ['class' => 'form-control', 'id' => 'processing_unit']
                ) ?>
            </div>

            <div class="form-group request_types">
                <label for="request_type">Request type(s)</label>
                <div class="holder">
                    <?= \yii\helpers\Html::checkboxList('CreateJobForm[request_type][]', null,
                        \app\models\RequestType::getDataList(3)) ?>
                </div>
            </div>

            <p class="text-right">
                <a href="#" class="cancel btn btn-info">Cancel</a>
                <a href="#" class="btn btn-success next_step"
                   data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading next step" style="display: none;">Next
                    step</a>
            </p>
        </div>
        <div class="step_1" style="display: none;">
            <?= $form->field($model, 'name')
                ->label('Job Name <i class="glyphicon glyphicon-question-sign text-info"></i>', [
                    'data-toggle' => 'tooltip',
                    'title' => 'For data requests, the Job Name is the same as the Request Title'
                ]) ?>
            <?= $form->field($model, 'description')
                ->textarea()
                ->label('Job Description <i class="glyphicon glyphicon-question-sign text-info"></i>', [
                    'data-toggle' => 'tooltip',
                    'title' => 'Please provide as much detail as possible about your request, including justification for the request.'
                ]) ?>

            <?= $form->field($model, 'due_date')->widget(\app\widgets\DatePicker::class, [
                'clientOptions' => [
                    'startDate' => new \yii\web\JsExpression('moment().add(14, \'days\').format(\'MM-DD-YYYY\')')
                ]
            ])->label('Due Date <i class="glyphicon glyphicon-question-sign text-info"></i>', [
                'data-toggle' => 'tooltip',
                'title' => 'Standard turnaround time for requests is two weeks. For data requests, your project manager can adjust the deadline as needed.'
            ]) ?>
            <?= $form->field($model, 'approver')
                ->label('Approver <i class="glyphicon glyphicon-question-sign text-info"></i>', [
                    'data-toggle' => 'tooltip',
                    'title' => 'For content requests, this is the content owner, program manager, or original requestor. For data requests, this is the person who should complete the request.'
                ]) ?>

            <div class="row">
                <div class="col-md-6">
                    <a href="#" class="back btn btn-info">Back</a>
                </div>
                <div class="col-md-6 text-right">
                    <a href="#" class="cancel btn btn-info">Cancel</a>
                    <a href="#" class="btn btn-success next_step"
                       data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading next step">Next
                        step</a>
                </div>
            </div>
        </div>
        <div class="additional_steps">

        </div>
    </div>
<?php ActiveForm::end() ?>