<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Job
 */
$list = [];
$comments = \app\models\JobComment::find()
    ->joinWith(['user'])
    ->where(['job_id' => $model->id])
    ->orderBy(['created_at' => SORT_DESC]);
$comments = $comments->all();
$files = \app\models\JobFile::find()->joinWith(['user'])->where(['job_id' => $model->id])->orderBy(['created_at' => SORT_DESC])->all();
foreach ($comments as $item) {
    $list[] = [
        'type' => 'comment',
        'date' => $item->created_at,
        'model' => $item
    ];
}
foreach ($files as $item) {
    $list[] = [
        'type' => 'file',
        'date' => $item->created_at,
        'model' => $item
    ];
}
$list = collect($list)->sortBy('date', SORT_REGULAR, true);

foreach ($list as $item) {
    if ($item['type'] == 'file') {
        ?>
        <div class="item file" data-id="<?= $item['model']->id ?>">
            <i class="fa fa-file"></i>
            <div class="info">
                <a href="<?= $item['model']->getUrl() ?>" target="_blank" class="title"><?= $item['model']->title ?></a>
                <span class="size"><?= $item['model']->getSize() ?></span>
            </div>
            <?php
            if (Yii::$app->user->identity->role === \app\models\enums\UserRoles::ADMIN) {
                ?>
                <div class="delete_container">
                    <a href="#" class="btn btn-lg btn-danger delete_file" title="Delete attachment" rel="tooltip"><i class="fa fa-trash"></i></a>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    } elseif ($item['type'] == 'comment') {
        /**
         * @var $obj \app\models\JobComment
         */
        $obj = $item['model'];
        ?>
        <div class="item">
            <div class="meta">
                <strong><?= $obj->user->getShortName() ?>
                    • </strong><i><?= Yii::$app->formatter->asDatetime($obj->created_at) ?></i>
            </div>
            <div class="message">
                <?= nl2br($obj->body) ?>
            </div>
        </div>
        <?php
    }
}