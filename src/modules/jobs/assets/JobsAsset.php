<?php

namespace app\modules\jobs\assets;

use app\assets\AppAsset;
use app\assets\AutosizeAsset;
use app\assets\AxiosAsset;
use app\assets\HandlebarsAsset;
use app\assets\JqueryScrollbarAsset;
use app\assets\MomentJsAsset;
use app\assets\DatePickerAsset;
use app\assets\MCustomScrollbarAsset;
use yii\web\AssetBundle;

class JobsAsset extends AssetBundle
{
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';

    public $css = [
        'css/pages/jobs.css'
    ];

    public $js = [
        'js/pages/jobs.js',
        'js/pages/jobs_search.js',
        'js/pages/create_job.js'
    ];

    public $depends = [
        AppAsset::class,
        MomentJsAsset::class,
        HandlebarsAsset::class,
        DatePickerAsset::class,
        MCustomScrollbarAsset::class,
        AxiosAsset::class,
        AutosizeAsset::class
    ];
}
