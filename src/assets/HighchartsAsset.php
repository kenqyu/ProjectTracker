<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class HighchartsAsset extends AssetBundle
{
    public $sourcePath = '@bower/highcharts';
    public $css = [];
    public $js = [
        'highcharts.js'
    ];
    public $depends = [
        JqueryAsset::class
    ];
}
