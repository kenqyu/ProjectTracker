<?php

namespace app\modules\reports\assets;

use app\assets\AppAsset;
use app\assets\AxiosAsset;
use app\assets\DatePickerAsset;
use app\assets\HandlebarsAsset;
use app\assets\SortableAsset;
use yii\web\AssetBundle;

class BuilderAsset extends AssetBundle
{
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';

    public $css = [
        'css/pages/report_builder.css'
    ];

    public $js = [
        'js/pages/report_builder.js'
    ];

    public $depends = [
        AppAsset::class,
        HandlebarsAsset::class,
        SortableAsset::class,
        AxiosAsset::class,
        DatePickerAsset::class
    ];
}
