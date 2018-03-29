<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\reports\models\BuilderForm
 */

$fields = collect([
    ['value' => 'job.legacy_id', 'name' => 'Job ID', 'type' => 'string'],
    ['value' => 'job.name', 'name' => 'Job Name', 'type' => 'string'],
    ['value' => 'job.description', 'name' => 'Description', 'type' => 'string'],
    ['value' => 'job.status', 'name' => 'Status', 'type' => 'status'],
    ['value' => 'job.creator_id', 'name' => 'Requested By', 'type' => 'user'],
    //['value' => 'job.created_at', 'name' => 'Created On', 'type' => 'daterange'],
    //['value' => 'job.updated_at', 'name' => 'Updated On', 'type' => 'daterange'],
    //['value' => 'job.due_date', 'name' => 'Due Date', 'type' => 'daterange'],
    //['value' => 'job.budget', 'name' => 'Budget range', 'type' => 'range'],
    ['value' => 'job.request_type_id', 'name' => 'Request Type', 'type' => 'requesttype'],
    ['value' => 'job.project_manager_id', 'name' => 'Project Manager', 'type' => 'user'],
    ['value' => 'job.project_lead_id', 'name' => 'Project Lead', 'type' => 'user'],
    ['value' => 'job.translation_manager_id', 'name' => 'Translation Manager', 'type' => 'user'],
    ['value' => 'job.agency_id', 'name' => 'Agency', 'type' => 'agency'],
    ['value' => 'job.iwcm_publishing_assignee_id', 'name' => 'iWCM Publishing Assignee', 'type' => 'user'],
    //['value' => 'job.estimate_amount', 'name' => 'Estimate Amount', 'type' => 'range'],
    ['value' => 'job.translation_needed', 'name' => 'Translation Needed', 'type' => 'boolean'],
    ['value' => 'job.ccc_impact', 'name' => 'CCC Impact', 'type' => 'boolean'],
    ['value' => 'job.one_voice', 'name' => 'One Voice', 'type' => 'boolean'],
    ['value' => 'job.ccc_contact_id', 'name' => 'CCC Contact', 'type' => 'user'],
    //['value' => 'job.content_expiration_date', 'name' => 'Content expiration date', 'type' => 'daterange'],
    //['value' => 'job.completed_on', 'name' => 'Completed / Canceled on', 'type' => 'daterange'],
    //['value' => 'job.published_on', 'name' => 'Publish date', 'type' => 'daterange'],
]);

?>
<div class="modal fade" id="filters_modal" tabindex="-1" role="dialog" aria-labelledby="filters_modal" data-cwa="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Global Filters</h4>
            </div>
            <div class="modal-body">
                <div class="filters-list">
                    <?php
                    foreach ($model->getModel()->getDecodedFilters() as $key => $item) {
                        ?>
                        <div class="item row" data-field="<?= $item['field'] ?>">
                            <input type="hidden" name="BuilderForm[filters][<?= $key ?>][field]"
                                   value="<?= $item['field'] ?>"/>
                            <div class="col-md-5" style="overflow: hidden">
                                <label>
                                    <?= $fields->where('value', $item['field'])->first()['name'] ?>
                                </label>
                            </div>
                            <div class="col-md-5">
                                <?php
                                switch ($fields->where('value', $item['field'])->first()['type']) {
                                    case 'string':
                                        echo \yii\helpers\Html::hiddenInput('BuilderForm[filters][' . $key . '][type]', 'like');
                                        echo \yii\bootstrap\Html::textInput('BuilderForm[filters][' . $key . '][value]',
                                            $item['value'], ['class' => 'form-control']);
                                        break;
                                    case 'boolean':
                                        echo \yii\helpers\Html::hiddenInput('BuilderForm[filters][' . $key . '][type]', 'boolean');
                                        ?>
                                        <div class="form-group">
                                            <label><input type="radio" name=BuilderForm[filters][<?= $key ?>][value]
                                                          value="1" <?= $item['value'] == 1 ? 'checked' : '' ?>>
                                                Yes</label>&nbsp;&nbsp;
                                            <label><input type="radio" name="BuilderForm[filters][<?= $key ?>][value]"
                                                          value="0" <?= $item['value'] == 0 ? 'checked' : '' ?>>
                                                No</label>
                                        </div>
                                        <?php
                                        break;
                                    case 'status':
                                        echo \yii\helpers\Html::hiddenInput('BuilderForm[filters][' . $key . '][type]', 'match');
                                        echo \yii\bootstrap\Html::dropDownList(
                                            'BuilderForm[filters][' . $key . '][value]',
                                            $item['value'],
                                            \app\models\enums\JobStatus::getDataList(),
                                            ['class' => 'form-control']
                                        );
                                        break;
                                    case 'user':
                                        echo \yii\helpers\Html::hiddenInput('BuilderForm[filters][' . $key . '][type]', 'match');
                                        echo \yii\bootstrap\Html::dropDownList(
                                            'BuilderForm[filters][' . $key . '][value]',
                                            $item['value'],
                                            \app\models\User::getDataList(true),
                                            ['class' => 'form-control']
                                        );
                                        break;
                                    case 'requesttype':
                                        echo \yii\helpers\Html::hiddenInput('BuilderForm[filters][' . $key . '][type]', 'match');
                                        echo \yii\bootstrap\Html::dropDownList(
                                            'BuilderForm[filters][' . $key . '][value]',
                                            $item['value'],
                                            \app\models\RequestType::getDataList(null),
                                            ['class' => 'form-control']
                                        );
                                        break;
                                    case 'worktype':
                                        echo \yii\helpers\Html::hiddenInput('BuilderForm[filters][' . $key . '][type]', 'match');
                                        echo \yii\bootstrap\Html::dropDownList(
                                            'BuilderForm[filters][' . $key . '][value]',
                                            $item['value'],
                                            \app\models\WorkType::getDataList(),
                                            ['class' => 'form-control']
                                        );
                                        break;
                                    case 'justifications':
                                        echo \yii\helpers\Html::hiddenInput('BuilderForm[filters][' . $key . '][type]', 'match');
                                        echo \yii\bootstrap\Html::dropDownList(
                                            'BuilderForm[filters][' . $key . '][value]',
                                            $item['value'],
                                            \app\models\Justifications::getDataList(true),
                                            ['class' => 'form-control']
                                        );
                                        break;
                                    case 'agency':
                                        echo \yii\helpers\Html::hiddenInput('BuilderForm[filters][' . $key . '][type]', 'match');
                                        echo \yii\bootstrap\Html::dropDownList(
                                            'BuilderForm[filters][' . $key . '][value]',
                                            $item['value'],
                                            \app\models\Agency::getDataList(true),
                                            ['class' => 'form-control']
                                        );
                                        break;
                                    case 'cwa':
                                        echo \yii\helpers\Html::hiddenInput('BuilderForm[filters][' . $key . '][type]', 'match');
                                        echo \yii\bootstrap\Html::dropDownList(
                                            'BuilderForm[filters][' . $key . '][value]',
                                            $item['value'],
                                            \app\models\CWA::getDataList([], false),
                                            ['class' => 'form-control']
                                        );
                                        break;
                                    case 'daterange':
                                        ?>
                                        <input name="BuilderForm[filters][<?= $key ?>][type]" value="daterange"
                                               type="hidden">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control datepicker"
                                                       name="BuilderForm[filters][<?= $key ?>][value][0]"
                                                       value="<?= $item['value'][0] ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control datepicker"
                                                       name="BuilderForm[filters][<?= $key ?>][value][1]"
                                                       value="<?= $item['value'][1] ?>">
                                            </div>
                                        </div>
                                        <?php
                                        break;
                                    case 'range':
                                        ?>
                                        <input name="BuilderForm[filters][<?= $key ?>][type]" value="range"
                                               type="hidden">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"
                                                       name="BuilderForm[filters][<?= $key ?>][value][0]"
                                                       value="<?= $item['value'][0] ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"
                                                       name="BuilderForm[filters][<?= $key ?>][value][1]"
                                                       value="<?= $item['value'][1] ?>">
                                            </div>
                                        </div>
                                        <?php
                                        break;
                                }
                                ?>
                            </div>
                            <div class="col-md-2" style="padding: 0;">
                                <a href="#" class="delete btn btn-default btn-block">Delete</a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <?= \yii\helpers\Html::dropDownList('new_filter', null, $fields->mapWithKeys(function ($item) {
                            return [$item['value'] => $item['name']];
                        })->all(), [
                            'class' => 'form-control',
                            'id' => 'new_filter',
                            'options' => $fields->mapWithKeys(function ($item) {
                                return [$item['value'] => ['data-type' => $item['type']]];
                            })->all()
                        ]);
                        ?>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-primary btn-block" id="add_filter">Add filter</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script id="filter-row" type="text/x-handlebars-template">
    <div class="item row" data-field="{{name}}">
        <input type="hidden" name="BuilderForm[filters][{{id}}][field]" value="{{name}}"/>
        <div class="col-md-5">
            <label>{{humanName}}</label>
        </div>
        <div class="col-md-5">
            {{{type_content}}}
        </div>
        <div class="col-md-2" style="padding: 0;">
            <a href="#" class="delete btn btn-default btn-block">Delete</a>
        </div>
    </div>
</script>
<script id="filter-type-string" type="text/x-handlebars-template">
    <input name="BuilderForm[filters][{{id}}][type]" value="like" type="hidden">
    <input name="BuilderForm[filters][{{id}}][value]" type="text" class="form-control">
</script>
<script id="filter-type-boolean" type="text/x-handlebars-template">
    <input name="BuilderForm[filters][{{id}}][type]" value="boolean" type="hidden">
    <div class="form-group">
        <label><input type="radio" name=BuilderForm[filters][{{id}}][value] value="1" checked=""> Yes</label>&nbsp;&nbsp;
        <label><input type="radio" name="BuilderForm[filters][{{id}}][value]" value="0"> No</label>
    </div>
</script>
<script id="filter-type-status" type="text/x-handlebars-template">
    <input name="BuilderForm[filters][{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[filters][{{id}}][value]',
        null,
        \app\models\enums\JobStatus::getDataList(),
        ['class' => 'form-control'])
    ?>
</script>
<script id="filter-type-user" type="text/x-handlebars-template">
    <input name="BuilderForm[filters][{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[filters][{{id}}][value]',
        null,
        \app\models\User::getDataList(true),
        ['class' => 'form-control'])
    ?>
</script>
<script id="filter-type-worktype" type="text/x-handlebars-template">
    <input name="BuilderForm[filters][{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[filters][{{id}}][value]',
        null,
        \app\models\WorkType::getDataList(),
        ['class' => 'form-control'])
    ?>
</script>
<script id="filter-type-agency" type="text/x-handlebars-template">
    <input name="BuilderForm[filters][{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[filters][{{id}}][value]',
        null,
        \app\models\Agency::getDataList(),
        ['class' => 'form-control'])
    ?>
</script>
<script id="filter-type-processingUnit" type="text/x-handlebars-template">
    <input name="BuilderForm[filters][{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[filters][{{id}}][value]',
        null,
        \app\models\ProcessingUnit::getDataList(),
        ['class' => 'form-control'])
    ?>
</script>
<script id="filter-type-requesttype" type="text/x-handlebars-template">
    <input name="BuilderForm[filters][{{id}}][type]" value="match" type="hidden">
    <?= \yii\bootstrap\Html::dropDownList(
        'BuilderForm[filters][{{id}}][value]',
        null,
        \app\models\RequestType::getDataList(null),
        ['class' => 'form-control'])
    ?>
</script>
<script id="filter-type-daterange" type="text/x-handlebars-template">
    <input name="BuilderForm[filters][{{id}}][type]" value="daterange" type="hidden">
    <div class="row">
        <div class="col-md-6">
            <input type="text" class="form-control datepicker" name="BuilderForm[filters][{{id}}][value][0]">
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control datepicker" name="BuilderForm[filters][{{id}}][value][1]">
        </div>
    </div>
</script>
<script id="filter-type-range" type="text/x-handlebars-template">
    <input name="BuilderForm[filters][{{id}}][type]" value="range" type="hidden">
    <div class="row">
        <div class="col-md-6">
            <input type="text" class="form-control" name="BuilderForm[filters][{{id}}][value][0]">
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" name="BuilderForm[filters][{{id}}][value][1]">
        </div>
    </div>
</script>