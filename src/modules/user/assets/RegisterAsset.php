<?php

namespace app\modules\user\assets;

use app\assets\AxiosAsset;
use yii\web\AssetBundle;

class RegisterAsset extends AssetBundle
{
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';

    public $js = [
        'js/pages/register.js'
    ];

    public $depends = [
        AuthAsset::class,
        AxiosAsset::class
    ];
}
