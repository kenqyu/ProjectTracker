<?php
namespace app\assets;

use yii\web\AssetBundle;

class HandlebarsAsset extends AssetBundle
{
    public $sourcePath = '@bower/handlebars/';
    public $css = [
    ];
    public $js = [
        'handlebars.min.js'
    ];
    public $depends = [];
}