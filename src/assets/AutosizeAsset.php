<?php

namespace app\assets;

use yii\web\AssetBundle;

class AutosizeAsset extends AssetBundle
{
    public $sourcePath = '@bower/autosize/dist';
    public $css = [];
    public $js = [
        'autosize.min.js'
    ];
    public $depends = [];
}