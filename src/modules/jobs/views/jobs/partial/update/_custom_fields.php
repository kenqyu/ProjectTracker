<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\jobs\models\UpdateJobForm
 * @var $form \yii\bootstrap\ActiveForm
 */
foreach ($model->customFields as $key => $field) {
    switch ($field['type']) {
        case \app\models\enums\CustomFormFieldType::VARCHAR:
            echo $form->field($model, 'customFields[' . $key . '][value]')->label($field['label']);
            break;
        case \app\models\enums\CustomFormFieldType::DATE:
            echo $form->field($model, 'customFields[' . $key . '][value]')
                ->label($field['label'])
                ->widget(\app\widgets\DatePicker::class, [
                    'options' => [
                        'value' => !empty($field['value']) ? $field['value'] : ''
                    ]
                ]);
            break;
        case \app\models\enums\CustomFormFieldType::DATETIME:
            echo $form->field($model, 'customFields[' . $key . '][value]')
                ->label($field['label'])
                ->widget(\dosamigos\datetimepicker\DateTimePicker::class, [
                    'options' => [
                        'value' => !empty($field['value']) ? $field['value'] : ''
                    ]
                ]);
            break;
        case \app\models\enums\CustomFormFieldType::SELECT:
            echo $form->field($model, 'customFields[' . $key . '][value]')
                ->label($field['label'])
                ->dropDownList(\yii\helpers\ArrayHelper::map(json_decode($field['options']), function ($item) {
                    return $item;
                }, function ($item) {
                    return $item;
                }));
            break;
        case \app\models\enums\CustomFormFieldType::CHECKBOX:
            echo $form->field($model, 'customFields[' . $key . '][value]')
                ->label($field['label'])
                ->checkbox(['label' => $field['label']]);
            break;
        case \app\models\enums\CustomFormFieldType::CHECKBOX_LIST:
            echo $form->field($model, 'customFields[' . $key . '][value]')
                ->label($field['label'])
                ->checkboxList(
                    \yii\helpers\ArrayHelper::map(json_decode($field['options']), function ($item) {
                        return $item;
                    }, function ($item) {
                        return $item;
                    }),
                    ['label' => $field['label']]
                );
            break;
    }
} ?>
<div class="row">
    <div class="col-md-12">
        <?= \yii\bootstrap\Html::submitButton('Save', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>
</div>
