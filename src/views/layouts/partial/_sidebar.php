<div id="sidebar">
    <div class="container">
        <div class="row">
            <div class="col-md-11">
                <a href="<?= \yii\helpers\Url::to(['/notifications/notifications/index']) ?>" class="title">Notifications</a>
            </div>
            <div class="col-md-1 text-right">
                <button class="close"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <a href="<?= \yii\helpers\Url::to(['/notifications/notifications/mark-all-as-read']) ?>"
                   data-method="post" class="mark_as_read">Mark all as read</a>
            </div>
        </div>
        <hr>

        <div class="list">
            <?php foreach (\app\services\NotificationService::getInstance()->getNotifications(Yii::$app->user->identity,
                20) as $item) { ?>
                <div class="item" data-id="<?= $item->id ?>">
                    <div class="content">
                        <?= $item->read ? '' : '<span class="label label-success new">NEW</span>' ?>
                        <?= $item->message ?>
                    </div>
                    <div class="meta">
                    <span rel="tooltip"
                          title="<?= Yii::$app->formatter->asDatetime($item->date) ?>"><?= Yii::$app->formatter->asRelativeTime($item->date) ?></span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>