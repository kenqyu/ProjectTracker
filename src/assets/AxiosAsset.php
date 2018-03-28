<?php

namespace app\assets;

use yii\web\AssetBundle;

class AxiosAsset extends AssetBundle
{
    public $sourcePath = '@bower/axios/dist';
    public $css = [];
    public $js = [
        'axios.min.js'
    ];
    public $depends = [
        ES6PromiseAsset::class
    ];
}