<?php
namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class CaretJsAsset extends AssetBundle
{
    public $sourcePath = '@npm/jquery.caret/dist';
    public $css = [];
    public $js = [
        'jquery.caret.min.js'
    ];
    public $depends = [
        JqueryAsset::class
    ];
}