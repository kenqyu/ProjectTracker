<?php

/* @var $this \yii\web\View */
/* @var $content string */

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
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<header class="simple container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="/" class="logo">
                    <img src="/static/images/logo.png" alt="Edison">
                </a>
            </div>
        </div>
    </div>
</header>

<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
