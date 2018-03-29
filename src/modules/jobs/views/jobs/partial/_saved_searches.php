<?php
/**
 * @var $this \yii\web\View
 */
?>
<div class="row">
    <div class="col-md-12">
        <?php foreach (\app\models\SavedSearch::find()->where(['user_id' => Yii::$app->user->id])->all() as $item) {
            $form = \yii\bootstrap\ActiveForm::begin(['action' => ['search'], 'method' => 'get']);
            foreach (json_decode($item->data, true) as $key => $value) {
                echo \yii\bootstrap\Html::hiddenInput('filter[' . $key . '][field]', $value['field']);
                echo \yii\bootstrap\Html::hiddenInput('filter[' . $key . '][type]', $value['type']);
                if (is_array($value['value'])) {
                    echo \yii\bootstrap\Html::hiddenInput('filter[' . $key . '][value][0]', $value['value'][0]);
                    echo \yii\bootstrap\Html::hiddenInput('filter[' . $key . '][value][1]', $value['value'][1]);
                } else {
                    echo \yii\bootstrap\Html::hiddenInput('filter[' . $key . '][value]', $value['value']);
                }
            }
            echo \yii\bootstrap\Html::submitButton($item->title, ['class' => 'btn btn-default']);
            \yii\bootstrap\ActiveForm::end();
        } ?>
    </div>
</div>
