<?php

namespace app\assets;

use yii\web\AssetBundle;

class ES6PromiseAsset extends AssetBundle
{
    public $sourcePath = '@bower/es6-promise';
    public $css = [];
    public $js = [
        'es6-promise.auto.min.js'
    ];
    public $depends = [];
}