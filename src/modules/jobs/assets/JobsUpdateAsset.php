<?php

namespace app\modules\jobs\assets;

use app\assets\AppAsset;
use app\assets\AtJsAsset;
use app\assets\AutosizeAsset;
use app\assets\AxiosAsset;
use app\assets\GoogleChartsAsset;
use app\assets\HandlebarsAsset;
use app\assets\HighchartsAsset;
use app\assets\JqueryFileUploadAsset;
use app\assets\MentionAsset;
use app\assets\MomentJsAsset;
use yii\web\AssetBundle;

class JobsUpdateAsset extends AssetBundle
{
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';

    public $css = [
        'css/pages/jobs.css'
    ];

    public $js = [
        'js/pages/jobs_update.js'
    ];

    public $depends = [
        AppAsset::class,
        MomentJsAsset::class,
        HandlebarsAsset::class,
        JqueryFileUploadAsset::class,
        AtJsAsset::class,
        AutosizeAsset::class,
        AxiosAsset::class,
        HighchartsAsset::class
    ];
}
