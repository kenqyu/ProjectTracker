<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\jobs\models\UpdateJobForm
 * @var $form \yii\bootstrap\ActiveForm
 */
$invoicesSum = collect($model->model->jobInvoices)->sum(function ($item) {
    return $item->amount;
});
$costCentersCollection = collect($model->model->jobCostCenters)->map(function ($item) {
    /**
     * @var $item \app\models\JobCostCenter
     */
    return $item->toArray();
});
$CWAs = \yii\helpers\ArrayHelper::map($model->model->cwa, 'id', 'id');
$this->registerJs('new JobsUpdate();');
?>
<div class="row accounting">
    <div class="col-md-12">

        <?= $form->field($model, 'internal_only',
            ['hintType' => \kartik\form\ActiveField::HINT_SPECIAL])->radioList([1 => 'Yes', 0 => 'No']) ?>

        <div class="cost-centers">
            <div class="add">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="new_cost_center_name" data-toggle="popover"
                                   style="border-bottom: 1px dashed #888;"
                                   data-placement="top"
                                   data-content="Enter cost center number(s) and the percentage of overall project costs that should be applied to each cost center. After entering info, please click the “Save” button.">
                                Cost Center
                                <i class="glyphicon glyphicon-question-sign text-info"></i>
                            </label>
                            <input type="text" id="new_cost_center_name" class="form-control" maxlength="8">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="new_cost_center_percent">Percent</label>
                            <input type="text" id="new_cost_center_percent" class="form-control">

                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label" style="width: 100%;">&nbsp;</label>
                        <a href="#" class="btn btn-primary" id="add_cost_center">Save</a>
                    </div>
                </div>
            </div>
            <div class="list">
                <table class="table">
                    <tbody>
                    <?php foreach ($model->model->jobCostCenters as $item) { ?>
                        <tr data-id="<?= $item->id ?>"
                            data-percent="<?= $item->percent ?>">
                            <td><?= $item->cost_center ?></td>
                            <td><?= $item->percent ?>%</td>
                            <td width="40"><a class="delete" href="#">Delete</a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="internal_only_block" style="display: <?= $model->internal_only ? 'none' : 'block' ?>">
            <hr>
            <div class="cwa">
                <div class="row">
                    <div class="col-md-10">
                        <label for="new_cwa">CWA Number - CWA Name</label>
                        <?= \yii\helpers\Html::dropDownList('new_cwa', null,
                            \app\models\CWA::getDataList($CWAs, true, true, ''),
                            [
                                'id' => 'new_cwa',
                                'class' => 'form-control',
                                'options' => \app\models\CWA::getDataListInfo([$model->model->id], $CWAs, true)
                            ]) ?>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <a href="#" id="add_cwa" class="btn btn-primary btn-block"><i class="fa fa-plus"></i></a>
                    </div>
                </div>
                <div class="list">
                    <table class="table">
                        <tbody>
                        <?php foreach ($model->model->cwa as $item) {
                            $invoices = $model->model->countInvoices($item->id);
                            $total = $model->model->totalInvoices($item->id);
                            ?>
                            <tr
                                    data-id="<?= $item->id ?>"
                                    data-name="<?= $item->number . ' - ' . $item->name ?>"
                                    data-invoices="<?= $invoices ?>"
                                    data-amount="<?= $item->amount ?>"
                                    data-used="<?= $item->getTotalUsage([$model->model->id]) ?>"
                            >
                                <td><?= $item->number . ' - ' . $item->name ?></td>
                                <td><a class="invoices" href="#">Invoices
                                        ($<span class="invoice_total"><?= number_format($total, 2) ?></span>)</a></td>
                                <td width="40"><a class="delete" href="#">Delete</a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="cwa_modal" tabindex="-1" role="dialog" aria-labelledby="cwa_modal" data-cwa="">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= \yii\bootstrap\Html::submitButton('Save', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>
</div>
<script id="invoice-template" type="text/x-handlebars-template">
    <tr data-id="{{id}}" data-amount="{{amount}}">
        <td width="100">{{number}}</td>
        <td>{{date}}</td>
        <td>${{amount}} <span class="color-code" style="background: #4D863D"></span></td>
        <td><?= Yii::$app->user->identity->getShortName() ?></td>
        <td width="40"><a class="delete" href="#">Delete</a></td>
    </tr>
</script>
<script id="cost-center-template" type="text/x-handlebars-template">
    <tr data-id="{{id}}"
        data-percent="{{percent}}">
        <td>{{cost_center}}</td>
        <td>{{percent}}%</td>
        <td width="40"><a class="delete" href="#">Delete</a></td>
    </tr>
</script>
<script id="cwa-template" type="text/x-handlebars-template">
    <tr
            data-id="{{id}}"
            data-name="{{name}}"
            data-amount="{{amount}}"
            data-used="{{used}}"
    >
        <td>{{name}}</td>
        <td><a class="invoices" href="#">Invoices ($<span class="invoice_total">0</span>)</a></td>
        <td width="40"><a class="delete" href="#">Delete</a></td>
    </tr>
</script>
<script id="cwa-modal-template" type="text/x-handlebars-template">
    <div class="invoices">
        <div class="add">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="new_invoice_date">Date</label>
                        <?= \app\widgets\DatePicker::widget([
                            'name' => 'new_invoice_date',
                            'options' => [
                                'value' => date('m/d/Y'),
                                'id' => 'new_invoice_date',
                                'class' => 'form-control',
                                'placeholder' => 'Choose date'
                            ]
                        ]); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label" for="new_invoice_number">Number</label>
                        <input type="text" id="new_invoice_number" class="form-control"
                               placeholder="Add number">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label" for="new_invoice_amount">Amount</label>
                        <input type="text" id="new_invoice_amount" class="form-control"
                               placeholder="Add amount">
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="#" id="add_invoice" class="btn btn-lg btn-primary"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>

        <div class="list">
            <table class="table">
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="balance-cwa col-md-offset-2 col-md-8">
            <h4>CWA Breakdown</h4>

            <p class="balance">
                <strong>CWA Remaining Amount:</strong> $<span id="cwa_remaining">0</span>
            </p>

            <div class="progress_holder"></div>
        </div>
    </div>
</script>