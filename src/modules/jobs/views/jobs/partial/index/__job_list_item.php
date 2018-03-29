<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Job
 */
?>
<a href="<?= \yii\helpers\Url::to(['update', 'id' => $model->id]) ?>" class="job">
    <div class="head">
        <p class="title">#<?= $model->legacy_id ?></p>
        <span>updated <?= Yii::$app->formatter->asTime($model->updated_at, 'php:m/d/Y') ?></span>
    </div>

    <p><?= \yii\helpers\Html::encode($model->name) ?></p>
    <p class="author">By <?= $model->creator->first_name . ' ' . substr($model->creator->last_name, 0, 1) ?>.</p>
</a>
