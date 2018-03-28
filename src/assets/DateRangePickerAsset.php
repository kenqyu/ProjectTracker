<?php

namespace app\assets;

use app\assets\MomentJsAsset;
use yii\web\AssetBundle;

class DateRangePickerAsset extends AssetBundle
{
    public $sourcePath = '@npm/bootstrap-daterangepicker';
    public $css = [
        'daterangepicker.css'
    ];
    public $js = [
        'daterangepicker.js'
    ];
    public $depends = [
        MomentJsAsset::class
    ];
}