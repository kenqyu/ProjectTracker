<?php

namespace app\modules\reports\assets;

use app\assets\AppAsset;
use app\assets\HighchartsAsset;
use app\assets\DateRangePickerAsset;
use yii\web\AssetBundle;

class ReportAsset extends AssetBundle
{
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';

    public $css = [
        'css/pages/report.css'
    ];

    public $js = [
        'js/pages/report.js'
    ];

    public $depends = [
        AppAsset::class,
        HighchartsAsset::class,
        DateRangePickerAsset::class
    ];
}
