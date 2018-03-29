<?php

namespace app\modules\jobs\assets;

use app\assets\AppAsset;
use app\assets\HandlebarsAsset;
use yii\web\AssetBundle;

class RequestTypeAsset extends AssetBundle
{
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';

    public $css = [
    ];

    public $js = [
        'js/pages/request_types.js'
    ];

    public $depends = [
        AppAsset::class,
        HandlebarsAsset::class,
    ];
}
