<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\models\enums\UserRoles;
use yii\helpers\Html;

\app\assets\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= !empty($this->title) ? Html::encode($this->title) : 'SCE Project Tracker' ?></title>

    <script src="//cdn.ravenjs.com/3.18.1/raven.min.js" crossorigin="anonymous"></script>
    <script>Raven.config('<?= getenv('SENTRY_PUBLIC_DSN') ?>').install();</script>
    <?php if (!Yii::$app->user->isGuest) {
        ?>
        <script>
            Raven.setUserContext({
                username: '<?= Yii::$app->user->identity->username ?>',
                email: '<?= Yii::$app->user->identity->email ?>',
                id: '<?= Yii::$app->user->identity->id ?>'
            })
        </script>
        <?php
    } ?>

    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div id="body">
    <?php if (getenv('STAGING') == 1) { ?>
        <div class="staging_server">
            <h1>Warning!</h1>
            <h2>This is staging/testing server. All changes will not be applied to live server and can be erased at any
                moment.</h2>
        </div>
    <?php } ?>
    <?= $this->render('partial/_header') ?>

    <?= $content ?>
</div>

<?= $this->render('partial/_sidebar') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
