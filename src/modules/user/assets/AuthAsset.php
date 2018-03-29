<?php
namespace app\modules\user\assets;

use app\assets\AppAsset;
use yii\web\AssetBundle;

class AuthAsset extends AssetBundle
{
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';

    public $css = [
        'css/pages/auth.css'
    ];

    public $depends = [
        AppAsset::class
    ];
}
