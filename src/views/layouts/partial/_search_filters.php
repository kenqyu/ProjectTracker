<?php
/**
 * @var $this \yii\web\View
 */
$fields = collect([
    ['value' => 'job.legacy_id', 'name' => 'Job ID', 'type' => 'string'],
    ['value' => 'job.name', 'name' => 'Job Name', 'type' => 'string'],
    ['value' => 'job.description', 'name' => 'Description', 'type' => 'string'],
    ['value' => 'job.status', 'name' => 'Status', 'type' => 'status'],
    ['value' => 'jobLinks.link', 'name' => 'Link', 'type' => 'string'],
    ['value' => 'job.creator_id', 'name' => 'Requested By', 'type' => 'user'],
    ['value' => 'job.created_at', 'name' => 'Created On', 'type' => 'daterange'],
    ['value' => 'job.updated_at', 'name' => 'Updated On', 'type' => 'daterange'],
    ['value' => 'job.due_date', 'name' => 'Due Date', 'type' => 'daterange'],
    ['value' => 'job.budget', 'name' => 'Budget range', 'type' => 'range'],
    ['value' => 'job.cwa_id', 'name' => 'CWA', 'type' => 'cwa'],
    ['value' => 'workTypes.id', 'name' => 'Work type', 'type' => 'worktype'],
    ['value' => 'job.processing_unit_id', 'name' => 'Processing Department', 'type' => 'processingUnit'],
    ['value' => 'job.request_type_id', 'name' => 'Request Type', 'type' => 'requesttype'],
    ['value' => 'justifications.id', 'name' => 'Justification for request', 'type' => 'justifications'],
    ['value' => 'job.project_manager_id', 'name' => 'Project Manager', 'type' => 'user'],
    ['value' => 'job.project_lead_id', 'name' => 'Project Lead', 'type' => 'user'],
    ['value' => 'job.translation_manager_id', 'name' => 'Translation Manager', 'type' => 'user'],
    ['value' => 'job.agency_id', 'name' => 'Agency', 'type' => 'agency'],
    ['value' => 'iwcm_publishing_assignee_id', 'name' => 'iWCM Publishing Assignee', 'type' => 'user'],
    ['value' => 'job.estimate_amount', 'name' => 'Estimate Amount', 'type' => 'range'],
    ['value' => 'job.translation_needed', 'name' => 'Translation Needed', 'type' => 'boolean'],
    ['value' => 'job.ccc_impact', 'name' => 'CCC Impact', 'type' => 'boolean'],
    ['value' => 'job.one_voice', 'name' => 'One Voice', 'type' => 'boolean'],
    ['value' => 'job.ccc_contact_id', 'name' => 'CCC Contact', 'type' => 'user'],
    ['value' => 'sceApprovers.id', 'name' => 'SCE Approver', 'type' => 'user'],
    ['value' => 'job.content_expiration_date', 'name' => 'Content expiration date', 'type' => 'daterange'],
    ['value' => 'job.completed_on', 'name' => 'Completed / Canceled on', 'type' => 'daterange'],
    ['value' => 'job.published_on', 'name' => 'Publish date', 'type' => 'daterange'],
]);
$fieldOptions = '<option value="">Choose field...</option>';
foreach ($fields as $item) {
    $fieldOptions .= '<option value="' . $item['value'] . '" data-type="' . $item['type'] . '">' . $item['name'] . '</option>';
}
?>
<?php \yii\bootstrap\ActiveForm::begin(['action' => ['/jobs/jobs/search'], 'method' => 'get']) ?>

<p class="help-block">
    In order to generate a report, select your search criteria and select ‘Find’. On the search results
    page, go to ‘Export’ and select the report format. Select ‘OK’ on the confirmation window to proceed
    with downloading your report.
</p>

<div class="filters">
    <?php
    foreach (Yii::$app->request->get('filter', []) as $key => $item) {
        ?>
        <div class="item row" data-field="<?= $item['field'] ?>">
            <input type="hidden" name="filter[<?= $key ?>][field]" value="<?= $item['field'] ?>"/>
            <div class="col-md-5" style="overflow: hidden">
                <label>
                    <?= $fields->where('value', $item['field'])->first()['name'] ?>
                </label>
            </div>
            <div class="col-md-6">
                <?php
                switch ($fields->where('value', $item['field'])->first()['type']) {
                    case 'string':
                        echo '<input name="filter[' . $key . '][type]" value="like" type="hidden">';
                        echo \yii\bootstrap\Html::textInput('filter[' . $key . '][value]', $item['value'],
                            ['class' => 'form-control']);
                        break;
                    case 'boolean':
                        ?>
                        <input name="filter[<?= $key ?>][type]" value="boolean" type="hidden">
                        <div class="form-group">
                            <label><input type="radio" name=filter[<?= $key ?>][value]
                                          value="1" <?= $item['value'] == 1 ? 'checked' : '' ?>> Yes</label>&nbsp;&nbsp;
                            <label><input type="radio" name="filter[<?= $key ?>][value]"
                                          value="0" <?= $item['value'] == 0 ? 'checked' : '' ?>> No</label>
                        </div>
                        <?php
                        break;
                    case 'status':
                        ?>
                        <input name="filter[<?= $key ?>][type]" value="match" type="hidden">
                        <?= \yii\bootstrap\Html::dropDownList(
                        'filter[' . $key . '][value]',
                        $item['value'],
                        \app\models\enums\JobStatus::getDataList(),
                        ['class' => 'form-control'])
                        ?>
                        <?php
                        break;
                    case 'user':
                        ?>
                        <input name="filter[<?= $key ?>][type]" value="match" type="hidden">
                        <?= \yii\bootstrap\Html::dropDownList(
                        'filter[' . $key . '][value]',
                        $item['value'],
                        \app\models\User::getDataList(true),
                        ['class' => 'form-control'])
                        ?>
                        <?php
                        break;
                    case 'processingUnit':
                        ?>
                        <input name="filter[<?= $key ?>][type]" value="match" type="hidden">
                        <?= \yii\bootstrap\Html::dropDownList(
                        'filter[' . $key . '][value]',
                        $item['value'],
                        \app\models\ProcessingUnit::getDataList(),
                        ['class' => 'form-control'])
                        ?>
                        <?php
                        break;
                    case 'requesttype':
                        ?>
                        <input name="filter[<?= $key ?>][type]" value="match" type="hidden">
                        <?= \yii\bootstrap\Html::dropDownList(
                        'filter[' . $key . '][value]',
                        $item['value'],
                        \app\models\RequestType::getDataList(null),
                        ['class' => 'form-control'])
                        ?>
                        <?php
                        break;
                    case 'worktype':
                        ?>
                        <input name="filter[<?= $key ?>][type]" value="match" type="hidden">
                        <?= \yii\bootstrap\Html::dropDownList(
                        'filter[' . $key . '][value]',
                        $item['value'],
                        \app\models\WorkType::getDataList(true),
                        ['class' => 'form-control'])
                        ?>
                        <?php
                        break;
                    case 'justifications':
                        ?>
                        <input name="filter[<?= $key ?>][type]" value="match" type="hidden">
                        <?= \yii\bootstrap\Html::dropDownList(
                        'filter[' . $key . '][value]',
                        $item['value'],
                        \app\models\Justifications::getDataList(true),
                        ['class' => 'form-control'])
                        ?>
                        <?php
                        break;
                    case 'agency':
                        ?>
                        <input name="filter[<?= $key ?>][type]" value="match" type="hidden">
                        <?= \yii\bootstrap\Html::dropDownList(
                        'filter[' . $key . '][value]',
                        $item['value'],
                        \app\models\Agency::getDataList(true),
                        ['class' => 'form-control'])
                        ?>
                        <?php
                        break;
                    case 'cwa':
                        ?>
                        <input name="filter[<?= $key ?>][type]" value="match" type="hidden">
                        <?= \yii\bootstrap\Html::dropDownList(
                        'filter[' . $key . '][value]',
                        $item['value'],
                        \app\models\CWA::getDataList([], false),
                        ['class' => 'form-control'])
                        ?>
                        <?php
                        break;
                    case 'daterange':
                        ?>
                        <input name="filter[<?= $key ?>][type]" value="daterange" type="hidden">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control datepicker" name="filter[<?= $key ?>][value][0]"
                                       value="<?= $item['value'][0] ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control datepicker" name="filter[<?= $key ?>][value][1]"
                                       value="<?= $item['value'][1] ?>">
                            </div>
                        </div>
                        <?php
                        break;
                    case 'range':
                        ?>
                        <input name="filter[<?= $key ?>][type]" value="range" type="hidden">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="filter[<?= $key ?>][value][0]"
                                       value="<?= $item['value'][0] ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="filter[<?= $key ?>][value][1]"
                                       value="<?= $item['value'][1] ?>">
                            </div>
                        </div>
                        <?php
                        break;
                }
                ?>
            </div>
            <div class="col-md-1" style="padding: 0;">
                <a href="#" class="delete btn btn-default"><i class="fa fa-times"></i></a>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<div class="add row">
    <div class="col-md-12">
        <select class="form-control">
            <?= $fieldOptions ?>
        </select>
    </div>
</div>
<div class="row actions">
    <div class="col-md-12 text-center">
        <button class="btn btn-lg btn-primary">Find!</button>
    </div>
</div>
<?php \yii\bootstrap\ActiveForm::end() ?>
<script id="search-row" type="text/x-handlebars-template">
    <div class="item row" data-field="{{name}}">
        <input type="hidden" name="filter[{{id}}][field]" value="{{name}}"/>
        <div class="col-md-5">
            <label>{{humanName}}</label>
        </div>
        <div class="col-md-6">
            {{{type_content}}}
        </div>
        <div class="col-md-1" style="padding: 0;">
            <a href="#" class="delete btn btn-default"><i class="fa fa-times"></i></a>
        </div>
    </div>
</script>
<script id="search-type-string" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="like" type="hidden">
    <input name="filter[{{id}}][value]" type="text" class="form-control">
</script>
<script id="search-type-boolean" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="boolean" type="hidden">
    <div class="form-group">
        <label><input type="radio" name=filter[{{id}}][value] value="1" checked=""> Yes</label>&nbsp;&nbsp;
        <label><input type="radio" name="filter[{{id}}][value]" value="0"> No</label>
    </div>
</script>
<script id="search-type-status" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'filter[{{id}}][value]',
        null,
        \app\models\enums\JobStatus::getDataList(),
        ['class' => 'form-control'])
    ?>
</script>
<script id="search-type-user" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'filter[{{id}}][value]',
        null,
        \app\models\User::getDataList(true),
        ['class' => 'form-control'])
    ?>
</script>
<script id="search-type-worktype" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'filter[{{id}}][value]',
        null,
        \app\models\WorkType::getDataList(),
        ['class' => 'form-control'])
    ?>
</script>
<script id="search-type-justifications" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'filter[{{id}}][value]',
        null,
        \app\models\Justifications::getDataList(),
        ['class' => 'form-control'])
    ?>
</script>
<script id="search-type-agency" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'filter[{{id}}][value]',
        null,
        \app\models\Agency::getDataList(),
        ['class' => 'form-control'])
    ?>
</script>
<script id="search-type-cwa" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'filter[{{id}}][value]',
        null,
        \app\models\CWA::getDataList([], false),
        ['class' => 'form-control'])
    ?>
</script>
<script id="search-type-processingUnit" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'filter[{{id}}][value]',
        null,
        \app\models\ProcessingUnit::getDataList(),
        ['class' => 'form-control'])
    ?>
</script>
<script id="search-type-requesttype" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'filter[{{id}}][value]',
        null,
        \app\models\RequestType::getDataList(null),
        ['class' => 'form-control'])
    ?>
</script>
<script id="search-type-daterange" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="daterange" type="hidden">
    <div class="row">
        <div class="col-md-6">
            <input type="text" class="form-control datepicker" name="filter[{{id}}][value][0]">
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control datepicker" name="filter[{{id}}][value][1]">
        </div>
    </div>
</script>
<script id="search-type-range" type="text/x-handlebars-template">
    <input name="filter[{{id}}][type]" value="range" type="hidden">
    <div class="row">
        <div class="col-md-6">
            <input type="text" class="form-control" name="filter[{{id}}][value][0]">
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" name="filter[{{id}}][value][1]">
        </div>
    </div>
</script>