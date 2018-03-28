<?php
namespace app\assets;

use yii\web\AssetBundle;

class MomentJsAsset extends AssetBundle
{
    public $sourcePath = '@npm/moment/';
    public $css = [
    ];
    public $js = [
        'moment.js'
    ];
    public $depends = [];
}
