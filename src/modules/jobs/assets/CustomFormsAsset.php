<?php

namespace app\modules\jobs\assets;

use app\assets\AppAsset;
use app\assets\HandlebarsAsset;
use app\assets\SortableAsset;
use yii\jui\JuiAsset;
use yii\web\AssetBundle;

class CustomFormsAsset extends AssetBundle
{
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';

    public $css = [
        'css/pages/custom_forms.css'
    ];

    public $js = [
        'js/pages/custom_forms.js'
    ];

    public $depends = [
        AppAsset::class,
        HandlebarsAsset::class,
        SortableAsset::class
    ];
}
