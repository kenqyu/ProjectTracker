<?php
namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class MCustomScrollbarAsset extends AssetBundle
{
    public $sourcePath = '@bower/malihu-custom-scrollbar-plugin';
    public $css = [
        'jquery.mCustomScrollbar.min.css'
    ];
    public $js = [
        'jquery.mCustomScrollbar.concat.min.js'
    ];
    public $depends = [
        JqueryAsset::class
    ];
}
