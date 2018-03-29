<?php

namespace app\modules\user\assets;

use app\assets\AppAsset;
use app\assets\AxiosAsset;
use yii\web\AssetBundle;

class UserUpdateAsset extends AssetBundle
{
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';

    public $js = [
        'js/pages/user_update.js'
    ];

    public $depends = [
        AppAsset::class,
        AxiosAsset::class
    ];
}
