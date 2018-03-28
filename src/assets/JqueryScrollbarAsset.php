<?php
namespace app\assets;

use yii\web\AssetBundle;

class JqueryScrollbarAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery.scrollbar';
    public $css = [
        'jquery.scrollbar.css',
    ];
    public $js = [
        'jquery.scrollbar.min.js'
    ];
    public $depends = [];
}
