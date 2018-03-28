<?php
namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class AtJsAsset extends AssetBundle
{
    public $sourcePath = '@npm/at.js/dist';
    public $css = [
        'css/jquery.atwho.min.css'
    ];
    public $js = [
        'js/jquery.atwho.min.js'
    ];
    public $depends = [
        JqueryAsset::class,
        CaretJsAsset::class
    ];
}