<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

            <h3 class="text-center">
                <?= nl2br(Html::encode($message)) ?>
            </h3>
            <br>
            <p class="text-center">
                The above error occurred while the web server was processing your request.
            </p>
            <p class="text-center">
                Please <a style="text-decoration: underline;"
                          href="mailto:sce.comproduction@sce.com?subject=<?= Html::encode('SCE CX Requests and Project Tracker Error (' . time() . '-' . $name . ')') ?>">contact
                    us</a> if
                you think this is a server error. Thank you.
            </p>
        </div>
    </div>
</div>
